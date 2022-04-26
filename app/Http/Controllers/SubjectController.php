<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ResponseJsonResource;
use App\Http\Resources\ResponseCollectionResource;

class SubjectController extends Controller
{
    public function get($school, $campus, $r, $per_page, $page){
        if($r == "admin"){
            $subjects = DB::connection($school)->table("subject")->orderBy("created", "desc")->paginate($per_page);
        }else{
            $subjects = DB::connection($school)->table("subject")->where(["campusId" => $campus])->orderBy("created", "desc")->paginate($per_page);
        }
        return new ResponseCollectionResource(paginate($subjects));
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            "name" => "required",
        ]);

        if($validator->fails()){
            return [
                "status" => false,
                "validate" => true,
                "message" => $validator->errors()
            ];
        }

        try {
            $id = DB::connection($request->school)->table("subject")->insertGetId([
                "name" => $request->name,
                "description" => $request->description
            ]);
            return new ResponseJsonResource(
                [
                    "status" => true,
                    "message" => "Subject created successfully",
                    "validate" => false,

                ]
            );
        } catch (Exception $e) {
            return new ResponseJsonResource([
                "error" => $e->getMessage(),
                "status" => false,
                "message" => "Failed to create subject",
                "validate" => false,
            ]);
        }
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            "name" => "required",
        ]);

        if($validator->fails()){
            return [
                "status" => false,
                "validate" => true,
                "message" => $validator->errors()
            ];
        }
        try {
            DB::connection($request->school)->table("subject")->where(["id" => $request->id])->update([
                "name" => $request->name,
                "description" => $request->description
            ]);
            return new ResponseJsonResource(
                [
                    "status" => true,
                    "message" => "Subject updated successfully",
                    "validate" => false,
                ]
            );
        } catch (Exception $e) {
            return new ResponseJsonResource([
                "error" => $e->getMessage(),
                "status" => false,
                "message" => "Failed to update subject",
                "validate" => false,
            ]);
        }
    }

    public function delete($school, $id){
        try {
            DB::connection($school)->table("subject")->where(["id" => $id])->delete();
            return new ResponseJsonResource(
                [
                    "status" => true,
                    "message" => "Subject deleted successfully",
                ]
            );
        } catch (Exception $e) {
            return new ResponseJsonResource([
                "error" => $e->getMessage(),
                "status" => false,
                "message" => "Failed to delete subject"
            ]);
        }
    }

    public function subjectCat($school){
        $subjects = DB::connection($school)->table("subjectcategories")->get();
        return new ResponseJsonResource($subjects);
    }
}
