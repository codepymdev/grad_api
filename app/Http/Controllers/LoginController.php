<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use App\Http\Classes\AppStream;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\LoginResource;

class LoginController extends Controller
{
    public function login(Request $request){

        /**
         * status
         */
        $data = _check($request->school);
        if($data['status']){
            $data = $this->_login($request);
            return new LoginResource($data);
        }else{
            /**
             * return error
             */
            return new LoginResource($data);
        }
    }

    /**
     * return array
     * @param object $request
     */
    private function _login($request){
        $internal = 0;

        $email = trim(strtolower($request->email));
        $password = trim($request->password);
        $school = $request->school;
        if($email == ""){
            return [
                "status" => false,
                "message" => "Email or Student Id is required",
            ];
        }else if($password == ""){
            return [
                "status" => false,
                "message" => "Password is required",
            ];
        }else{
            $us = DB::connection($school)->table("users")->where(["email" => $email])->count();
            $st = DB::connection($school)->table("users")->where(["reg_no" => $email])->count();
            $_us = '0';
            $_st = '0';

            if ( $us > '0' ) { $_us = '1'; $internal = '1'; };
            if ( $st > '0' ) { $_st = '1';  $internal = '1'; $email = strtoupper( $email ); };

            if ($internal != "0") {
                //set the password
			    if ( $_us == '1' ) $user = DB::connection($school)->table("users")->where(["email" => $email])->first();
			    if ( $_st == '1' ) $user = DB::connection($school)->table("users")->where(["reg_no" => $email])->first();

                if($user->status == '0'){
                    return [
                        "status" => false,
                        "message" => "Authentication Failed! Account not yet activated",
                    ];
                }else if( $user->status == "1"){
                    return [
                        "status" => false,
                        "message" => "Authentication Failed!. This account have been blocked",
                    ];
                }else{
                    if($user->status == "2"){
                        if(Hash::check($password, $user->password) == true || Hash::check($password, $user->l_password) == true){
                            recent_activity($user->id, $school, "Account Login successful", "login", "Account Login");
                            //set one signal token if null
                            $oneSignalToken = $user->oneSignalToken;
                            if($user->oneSignalToken == null){
                                $oneSignalToken = oneSignalToken($user->id);
                                DB::connection($school)->table("users")->where(["id" => $user->id])->update([
                                    "oneSignalToken" => $oneSignalToken
                                ]);
                            }

                            //set stream token if null
                            $streamToken = $user->token;
                            if($user->token == null){
                                $streamToken = AppStream::generateToken($school ."_".$user->id, $user->first_name .' '. $user->last_name , $user->avatar);
                                DB::connection($school)->table("users")->where('id', $user->id)->update(['token' => $streamToken ]);
                            }
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
                                    "token" => $streamToken,
                                    "oneSignalToken" => $oneSignalToken,
                                ],
                            ];
                        }else{
                            return [
                                "status" => false,
                                "message" => "Password is wrong",
                            ];
                        }
                    }else{
                        return [
                            "status" => false,
                            "message" => "Oops, there must be an error try again later",
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
