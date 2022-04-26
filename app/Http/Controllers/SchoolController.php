<?php

namespace App\Http\Controllers;

use App\Http\Resources\SchoolResource;
use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{

    /**
     *
     * get all schools
     */
    public function all(){
        return new SchoolResource(School::all());
    }

    /**
     * get active schools
     */
    public function active(){
        $schools = School::where(["status" => "1"])->get();
        return new SchoolResource($schools);
    }

    /**
     * get inactive schools
     */
    public function inactive(){
        $schools = School::where(["status" => "0"])->get();
        return new SchoolResource($schools);
    }

    public function get($slug){

        $school = School::where(["slug" => $slug])->first()->toArray();
        return new SchoolResource($school);
    }
}
