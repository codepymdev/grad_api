<?php

namespace App\Http\Controllers;


use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\EventResource;
use App\Http\Resources\EventsResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function events(Request $request, $per_page, $page){
        $evt = collect();
        if($request->type == "upcoming"){

            $firstDayOfMonth = Carbon::parse(Carbon::now()->firstOfMonth())->format("Y-m-d");
            $lastDayOfMonth = Carbon::parse(Carbon::now()->lastOfMonth())->format("Y-m-d");
            $events = DB::connection($request->school)
                        ->table("events")
                        ->where([
                                "term" => $request->term,
                                "year" => $request->year,
                        ])
                        ->whereBetween("start_date", [$firstDayOfMonth, $lastDayOfMonth] )
                        ->orWhereBetween("end_date", [$firstDayOfMonth, $lastDayOfMonth])
                        ->orderBy("created", "DESC")->paginate($per_page);
            foreach ($events as $event) {
                $user = getUser($event->userId, $request->school);
                $event->user = $user;
                $event->_start_date = $firstDayOfMonth;
                $event->_end_state = $lastDayOfMonth;
                $evt[] = $event;
            }
        }else{
            $events = DB::connection($request->school)->table("events")->orderBy("created", "DESC")->paginate($per_page);
            foreach ($events as $event) {
                $user = getUser($event->userId, $request->school);
                $event->user = $user;
                $evt[] = $event;
            }
        }
        return new EventsResource(paginate($evt, $per_page));
    }

    public function store(Request $request){
        return new EventResource($this->_store($request));
    }

    private function handler($arr){
        return new EventResource($arr);
    }

    private function _store($request){
        //valiate image
        $validator = Validator::make($request->all(), [
            "title" => "required",
            "start_date" => "required",
            "end_date" => "required",
            "userId" => "required",
            "term" => "required",
            "year" => "required",
        ]);

        if($validator->fails()){
            return [
                "status" => false,
                "validate" => true,
                "message" => $validator->errors()
            ];
        }

        try {
            DB::connection($request->school)->table("events")->insert([
                "term" => $request->term,
                "year" => $request->year,
                "title" => $request->title,
                "description" => $request->description,
                "url" => $request->url,
                "start_date" => $request->start_date,
                "end_date" => $request->end_date,
                "userId" => $request->userId,
            ]);

            return [
                "status" => true,
                "message" => "success",
                "validate" => false,
            ];
        }catch(Exception $e){
            return [
                "status" => false,
                "message" => "Oops, there was an error. try again!",
                "validate" => false,
                "error" => $e->getMessage(),
            ];
        }
    }

    public function update(Request $request){
        //valiate image
        $validator = Validator::make($request->all(), [
            "title" => "required",
            "start_date" => "required",
            "end_date" => "required",
            "userId" => "required",
            "term" => "required",
            "year" => "required",
            "id" => "required",
            "school" => "required"
        ]);

        if($validator->fails()){
            return $this->handler([
                "status" => false,
                "validate" => true,
                "message" => $validator->errors()
            ]);
        }

        try {
            DB::connection($request->school)->table("events")->where(["id" => $request->id])->update([
                "term" => $request->term,
                "year" => $request->year,
                "title" => $request->title,
                "description" => $request->description,
                "url" => $request->url,
                "start_date" => $request->start_date,
                "end_date" => $request->end_date,
            ]);

            return $this->handler([
                "status" => true,
                "message" => "success",
                "validate" => false,
            ]);
        }catch(Exception $e){
            return $this->handler([
                "status" => false,
                "message" => "Oops, there was an error. try again!",
                "validate" => false,
                "error" => $e->getMessage(),
            ]);
        }
    }


    public function delete($id, $school){
        try {
            DB::connection($school)->table("events")->where(["id" => $id])->delete();
            return $this->handler([
                "status" => true,
                "message" => "success",
                "validate" => false,
            ]);
        } catch (Exception $e) {
            return $this->handler([
                "status" => false,
                "message" => "Oops, there was an error. try again!",
                "validate" => false,
                "error" => $e->getMessage(),
            ]);
        }
    }
}
