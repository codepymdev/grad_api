<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ResponseJsonResource;

class AttendanceController extends Controller
{
    public function get(Request $request){

        try {
            $attendance = DB::connection($request->school)->table("attendance")->where(['classId' => $request->classId, 'studentId' => $request->studentId, 'year' => $request->year, 'term' => $request->term])->get();
            return new ResponseJsonResource($attendance);
        } catch (Exception $e) {
            return new ResponseJsonResource( [
                "status" => false,
                "message" => "Oops, there was an error try again!",
                "error" => $e->getMessage(),
            ]);
        }
    }

    public function create(Request $request){
        try {
            DB::connection($request->school)->table("attendance")->insert([
                "classId" => $request->classId,
                "studentId" => $request->studentId,
                "year" => $request->year,
                "term" => $request->term,
                "date" => $request->date,
                "description" => $request->description,
                "holiday" => $request->holiday,
                "status" => $request->status,
            ]);
            return new ResponseJsonResource( [
                "status" => true,
                "message" => "Attendance created successfully!",
            ]);
        } catch (Exception $e) {
            return new ResponseJsonResource( [
                "status" => false,
                "message" => "Oops, there was an error try again!",
                "error" => $e->getMessage(),
            ]);
        }
    }

    public function delete($school, $id){
        try {
           DB::connection($school)->table("attendance")->where(['id' => $id])->delete();
              return new ResponseJsonResource( [
                 "status" => true,
                 "message" => "Attendance deleted successfully!",
                ]);
        } catch (Exception $e) {
            return new ResponseJsonResource( [
                "status" => false,
                "message" => "Oops, there was an error try again!",
                "error" => $e->getMessage(),
            ]);
        }
    }
}
