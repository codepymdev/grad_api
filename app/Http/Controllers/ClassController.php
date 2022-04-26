<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Whoops\Handler\JsonResponseHandler;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ResponseJsonResource;
use App\Http\Resources\ResponseCollectionResource;

class ClassController extends Controller
{
    public function get($school, $campus, $r, $per_page, $page){
        if($r == "admin"){
            $classes = DB::connection($school)->table("class")->orderBy("created", "desc")->paginate($per_page);
        }else{
            $classes = DB::connection($school)->table("class")->where(["campusId" => $campus])->orderBy("created", "desc")->paginate($per_page);
        }
        return new ResponseCollectionResource(paginate($classes));
    }

    public function create(Request $request){

        $validator = Validator::make($request->all(), [
            "school" => "required",
            "campusId" => "required",
            "name" => "required",
            "arm" => "required",
            "section" => "required",
            "description" => "required",
        ]);
        if($validator->fails()){
            return [
                "status" => false,
                "validate" => true,
                "message" => $validator->errors()
            ];
        }

        try {
            $id = DB::connection($request->school)->table("class")->insertGetId([
                "name" => $request->name,
                "campusId" => $request->campusId,
                "arm" => $request->arm,
                "section" => $request->section,
                "fee" => $request->fee,
                "other_payment_title" => $request->other_payment_title,
                "amount" => $request->amount,
                "description" => $request->description
            ]);
            return new ResponseJsonResource(
                [
                    "status" => true,
                    "message" => "Class created successfully",
                     "id" => $id,
                     "validate" => false,
                ]
            );
        } catch (Exception $e) {
            return new ResponseJsonResource([
                "error" => $e->getMessage(),
                "status" => false,
                "validate" => false,
                "message" => "Failed to create class"
            ]);
        }
    }

    public function update(Request $request){


        $validator = Validator::make($request->all(), [
            "school" => "required",
            "campusId" => "required",
            "name" => "required",
            "arm" => "required",
            "section" => "required",
            "description" => "required",
        ]);

        if($validator->fails()){
            return [
                "status" => false,
                "validate" => true,
                "message" => $validator->errors()
            ];
        }

        try {
            DB::connection($request->school)->table("class")->where(["id" => $request->id])->update([
                "name" => $request->name,
                "campusId" => $request->campusId,
                "arm" => $request->arm,
                "section" => $request->section,
                "fee" => $request->fee,
                "other_payment_title" => $request->other_payment_title,
                "amount" => $request->amount,
                "description" => $request->description
            ]);
            return new ResponseJsonResource(
                [
                    "status" => true,
                    "message" => "Class updated successfully",
                    "validate" => false,
                ]
            );
        } catch (Exception $e) {
            return new ResponseJsonResource([
                "error" => $e->getMessage(),
                "status" => false,
                "message" => "Failed to update class",
                "validate" => false,
            ]);
        }
    }

    public function delete($school, $id){
        try {
            DB::connection($school)->table("class")->where("id", $id)->delete();
            return new ResponseJsonResource(
                [
                    "status" => true,
                    "message" => "Class deleted successfully",
                ]
            );
        } catch (Exception $e) {
            return new ResponseJsonResource([
                "error" => $e->getMessage(),
                "status" => false,
                "message" => "Failed to delete class"
            ]);
        }
    }

    public function updateSubjects(Request $request){
        try {
            DB::connection($request->school)->table("class")->where("id", $request->id)->update([
                "sub" => $request->subjects,
            ]);

            return new ResponseJsonResource(
                [
                    "status" => true,
                    "message" => "Class subjects updated successfully",
                ]
            );
        } catch (Exception $e) {
            return new ResponseJsonResource([
                "error" => $e->getMessage(),
                "status" => false,
                "message" => "Failed to update class subjects"
            ]);
        }
    }

    public function classCat($school){
        $classes = DB::connection($school)->table("classcategories")->get();
        return new ResponseJsonResource($classes);
    }
}
