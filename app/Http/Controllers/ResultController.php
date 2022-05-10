<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ResultController extends Controller
{
    public function currentResult(Request $request){

        $count = DB::connection($request->school)->table('result')
        // ->where([
        //     [
        //         'classId' => $request->classId,
        //         'campusId' => $request->campusId,
        //         "term" => $request->term,
        //         "year" => $request->year,
        //         'userId' => $request->userId,
        //     ],
        // ])
        ->count();

        if($count > 0){
            if($request->school == "fkka"){
                $this->_fkkaResult($request);
            }else if($request->school == "golden"){

            }else if($request->school == "victory"){
            }else if($request->school == "kings"){

            }
        }
    }




    private function _fkkaResult($request){
            $data = DB::connection($request->school)->table('result')
            // ->where([
            //     [
            //         'classId' => $request->classId,
            //         'campusId' => $request->campusId,
            //         "term" => $request->term,
            //         "year" => $request->year,
            //         'userId' => $request->userId,
            //     ],
            // ])
            ->limit(5)
            ->get()
            ->toArray();
        if ($data) {
            $pdf = PDF::loadView('results.fkka.index', $data);
            $pdf->setPaper('A4', 'portrait');
            $fileName = 'export-'. date('m-d-Y-His').'.pdf';
            Storage::put('public/uploads/datas/downloads/'.$fileName, $pdf->output());
            return $fileName;
        }
    }
}
