<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ForgotPasswordResource;
use App\Mail\RecoveryPasswordCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function forgot(Request $request){

        /**
         * status
         */
        $data = _check($request->school);
        if($data['status']){
            $data = $this->forgetPass($request);
            return new ForgotPasswordResource($data);
        }else{
            /**
             * return error
             */
            return new ForgotPasswordResource($data);
        }
    }

    public function verifyAccount(Request $request){
        /**
         * status
         */
        $data = _check($request->school);
        if($data['status']){
            $data = $this->_verify($request);
            return new ForgotPasswordResource($data);
        }else{
            /**
             * return error
             */
            return new ForgotPasswordResource($data);
        }
    }

    public function changePassword(Request $request){
        /**
         * status
         */
        $data = _check($request->school);
        if($data['status']){
            $data = $this->_changePassword($request);
            return new ForgotPasswordResource($data);
        }else{
            /**
             * return error
             */
            return new ForgotPasswordResource($data);
        }
    }

    public function resendRecoveryCode(Request $request){
        /**
         * status
         */
        $data = _check($request->school);
        if($data['status']){
            $data = $this->forgetPass($request);
            return new ForgotPasswordResource($data);
        }else{
            /**
             * return error
             */
            return new ForgotPasswordResource($data);
        }
    }

    private function _changePassword($request){
        $email = trim(strtolower($request->email));
        $school = trim($request->school);
        $password = trim($request->password);
        $confirm_password = trim($request->confirm_password);
        if($school == "" || $email == ""){
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
                $user = DB::connection($school)->table("users")->where(["email" => $email])->first();
                if($password == $confirm_password){
                    //hash password
                    $hash_pass = Hash::make($password);
                    //update
                    DB::connection($school)->table("users")->where(["email" => $email])->update(["password" => $hash_pass, "recovery_code" => null,]);

                    recent_activity($user->id, $school, "You reset your account password", "reset_password", "Reset Password");
                    return [
                        "status" => true,
                        "message" => "success",
                        "school" => $school,
                        "data" => [
                            "id" => $user->id,
                            "school" => $school,
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
                        ],
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

    private function _verify($request){
        $email = trim(strtolower($request->email));
        $school = trim($request->school);
        $code = trim($request->code);
        if($school == "" || $email == "" || $code == ""){
            return [
                "status" => false,
                "message" => "Oops, there was an error. try again!",
            ];
        }else{
            $user = DB::connection($school)->table("users")->where(["email" => $email])->first();
            if($user->recovery_code != ""){
                if($user->recovery_code == $code){

                    recent_activity($user->id, $school, "You successful verify recovery code", "recovery_code", "Verify Recovery Code");

                    return [
                        "status" => true,
                        "message" => "success"
                    ];
                }else{
                    return [
                        "status" => false,
                        "message" => "Recovery code is wrong, please try again"
                    ];
                }
            }else{
                return [
                    "status" => false,
                    "message" => "Oops, there was an error. try again!",
                ];
            }
        }
    }

    private function forgetPass($request){
        $internal = 0;

        $email = trim(strtolower($request->email));
        $school = trim($request->school);
        //check if not empty
        if($email == ""){
            return [
                "status" => false,
                "message" => "Email or Student Id is required",
            ];
        }else{
            //check if email or student id exists

            $us = DB::connection($school)->table("users")->where(["email" => $email])->count();
            $st = DB::connection($school)->table("users")->where(["reg_no" => $email])->count();
            $_us = '0';
            $_st = '0';
            if ( $us > '0' ) { $_us = '1'; $internal = '1'; };
            if ( $st > '0' ) { $_st = '1';  $internal = '1'; $email = strtoupper( $email ); };

            if ($internal != "0") {

                if ( $_us == '1' ) $user = DB::connection($school)->table("users")->where(["email" => $email])->first();

                /**
                 * if student, alert them to contact their head teacher to recover their password
                 */
                if ( $_st == '1' ) {
                    return [
                        "status" => false,
                        "message" => "Contact your head teacher to recover your password!",
                    ];
                }else{
                    $code = generateDigit(6);
                    //update user table
                    DB::connection($school)
                                            ->table("users")
                                            ->where(["id" => $user->id])
                                            ->update(["recovery_code" => $code]);
                    //mail to user
                    try {
                        $_data = [
                            "first_name" => $user->first_name,
                            "last_name" => $user->last_name,
                            "email" => $user->email,
                            "code" => $code,
                        ];

                        Mail::to($user->email)->send(new RecoveryPasswordCode($_data));
                        return [
                            "status" => true,
                            "message" => "success",
                        ];
                    } catch (Exception $e) {
                        return [
                            "status" => false,
                            "message" => $e->getMessage(),
                        ];
                    }
                }
            }else{
                return [
                    "status" => false,
                    "message" => "Oops, your email or student Id not found!",
                ];
            }
        }

    }
}
