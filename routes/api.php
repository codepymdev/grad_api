<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\CounterController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SubjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(["prefix" => "schools"], function(){
    Route::get("/", [SchoolController::class, "all"]);
    Route::get("/active", [SchoolController::class, "active"]);
    Route::get("/inactive", [SchoolController::class, "inactive"]);
    Route::get("get/{slug}", [SchoolController::class, "get"]);
});

Route::group(["prefix" => "auth"], function(){
    Route::post("/login", [LoginController::class, "login"]);
    Route::post("/forgot-password", [ForgotPasswordController::class, "forgot"]);
    Route::post("/verify-account", [ForgotPasswordController::class, "verifyAccount"]);
    Route::post("/change-password", [ForgotPasswordController::class, "changePassword"]);
    Route::post("/resend-recovery-code", [ForgotPasswordController::class, "resendRecoveryCode"]);
});

Route::group(["prefix" => "people"], function(){
    Route::get("/get/{school}/{campus}/{r}/{type}/{per_page}/{page}", [PeopleController::class, "getPeople"]);

    ///class student
    Route::group(["prefix" => "class-students"], function(){
        Route::get("/student/{classId}/{school}/{per_page}/{page}", [PeopleController::class, "students"]);
        Route::post("/update-class-student", [PeopleController::class, "updateClassStudent"]);
    });
    //class teachers
    Route::group(["prefix" => "class-teachers"], function(){
        Route::post("/update-class-teacher", [PeopleController::class, "updateClassTeacher"]);
    });

    //
    Route::post("/create", [PeopleController::class, "create"]);
    Route::post("/update", [PeopleController::class, "update"]);
    Route::delete("delete/{school}/{id}/{type}", [PeopleController::class, "delete"]);

    Route::get("/update-status", [PeopleController::class, "updateStatus"]);
});


Route::group(["prefix" => "counter"], function(){
    Route::get("/{school}/{campus}/{r}", [CounterController::class, "counter"]);
});

Route::group(["prefix" => "account"], function(){
    Route::post("/update-profile", [AccountController::class, "update"]);
    Route::post("/change-password-account", [AccountController::class, "changePassword"]);
    Route::get("/recent-activities/{school}/{userid}/{per_page}/{page}", [AccountController::class, "RecentActivities"]);
});

Route::group(["prefix" => "settings"], function(){
    Route::get("/{school}", [SettingsController::class, "configs"]); //get all configuration
    Route::post("/school-configuration", [SettingsController::class, "schoolConfiguration"]);
    Route::post("/session", [SettingsController::class, "session"]);
    Route::post("/term", [SettingsController::class, "term"]);
    Route::post("/stamp", [SettingsController::class, "stamp"]);
    Route::post("/bug", [SettingsController::class, "bug"]);

    ///campus and roles
    Route::get("/campus/{school}",[SettingsController::class, "campus"]);
    Route::get("/roles/{school}/{type}",[SettingsController::class, "roles"]);
});

Route::group(["prefix" => "events"], function() {
    Route::post("/get/{per_page}/{page}", [EventController::class, "events"]);
    Route::post("/upload-event", [EventController::class, "store"]);
    Route::post("/update", [EventController::class, "update"]);
    Route::delete("/delete/{id}/{school}", [EventController::class, "delete"]);
});

Route::group(["prefix" => "announcement"], function(){
    Route::get("/get/{school}/{per_page}/{page}", [AnnouncementController::class, "get"]);
    Route::get("/current/{school}", [AnnouncementController::class, "current"]);
    Route::post("/upload", [AnnouncementController::class, "store"]);
    Route::post("/update", [AnnouncementController::class, "update"]);
    Route::delete("/delete/{school}/{id}", [AnnouncementController::class, "delete"]);
    Route::post("/pin", [AnnouncementController::class, "pin"]);
    Route::post("/unpin", [AnnouncementController::class, "unpin"]);
});


Route::group(["prefix" => "classes"], function(){
    Route::get("/get/{school}/{campus}/{r}/{per_page}/{page}", [ClassController::class, "get"]);
    Route::post("/create",[ClassController::class, "create"]);
    Route::post("/update", [ClassController::class, "update"]);
    Route::delete("/delete/{school}/{id}", [ClassController::class, "delete"]);
    //update subjects
    Route::post("/update-subjects",[ClassController::class, "updateSubjects"]);
    Route::get("/class-categories/{school}", [ClassController::class, "classCat"]);
});

Route::group(["prefix" => "subjects"], function(){
    Route::get("/get/{school}/{campus}/{r}/{per_page}/{page}", [SubjectController::class, "get"]);
    Route::post("/create", [SubjectController::class, "create"]);
    Route::post("/update", [SubjectController::class, "update"]);
    Route::delete("/delete/{school}/{id}", [SubjectController::class, "delete"]);
    Route::get("/subject-categories/{school}", [SubjectController::class, "subjectCat"]);

});

