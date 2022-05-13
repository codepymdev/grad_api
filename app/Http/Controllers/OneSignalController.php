<?php

namespace App\Http\Controllers;

class OneSignalController extends Controller
{
    public function send(){
        return response()->json(sendSignalNotification("Message", "Heading"));
    }
}
