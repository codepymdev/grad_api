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
            $attendance = DB::connection($request->school)->table("attendance")
                                ->where([
                                    'classId' => $request->classId,
                                    'year' => $request->year,
                                    'term' => $request->term
                                    ])
                                ->select("date")
                                ->orderBy("created", "DESC")
                                ->groupBy("date")->get();

            return new ResponseJsonResource($attendance);
        } catch (Exception $e) {
            return new ResponseJsonResource( [
                "status" => false,
                "message" => "Oops, there was an error try again!",
                "error" => $e->getMessage(),
            ]);
        }
    }

    public function getAttendance(Request $request){

        $attendanceCollection = collect();
        try {
            $attendance = DB::connection($request->school)->table("attendance")
                            ->where([
                                'classId' => $request->classId,
                                'year' => $request->year,
                                'term' => $request->term,
                                "date" => $request->date
                                ])
                                ->orderBy("created", "DESC")
                                ->get();
            foreach ($attendance as $row) {
                $student = getUser($row->studentId, $request->school);
                $row->student = $student;
                $attendanceCollection[] = $row;
            }

            return new ResponseJsonResource($attendanceCollection);
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
            $studentsList = count($request->attendance);
            if($studentsList == 0){
                return new ResponseJsonResource( [
                    "status" => false,
                    "message" => "You need to select at least one student!",
                ]);
            }else{
                //check if student attendance already exists for this date -- clear all and reenter new
                $count_exist = DB::connection($request->school)->table("attendance")->where(["date" => $request->date, "classId" => $request->classId, "year" => $request->year, "term" => $request->term])->count();
                if($count_exist != 0){
                    DB::connection($request->school)->table("attendance")->where(["date" => $request->date, "classId" => $request->classId, "year" => $request->year, "term" => $request->term])->delete();
                }

                $students = $request->attendance;
                for($i = 0; $i < $studentsList; $i++){
                    DB::connection($request->school)->table("attendance")->insert([
                        "classId" => $request->classId,
                        "studentId" => $students[$i],
                        "year" => $request->year,
                        "term" => $request->term,
                        "date" => $request->date,
                        "status" => "1",
                        "description" => "present",
                    ]);
                }
                $absents = DB::connection($request->school)->table("classstudent")->where(["classId" => $request->classId])->whereNotIn("userId", $students)->get();
                foreach($absents as $absent){
                    DB::connection($request->school)->table("attendance")->insert([
                        "classId" => $request->classId,
                        "studentId" => $absent->userId,
                        "year" => $request->year,
                        "term" => $request->term,
                        "date" => $request->date,
                        "status" => "0",
                        "description" => "absent",
                    ]);
                }

                return new ResponseJsonResource( [
                    "status" => true,
                    "message" => "Attendance created successfully!",
                ]);
            }

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
