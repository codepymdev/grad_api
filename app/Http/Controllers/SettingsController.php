<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Classes\SystemFileManager;
use App\Http\Resources\SettingsResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ResponseJsonResource;

class SettingsController extends Controller
{
    public function configs($school){
        $config = DB::connection($school)->table("config")->get();
        return new SettingsResource($config);
    }

    public function schoolConfiguration(Request $request){
        try {
            DB::connection($request->school)->table("config")->where(['name' => "school_name"])->update(["value" => $request->school_name]);
            DB::connection($request->school)->table("config")->where(['name' => "school_moto"])->update(["value" => $request->school_moto]);
            DB::connection($request->school)->table("config")->where(['name' => "school_details"])->update(["value" => $request->school_details]);
            DB::connection($request->school)->table("config")->where(['name' => "school_address"])->update(["value" => $request->school_address]);
            DB::connection($request->school)->table("config")->where(['name' => "school_email1"])->update(["value" => $request->school_email1]);
            DB::connection($request->school)->table("config")->where(['name' => "school_email2"])->update(["value" => $request->school_email2]);
            DB::connection($request->school)->table("config")->where(['name' => "school_email3"])->update(["value" => $request->school_email3]);
            DB::connection($request->school)->table("config")->where(['name' => "school_tel1"])->update(["value" => $request->school_tel1]);
            DB::connection($request->school)->table("config")->where(['name' => "school_tel2"])->update(["value" => $request->school_tel2]);
            DB::connection($request->school)->table("config")->where(['name' => "school_tel3"])->update(["value" => $request->school_tel3]);
            DB::connection($request->school)->table("config")->where(['name' => "reg_no_prefix"])->update(["value" => $request->reg_no_prefix]);



            return [
                "status" => true,
                "validate" => false,
                "message" => "success",
            ];
        } catch (Exception $e) {
            return [
                "status" => false,
                "validate" => false,
                "message" => "Oops, there was an error try again!",
                "error" => $e->getMessage(),
            ];
        }
    }
    public function session(Request $request){
        try {
            DB::connection($request->school)->table("config")->where(['name' => "term"])->update(["value" => $request->term]);
            DB::connection($request->school)->table("config")->where(['name' => "year"])->update(["value" => $request->year]);

            return [
                "status" => true,
                "validate" => false,
                "message" => "success",
            ];
        } catch (Exception $e) {
            return [
                "status" => false,
                "validate" => false,
                "message" => "Oops, there was an error try again!",
                "error" => $e->getMessage(),
            ];
        }
    }

    public function term(Request $request){
        try {

            DB::connection($request->school)->table("config")->where(['name' => "next_term_begins"])->update(["value" => $request->next_term_begins]);
            DB::connection($request->school)->table("config")->where(['name' => "term_begins"])->update(["value" => $request->term_begins]);
            DB::connection($request->school)->table("config")->where(['name' => "term_ends"])->update(["value" => $request->term_ends]);


            return [
                "status" => true,
                "validate" => false,
                "message" => "success",
            ];
        } catch (Exception $e) {
            return [
                "status" => false,
                "validate" => false,
                "message" => "Oops, there was an error try again!",
                "error" => $e->getMessage(),
            ];
        }
    }

    public function stamp(Request $request){
        $validator = Validator::make($request->all(), [
            "image" => "required|mimes:png,jpg,jpeg|max:2048",
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
                $upload = SystemFileManager::InternalUploader($request->file('image'), "stamp", $request->school);
                $image = "https://grad.fkkas.com/" . $upload;
                DB::connection($request->school)->table("config")->where(["name" => "school_signature"])->update(["value" => $image]);
                return [
                    "status" => true,
                    "validate" => false,
                    "message" => "success",
                ];
            }
        } catch (Exception $e) {
            return [
                "status" => false,
                "validate" => false,
                "message" => "Oops, there was an error try again!",
            ];
        }
    }


    public function bug(Request $request){
        try {
            DB::connection("grad")->table("bugs")->insert([
                "type" => $request->name,
                "school" => $request->school,
                "note" => $request->note
            ]);
            return [
                "status" => true,
                "message" => "success",
                "validate" => false,
            ];
        } catch (Exception $e) {
            return [
                "status" => false,
                "validate" => false,
                "message" => "Oops, there was an error try again!",
            ];
        }
    }

    public function campus($school){
        $campus = DB::connection($school)->table("campus")->get();
        return new ResponseJsonResource($campus);
    }

    public function roles($school, $type){
        $roles = DB::connection($school)->table("roles")->where(["type" => $type])->get();
        return new ResponseJsonResource($roles);
    }
}
