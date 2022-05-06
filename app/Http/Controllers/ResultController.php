<?php

namespace App\Http\Controllers;

use App\Http\Resources\ResponseJsonResource;
use Exception;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
    public function classSubjects($school, $classId){
        $subjects = [];
        try {

            $class = DB::connection($school)->table("class")->where(['id' => $classId])->first();
            $subs = unserialize($class->sub);
            for ($i=0; $i < count($subs); $i++) {
                if($subs[$i] == "null") continue;
                $_subj['name'] = getSubjectName($school, $subs[$i]);
                $_subj['id'] = $subs[$i];
                $subjects[] = $_subj;
            }
            return new ResponseJsonResource($subjects);
        } catch (Exception $e) {
            return new ResponseJsonResource( [
                "status" => false,
                "message" => "Oops, there was an error try again!",
                "error" => $e->getMessage(),
            ]);
        }
    }
}
