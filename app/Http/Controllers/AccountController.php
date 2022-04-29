<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Classes\SystemFileManager;
use App\Http\Resources\AccountResource;
use App\Http\Resources\RecentActivitiesResource;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function update(Request $request){
        /**
         * status
         */
        $data = _check($request->school);
        if($data['status']){
            $_data = $this->_updateAccount($request);
            return new AccountResource($_data);
        }else{
            return new AccountResource($data);
        }
    }

    public function changePassword(Request $request){
        /**
         * status
         */
        $data = _check($request->school);
        if($data['status']){
            $_data = $this->_changePassword($request);
            return new AccountResource($_data);
        }else{
            return new AccountResource($data);
        }
    }


    private function _changePassword($request){
        $userid = trim(strtolower($request->userid));
        $school = trim($request->school);
        $password = trim($request->password);
        $confirm_password = trim($request->confirm_password);
        if($school == "" || $userid == ""){
            return [
                "status" => false,
                "message" => "Oops, there was an error. try again!",
            ];
        }else{
            if($password == "" || $confirm_password == "" ){
                return [
                    "status" => false,
                    "message" => "Password is required",
                ];
            }else{
                $user = DB::connection($school)->table("users")->where(["id" => $userid])->first();
                if($password == $confirm_password){
                    //hash password
                    $hash_pass = Hash::make($password);
                    //update
                    DB::connection($school)->table("users")->where(["id" => $userid])->update(["password" => $hash_pass]);

                    recent_activity($user->id, $school, "You reset your account password", "reset_password", "Reset Password");
                    return [
                        "status" => true,
                        "message" => "success",
                    ];
                }else{
                    return [
                        "status" => false,
                        "message" => "Password must match",
                    ];
                }
            }
        }
    }

    private function _updateAccount($request){

        //valiate image
        $validator = Validator::make($request->all(), [
            "first_name" => "required",
            "last_name" => "required",
            "gender" => "required",
            "image" => "mimes:png,jpg,jpeg|max:2048",
        ]);
        if($validator->fails()){
            return [
                "status" => false,
                "validate" => true,
                "message" => $validator->errors()
            ];
        }
        try {
            if($request->image != null){
                $upload = SystemFileManager::InternalUploader($request->file('image'), "avatar", $request->school);
                $image = "https://grad.fkkas.com/" . $upload;
            }else{
                $image = "";
            }
            if($request->gender == "Select gender") $gender = ""; else $gender = $request->gender;
            if($request->address) $address = $request->address; else $address = "";
            if($request->tel) $tel = $request->tel; else $tel = "";
            if($request->city) $city = $request->city; else $city = "";
            if($request->country) $country = $request->country; else $country = "";
            if($request->middle_name) $middle_name = $request->middle_name; else $middle_name = "";

            DB::connection($request->school)->table("users")->where(["id" => $request->id])->update(
                [
                    "first_name" => $request->first_name,
                    "last_name" => $request->last_name,
                    "middle_name" => $middle_name,
                    "gender" => $gender,
                    "avatar" => $image,
                    "tel" => $tel,
                    "address" => $address,
                    "city" => $city,
                    "country" => $country
                ]
            );
            $user = DB::connection($request->school)->table("users")->where(["id" => $request->id])->first();
            recent_activity($user->id, $request->school, "You updated your profile details", "update_account", "Update Account");

            return [
                "status" => true,
                "message" => "success",
                "school" => $request->school,
                "validate" => false,
                "data" => [
                    "id" => $user->id,
                    "school" => $request->school,
                    "email" => $user->email,
                    "reg_no" => $user->reg_no,
                    "type" => $user->type,
                    "roleid" => $user->roleId,
                    "first_name" => $user->first_name,
                    "last_name" => $user->last_name,
                    "middle_name" => $user->middle_name,
                    "gender" => $user->gender,
                    "tel" => $user->tel,
                    "avatar" => $user->avatar,
                    "address" => $user->address,
                    "city" => $user->city,
                    "country" => $user->country,
                    "campus" => $user->campus,
                    "status" => $user->status,
                    "rating" => $user->rating,
                    "token" => $user->token,
                ]
            ];
        } catch (Exception $e) {
            return [
                "status" => false,
                "message" => "Oops, there was an error. try again!",
                "validate" => false,
                "error" => $e->getMessage(),
            ];
        }
    }


    public function RecentActivities($school, $userid, $per_page, $page){
        $activities = DB::connection($school)->table("activities")->where(["userId" => $userid])->orderBy("created", "DESC")->paginate($per_page);
        return new RecentActivitiesResource($activities);
    }
}
