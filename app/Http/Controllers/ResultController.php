<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ResponseJsonResource;

class ResultController extends Controller
{
    public function currentResult(Request $request){

        try {
            $count = DB::connection($request->school)->table('result')
            ->where([

                    'classId' => $request->classId,
                    'campusId' => $request->campusId,
                    "term" => $request->term,
                    "year" => $request->year,
                    'userId' => $request->userId,
                ])
            ->count();

            if($count > 0){
                $file = "";
                if($request->school == "fkka"){
                   $file = $this->_fkkaResult($request);
                }else if($request->school == "golden"){

                }else if($request->school == "victory"){
                }else if($request->school == "kings"){

                }
                return new ResponseJsonResource( [
                    "status" => true,
                    "message" => "Result downloading...",
                    "file" => $file,
                ]);
            }else{
                return new ResponseJsonResource( [
                    "status" => false,
                    "message" => "No result found for this student!",
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


    private function _fkkaResult($request){
        $data = [];
        //get results
        $result = DB::connection($request->school)->table('result')
        ->where([

                'classId' => $request->classId,
                'campusId' => $request->campusId,
                "term" => $request->term,
                "year" => $request->year,
                'userId' => $request->userId,
        ])
        ->get()
        ->toArray();

        //get grades
        $grade = DB::connection($request->school)->table('grade')
        ->where([
            'classId' => $request->classId,
            'campusId' => $request->campusId,
            "term" => $request->term,
            "year" => $request->year,
            'userId' => $request->userId,
        ])
        ->first();

        //get student info
        $student = DB::connection($request->school)->table('users')
            ->where(
                [
                    "id" => $request->userId
                ])
                ->first();
        //get class info
        $class = DB::connection($request->school)->table("class")
                    ->where(
                        [
                            "id" => $request->classId
                        ]
                        )
                    ->first();
        //get attendance
        $count_present = DB::connection($request->school)->table("attendance")
                    ->where([
                        "classId" => $request->classId,
                        "term" => $request->term,
                        "year" => $request->year,
                        'studentId' => $request->userId,
                        "status" => "1",
                    ])
                    ->count();
        //get absent
        $count_absent = DB::connection($request->school)->table("attendance")
        ->where([
            "classId" => $request->classId,
            "term" => $request->term,
            "year" => $request->year,
            'studentId' => $request->userId,
            "status" => "0",
        ])
        ->count();
        //get ratings
        $ratings = DB::connection($request->school)->table("ratings")
            ->where([
                'classId' => $request->classId,
                'campusId' => $request->campusId,
                "term" => $request->term,
                "year" => $request->year,
                'userId' => $request->userId,
            ])
            ->first();
        //get campus
        $campus = DB::connection($request->school)->table("campus")

                ->where([
                    "id" => $request->campusId
                ])
                ->first();
        //class students
        $no_of_students = DB::connection($request->school)->table("classstudent")
            ->where(
                    [
                        'classId' => $request->classId
                    ])
            ->count();
        $data = [
            "data" => [
                "result" => $result,
                "grade" => $grade,
                "student" => $student,
                "class" => $class,
                "attendance" => [
                                    "total" => $count_present + $count_absent,
                                    "present" => $count_present,
                                    "absent" => $count_absent,
                ],
                "rating" => $ratings,
                "campus" => $campus,
                "no_of_students" => $no_of_students,
            ],
            "school" => $request->school,
        ];

        // return response()->json($data);

        if ($data) {
            $pdf = PDF::loadView('results.fkka.index', $data);
            $pdf->setPaper('A4', 'portrait');

            $fileName = $student->first_name."_". $student->last_name .'_result-'. date('m-d-Y-His').'.pdf';
            Storage::disk("public")->put('results/fkka/'.$fileName, $pdf->output());
            return "uploads/results/fkka/" . $fileName;
        }
    }

    public function downloadResult($file){
        $headers = array(
            'Content-Type: application/pdf',
          );

        return response()->download($file, "result.pdf", $headers);
    }



    public function getClasses($school, $campus, $r){
        if($r == "admin"){
            $classes = DB::connection($school)->table("class")->orderBy("created", "desc")->get();
        }else{
            $classes = DB::connection($school)->table("class")->where(["campusId" => $campus])->orderBy("created", "desc")->get();
        }
        return new ResponseJsonResource($classes);
    }

}
