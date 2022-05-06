<?php

use App\Models\School;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * check status
 */
function _check($slug){
/**
 * Check if the school have been activitated
 */
$count = School::where(["slug" => $slug])->count();
if($count != 0){
    $school = School::where(["slug" => $slug])->first();
    if($school->status == 1){
        return [
            "status" => true,
            "message" => "success",
            "validate" => false,
        ];
    }else{
        return [
            "status" => false,
            "message" => "Oops, you are not authorized to login. Contact the support team!",
            "validate" => false,
        ];
    }
}else{
    return [
        "status" => false,
        "message" => "Oops, there was an error, please try again later",
        "validate" => false,
    ];
}
}

/**
 * generate random number
 * @return int
 * @param int length
 */
function generateDigit( $len = 6){
    // Set a blank variable to store the key in
    $key = "";
    for ($x = 1; $x <= $len; $x++) {
        // Set each digit
        $key .= random_int(0, 9);
    }
    return $key;
}



/**
 * store user activity
 * @param String $userId
 * @param String $school
 * @param String description
 * @param String type
 * @param String title
 *
 * @return void
 */
function recent_activity($userId, $school, $description, $type, $title){
    DB::connection($school)->table("activities")->insert([
        "userId" => $userId,
        "description" => $description,
        "type" => $type,
        "title" => $title
    ]);
}

function getClassTeacher($userId, $school){
    //check if found
    $count = DB::connection($school)->table("classteacher")->where(["userId" => $userId])->count();
    if($count == 0) return [];
    return DB::connection($school)->table("classteacher")->where(["userId" => $userId])->first();
}

function getSubjectName($school, $subjectId){
    $subject = DB::connection($school)->table("subject")->where(["id" => $subjectId])->first();
    return $subject->name;
}

function getUser($userId, $school){
    $count = DB::connection($school)->table("users")->where(["id" => $userId])->count();
    if($count == 0) return [];
    return DB::connection($school)->table("users")->where(["id" => $userId])->first();
}

function getClassStudent($userId, $school){
    //check if found
    $count = DB::connection($school)->table("classstudent")->where(["userId" => $userId])->count();
    if($count == 0) return [];
    return DB::connection($school)->table("classstudent")->where(["userId" => $userId])->first();
}

function getStudent($userId, $school){
    $count = DB::connection($school)->table("users")->where(["id" => $userId])->count();
    if($count == 0) return [];
    return DB::connection($school)->table("users")->where(["id" => $userId])->first();
}
function getClass($classId, $school){
    //check if found
    $count = DB::connection($school)->table("class")->where(["id" => $classId])->count();
    if($count == 0) return [];
    return DB::connection($school)->table("class")->where(["id" => $classId])->first();
}

function getRole($roleid, $school){
    return DB::connection($school)->table("roles")->where(["id" => $roleid])->first();
}

function paginate($items, $perPage = 5, $page = null,$baseUrl = null, $options = []){
    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

    $items = $items instanceof Collection ?
                    $items : Collection::make($items);

    $lap = new LengthAwarePaginator($items->forPage($page, $perPage),
                        $items->count(),
                        $perPage, $page, $options);

    if ($baseUrl) {
        $lap->setPath($baseUrl);
    }

    return $lap;
}


function generateReg( $f,$l, $school ){
    $config = DB::connection($school)->table("config")->where(["name" => "reg_no_prefix"])->first();
    $reg = $config->value;
    $sub_first_name = substr($f,0,3);
    $sub_last_name = substr($l,0,2);
    $date = date('Y');
    $random_number = rand(0,99);
    $regNum = strtoupper($reg . $date .'/'. $sub_first_name .'/'.$sub_last_name.$random_number);
    return $regNum;
}


function getConfigValue($school, $name) { //return config value from database
    $config = DB::connection($school)->table("config")->where(["name" => $name ])->first();
	return $config->value;
}

function randomString( $chars=10 ){
    $characters = '0123456789abcdefghijklmnoprstuvwxyz';
    $randstring = '';
    for ($i=0; $i < $chars; $i++) { $randstring .= $characters[rand(0, strlen($characters) - 1)]; }
    return $randstring;
}

function generateToken( $chars=10 ){
    $characters = '0123456789abcdefghijklmnoprstuvwxyz';
    $randstring = '';
    for ($i=0; $i < $chars; $i++) { $randstring .= $characters[rand(0, strlen($characters) - 1)]; }
    return $randstring;
}
?>
