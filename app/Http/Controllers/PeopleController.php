<?php

namespace App\Http\Controllers;


use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountVerificationMail;
use App\Http\Resources\PeopleResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ResponseJsonResource;

class PeopleController extends Controller
{
    public function getPeople($school, $campus, $r, $type, $per_page, $page){
        $people = collect();
        /**
         * where clause
         */
        $where = ["status" => "2"];
        if($r != "admin") $where['campus'] = $campus;
        if($type == "teaching"){
            $where["type"] = "teaching";
            $users = DB::connection($school)->table("users")->where($where)->orderBy("created", "DESC")->paginate($per_page);
            foreach ($users as $user) {
                $class_teacher = getClassTeacher($user->id, $school);
                $user->class_teacher = $class_teacher;
                $user->class = getClass($class_teacher->classId, $school);
                $people[] = $user;
            }
        }else if($type == "non-teaching"){
            $where["type"] = "non-teaching";
            $users = DB::connection($school)->table("users")->where(["type" => "non-teaching", "status" => "2"])->orderBy("created", "DESC")->paginate($per_page);
            foreach ($users as $user) {
                $role = getRole($user->roleId, $school);
                $user->user_role = $role;
                $people[] = $user;
            }
        }else if($type == "students"){
            $where["type"] = "student";
            $users = DB::connection($school)->table("users")->where($where)->orderBy("created", "DESC")->get();
            foreach ($users as $user) {
                $class_student = getClassStudent($user->id, $school);
                $user->class_student = $class_student;
                $user->class = getClass($class_student->classId, $school);
                $people[] = $user;
            }
        }else if($type == "users"){
            $where["type"] = "admin";
            $users = DB::connection($school)->table("users")->where(["type" => "admin", "status" => "2"])->orderBy("created", "DESC")->paginate($per_page);
            foreach ($users as $user) {
                $people[] = $user;
            }
        }else if($type == "parents"){
            $where["type"] = "parent";
            $users = DB::connection($school)->table("users")->where($where)->orderBy("created", "DESC")->paginate($per_page);
            foreach ($users as $user) {
                $people[] = $user;
            }
        }

        return new PeopleResource(paginate($people, $per_page));
    }


    public function students($classId, $school, $per_page, $page){
        $students = collect();
        $classstudents = DB::connection($school)->table("classstudent")->where(["classId" => $classId])->orderBy("created", "DESC")->get();
        foreach ($classstudents as $classstudent) {
            $class_student = getStudent($classstudent->userId, $school);
            $classstudent->student = $class_student;
            $students[] = $classstudent;
        }

        return new PeopleResource(paginate($students, $per_page));
    }

    public function updateClassStudent(Request $request){
        try {
            DB::connection($request->school)->table("classstudent")->where(['userId' => $request->id])->update(
                [
                    "classId" => $request->classId,
                    "subjects" => $request->subjects,
                ]
            );
            return new ResponseJsonResource(
                [
                    "status" => true,
                    "message" => "Class student updated successfully"
                ]);
        } catch (\Exception $e) {
            return new ResponseJsonResource(
                [
                    "status" => false,
                    "message" => "Oops, there was an error. Please try again later."
                ]);
        }
    }

    public function updateClassTeacher(Request $request){
        try {
            DB::connection($request->school)->table("classteacher")->where(['userId' => $request->id])->update(
                [
                    "classId" => $request->classId,
                    "subjects" => $request->subjects,
                ]
            );

            return new ResponseJsonResource(
                [
                    "status" => true,
                    "message" => "Class teacher updated successfully"
                ]);
        } catch (\Exception $e) {
            return new ResponseJsonResource(
                [
                    "status" => false,
                    "message" => "Oops, there was an error. Please try again later."
                ]);
        }
    }

    public function create(Request $request){
        $data = [];
        if($request->type == "student"){
            $data = $this->_createStudent($request);
        }else if($request->type == "teaching"){
            $data = $this->_createTeaching($request);
        }else if($request->type == "non-teaching"){
            $data = $this->_createNonTeaching($request);
        }else if($request->type == "parent"){
            $data = $this->_createParent($request);
        }else if($request->type == "user"){
            $data = $this->_createUser($request);
        }else{
            $data = [
                "status" => false,
                "message" => "Invalid type",
                "validate" => false,
            ];

        }
        return new ResponseJsonResource($data);
    }

    private function _createStudent($request){
        /**
         * generate reg number
         */
        $reg_no = generateReg($request->first_name, $request->last_name, $request->school);
        /*-----------------------------------------------------------------------------
        # Bcrypt the user's password // NOTE: The First and Last Name
        -------------------------------------------------------------------------------*/
        $password_f = Hash::make(strtolower($request->first_name));
        $password_l = Hash::make(strtolower($request->last_name));


        try {
            $id = DB::connection($request->school)->table("users")->insertGetId(
                [
                    "first_name" => $request->first_name,
                    "last_name" => $request->last_name,
                    "middle_name" => $request->middle_name,
                    "gender" => $request->gender,
                    "password" => $password_f,
                    "l_password" => $password_l,
                    "tel" => $request->tel,
                    "address" => $request->address,
                    "city" => $request->city,
                    "country" => $request->country,
                    "campus" => $request->campus,
                    "reg_no" => $reg_no,
                    "type"=>'student',
                    "status"=>'2',
                    "roleId"=> '2',
                    "email" => randomString(),
                ]
            );

            return [
                "status" => true,
                "message" => "Student created successfully",
                "id" => $id,
            ];
        } catch (Exception $e) {
            return [
                "status" => false,
                "message" => "Error creating student",
                "error" => $e,
            ];
        }
    }

    private function _createTeaching($request){
        try {


            $email = strtolower($request->email);
            $email_token = generateToken(64);
            //check if email already used
            $count = DB::connection($request->school)->table("users")->where(["email" => $email])->count();
            if($count == 0){
                $id = DB::connection($request->school)->table("users")->insertGetId(
                    [
                        "email" => $email,
                        "type" => "teaching",
                        "roleId" => "3",
                        "first_name" => $request->first_name,
                        "last_name" => $request->last_name,
                        "middle_name" => $request->middle_name,
                        "tel" => $request->tel,
                        "gender" => $request->gender,
                        "campus" => $request->campus,
                        "address" => $request->address,
                        "city" => $request->city,
                        "country" => $request->country,
                        "reg_no" => randomString(),
                        "email_token" => $email_token,
                    ]);

                    //notfication
                    Mail::to($request->email)->send(new AccountVerificationMail(
                        [
                            "first_name" => $request->first_name,
                            "last_name" => $request->last_name,
                            "type" => "Teacher",
                            "school_name" => getConfigValue($request->school, "school_name"),
                            "url" => getConfigValue($request->school, "app_url") . '?route=create-account&token=' . $email_token,
                        ]
                    ));

                    return [
                        "status" => true,
                        "message" => "Teacher created successfully",
                        "id" => $id,
                    ];
            }else{
                return [
                    "status" => false,
                    "message" => "Email already used",

                ];
            }
        } catch (Exception $e) {
            return [
                "status" => false,
                "message" => "Error creating teacher",
                "error" => $e,
            ];
        }
    }
    private function _createNonTeaching($request){
        try {

            $email = strtolower($request->email);
            $email_token = generateToken(64);
            //check if email already used
            $count = DB::connection($request->school)->table("users")->where(["email" => $email])->count();
            if($count == 0){
                $id = DB::connection($request->school)->table("users")->insertGetId(
                    [
                        "email" => $email,
                        "type" => "non-teaching",
                        "roleId" => $request->role,
                        "first_name" => $request->first_name,
                        "last_name" => $request->last_name,
                        "middle_name" => $request->middle_name,
                        "tel" => $request->tel,
                        "gender" => $request->gender,
                        "campus" => $request->campus,
                        "address" => $request->address,
                        "city" => $request->city,
                        "country" => $request->country,
                        "reg_no" => randomString(),
                        "email_token" => $email_token,
                    ]);

                    //notfication
                    Mail::to($request->email)->send(new AccountVerificationMail(
                        [
                            "first_name" => $request->first_name,
                            "last_name" => $request->last_name,
                            "type" => "Staff",
                            "school_name" => getConfigValue($request->school, "school_name"),
                            "url" => getConfigValue($request->school, "app_url") . '?route=create-account&token=' . $email_token,
                        ]
                    ));

                    return [
                        "status" => true,
                        "message" => "Staff created successfully",
                        "id" => $id,
                    ];
            }else{
                return [
                    "status" => false,
                    "message" => "Email already used",

                ];
            }
        } catch (Exception $e) {
            return [
                "status" => false,
                "message" => "Error creating teacher",
                "error" => $e,
            ];
        }
    }
    private function _createParent($request){
        try {

            $email = strtolower($request->email);
            $email_token = generateToken(64);
            //check if email already used
            $count = DB::connection($request->school)->table("users")->where(["email" => $email])->count();
            if($count == 0){
                $id = DB::connection($request->school)->table("users")->insertGetId(
                    [
                        "email" => $email,
                        "type" => "parent",
                        "roleId" => "4",
                        "first_name" => $request->first_name,
                        "last_name" => $request->last_name,
                        "middle_name" => $request->middle_name,
                        "tel" => $request->tel,
                        "gender" => $request->gender,
                        "campus" => $request->campus,
                        "address" => $request->address,
                        "city" => $request->city,
                        "country" => $request->country,
                        "reg_no" => randomString(),
                        "email_token" => $email_token,
                    ]);

                    //notfication
                    Mail::to($request->email)->send(new AccountVerificationMail(
                        [
                            "first_name" => $request->first_name,
                            "last_name" => $request->last_name,
                            "type" => "Parent",
                            "school_name" => getConfigValue($request->school, "school_name"),
                            "url" => getConfigValue($request->school, "app_url") . '?route=create-account&token=' . $email_token,
                        ]
                    ));

                    return [
                        "status" => true,
                        "message" => "Parent created successfully",
                        "id" => $id,
                    ];
            }else{
                return [
                    "status" => false,
                    "message" => "Email already used",

                ];
            }
        } catch (Exception $e) {
            return [
                "status" => false,
                "message" => "Error creating teacher",
                "error" => $e,
            ];
        }
    }

    private function _createUser($request){
        $validator = Validator::make($request->all(), [
            "email" => "required|email|unique:users,email",
            "first_name" => "required",
            "last_name" => "required",
            "campus" => "required",
            "gender" => "required",
            "roleId" => "required",
            "type" => "required",
            "school" => "required",
        ]);

        if($validator->fails()){
            return [
                "status" => false,
                "validate" => true,
                "message" => $validator->errors()
            ];
        }

        try {
            $email = strtolower($request->email);
            $email_token = generateToken(64);
            //check if email already used
            $count = DB::connection($request->school)->table("users")->where(["email" => $email])->count();
            if($count == 0){
                $id = DB::connection($request->school)->table("users")->insertGetId(
                    [
                        "email" => $email,
                        "type" => "admin",
                        "roleId" => $request->roleId,
                        "first_name" => $request->first_name,
                        "last_name" => $request->last_name,
                        "middle_name" => $request->middle_name,
                        "tel" => $request->tel,
                        "gender" => $request->gender,
                        "campus" => $request->campus,
                        "address" => $request->address,
                        "city" => $request->city,
                        "country" => $request->country,
                        "reg_no" => randomString(),
                        "email_token" => $email_token
                    ]);

                    //notfication
                    Mail::to($request->email)->send(new AccountVerificationMail(
                        [
                            "first_name" => $request->first_name,
                            "last_name" => $request->last_name,
                            "type" => "User",
                            "school_name" => getConfigValue($request->school, "school_name"),
                            "url" => getConfigValue($request->school, "app_url") . '?route=create-account&token=' . $email_token,
                        ]
                    ));
                    return [
                        "status" => true,
                        "message" => "User created successfully",
                        "id" => $id,
                "validate" => false,
                    ];
            }else{
                return [
                    "status" => false,
                    "message" => "Email already used",
                    "validate" => false,

                ];
            }
        } catch (Exception $e) {
            return [
                "status" => false,
                "message" => "Error creating teacher",
                "error" => $e,
                "validate" => false,
            ];
        }
    }

    /**
     * update user
     */
    public function update(Request $request){
        $data = [];
        if($request->type == "student"){
            $data = $this->_updateStudent($request);
        }else if($request->type == "teaching"){
            $data = $this->_updateTeaching($request);
        }else if($request->type == "non-teaching"){
            $data = $this->_updateNonTeaching($request);
        }else if($request->type == "parent"){
            $data = $this->_updateParent($request);
        }else if($request->type == "user"){
            $data = $this->_updateUser($request);
        }else{
            $data = [
                "status" => false,
                "message" => "Invalid type",
            ];

        }
        return new ResponseJsonResource($data);
    }


    private function _updateStudent($request){

        /*-----------------------------------------------------------------------------
        # Bcrypt the user's password // NOTE: The First and Last Name
        -------------------------------------------------------------------------------*/
        $password_f = Hash::make(strtolower($request->first_name));
        $password_l = Hash::make(strtolower($request->last_name));

        try {
            DB::connection($request->school)->table("users")->where(["id" => $request->id])->update(
                [
                    "first_name" => $request->first_name,
                    "last_name" => $request->last_name,
                    "middle_name" => $request->middle_name,
                    "gender" => $request->gender,
                    "password" => $password_f,
                    "l_password" => $password_l,
                    "tel" => $request->tel,
                    "address" => $request->address,
                    "city" => $request->city,
                    "country" => $request->country,
                    "campus" => $request->campus,
                    "type"=>'student',
                    "status"=>'2',
                    "roleId"=> '2',
                    "modified"=>date("Y-m-d H:i:s"),
                    "email" => randomString(),
                ]
            );

            return [
                "status" => true,
                "message" => "Student updated successfully",
            ];
        } catch (Exception $e) {
            return [
                "status" => false,
                "message" => "Error updating student",
                "error" => $e,
            ];
        }
    }

    private function _updateTeaching($request){
        try {


            DB::connection($request->school)->table("users")->where(["id" => $request->id])->update(
                [
                    "type" => "teaching",
                    "roleId" => "3",
                    "first_name" => $request->first_name,
                    "last_name" => $request->last_name,
                    "middle_name" => $request->middle_name,
                    "tel" => $request->tel,
                    "gender" => $request->gender,
                    "campus" => $request->campus,
                    "address" => $request->address,
                    "city" => $request->city,
                    "country" => $request->country,
                ]);

                return [
                    "status" => true,
                    "message" => "Teacher updated successfully",
                ];
        } catch (Exception $e) {
            return [
                "status" => false,
                "message" => "Error creating teacher",
                "error" => $e,
            ];
        }
    }
    private function _updateNonTeaching($request){
        try {


            DB::connection($request->school)->table("users")->where(["id" => $request->id])->update(
                [
                    "type" => "non-teaching",
                    "roleId" => $request->role,
                    "first_name" => $request->first_name,
                    "last_name" => $request->last_name,
                    "middle_name" => $request->middle_name,
                    "tel" => $request->tel,
                    "gender" => $request->gender,
                    "campus" => $request->campus,
                    "address" => $request->address,
                    "city" => $request->city,
                    "country" => $request->country,
                ]);

                return [
                    "status" => true,
                    "message" => "Staff updated successfully",
                ];
        } catch (Exception $e) {
            return [
                "status" => false,
                "message" => "Error creating teacher",
                "error" => $e,
            ];
        }
    }

    private function _updateParent($request){
        try {

            DB::connection($request->school)->table("users")->where(["id" => $request->id])->update(
                [
                    "first_name" => $request->first_name,
                    "last_name" => $request->last_name,
                    "middle_name" => $request->middle_name,
                    "tel" => $request->tel,
                    "gender" => $request->gender,
                    "campus" => $request->campus,
                    "address" => $request->address,
                    "city" => $request->city,
                    "country" => $request->country,
                    "reg_no" => randomString(),
                ]);

                return [
                    "status" => true,
                    "message" => "Parent updated successfully",
                ];
        } catch (Exception $e) {
            return [
                "status" => false,
                "message" => "Error updating parent",
                "error" => $e,
            ];
        }
    }

    private function _updateUser($request){
        try {

            DB::connection($request->school)->table("users")->where(["id" => $request->id])->update(
                [
                    "roleId" => $request->role,
                    "first_name" => $request->first_name,
                    "last_name" => $request->last_name,
                    "middle_name" => $request->middle_name,
                    "tel" => $request->tel,
                    "gender" => $request->gender,
                    "campus" => $request->campus,
                    "address" => $request->address,
                    "city" => $request->city,
                    "country" => $request->country,
                    "reg_no" => randomString(),
                ]);

                return [
                    "status" => true,
                    "message" => "User updated successfully",
                ];
        } catch (Exception $e) {
            return [
                "status" => false,
                "message" => "Error updating parent",
                "error" => $e,
            ];
        }
    }


    public function delete($school, $id, $type){
        try {
            DB::connection($school)->table("users")->where(["id" => $id])->delete();
            if($type == "student"){
                DB::connection($school)->table("classstudent")->where(["userId" => $id])->delete();
            }else if($type == "teacher"){
                DB::connection($school)->table("classteacher")->where(["userId" => $id])->delete();
            }

            return new ResponseJsonResource(
                [
                    "status" => true,
                    "message" => ucfirst($type) ." deleted successfully",
                ]
            );
        } catch (Exception $e) {
            return new ResponseJsonResource(
                [
                    "status" => false,
                    "message" => "Oops, there was an error. Please try again later."
                ]);
        }
    }

    public function updateStatus($school, $status, $id){
        try {
            DB::connection($school)->table("users")->where(["id" => $id])->update(["status" => $status]);
            return new ResponseJsonResource(
                [
                    "status" => true,
                    "message" => "Status updated successfully",
                ]
            );
        } catch (Exception $e) {
            return new ResponseJsonResource(
                [
                    "status" => false,
                    "message" => "Oops, there was an error. Please try again later."
                ]);
        }
    }
}
