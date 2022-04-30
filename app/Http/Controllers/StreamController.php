<?php

namespace App\Http\Controllers;

use App\Http\Classes\AppStream;
use Illuminate\Support\Facades\DB;

class StreamController extends Controller
{
    public function stream($school){
        //get all users
        $users = DB::connection($school)->table('users')->whereNull('token')->get();
        foreach ($users as $user) {
            DB::connection($school)->table("users")->where('id', $user->id)->update(['token' => AppStream::generateToken($user->first_name ."_".$user->id, $user->first_name .' '. $user->last_name , $user->avatar)]);
        }
        $_users = DB::connection($school)->table('users')->get();
        return response()->json($_users);
    }
}
