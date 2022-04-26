<?php

namespace App\Http\Controllers;

use App\Http\Resources\CounterResource;
use Illuminate\Support\Facades\DB;

class CounterController extends Controller
{
    public function counter($school, $campus, $r){

        //students
        if($r == "admin"){
            $students = DB::connection($school)->table("users")->where(["type" => "student", "status" => "2"])->count();
        }else{
            $students = DB::connection($school)->table("users")->where(["campus" => $campus,"type" => "student", "status" => "2"])->count();
        }
        //teaching
        if($r == "admin"){
            $teaching = DB::connection($school)->table("users")->where(["type" => "teaching", "status" => "2"])->count();
        }else{
            $teaching = DB::connection($school)->table("users")->where(["campus" => $campus, "type" => "teaching", "status" => "2"])->count();
        }
        //non teaching
        if($r == "admin"){
            $non_teaching = DB::connection($school)->table("users")->where(["type" => "non-teaching", "status" => "2"])->count();
        }else{
            $non_teaching = DB::connection($school)->table("users")->where(["campus" => $campus, "type" => "non-teaching", "status" => "2"])->count();
        }

        //parents
        if($r == "admin"){
            $parents = DB::connection($school)->table("users")->where(["type" => "parent", "status" => "2"])->count();
        }else{
            $parents = DB::connection($school)->table("users")->where(["campus" => $campus, "type" => "parent", "status" => "2"])->count();
        }

        //class
        if($r == "admin"){
            $class = DB::connection($school)->table("class")->count();
        }else{
            $class = DB::connection($school)->table("class")->where(["campusId" => $campus])->count();
        }

        //subjects
        if($r == "admin"){
            $subjects = DB::connection($school)->table("subject")->count();
        }else{
            $subjects = DB::connection($school)->table("subject")->where(["campusId" => $campus])->count();
        }

        //users
        if($r == "admin"){
            $users = DB::connection($school)->table("users")->where(["type" => "admin", "status" => "2"])->count();
        }else{
            $users = DB::connection($school)->table("users")->where(["campus" => $campus, "type" => "admin", "status" => "2"])->count();
        }

        return new CounterResource([
            "students" => $students,
            "teaching" => $teaching,
            "non_teaching" => $non_teaching,
            "parents" => $parents,
            "class" => $class,
            "subjects" => $subjects,
            "users" => $users,
        ]);
    }
}
