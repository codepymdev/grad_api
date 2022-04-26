<?php

namespace App\Http\Controllers;

use App\Http\Resources\AnnouncementResource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\AnnouncementsResource;

class AnnouncementController extends Controller
{
    public function get($school, $per_page, $page){
        $announcements = DB::connection($school)->table("announcements")->orderBy("pinned", "DESC")->paginate($per_page);
        return new AnnouncementsResource($announcements);
    }

    private function _handler($arr){
        return new AnnouncementResource($arr);
    }
    public function current($school){
        $count = DB::connection($school)->table("announcements")->where("pinned", "1")->count();
        if($count != 0){
            $announcement = DB::connection($school)->table("announcements")->where("pinned", "1")->first();
            return $this->_handler([
                "id" => $announcement->id,
                "type" => $announcement->type,
                "message" => $announcement->message,

            ]);
        }else{
            return response()->json(null);
        }

    }

    public function store(Request $request){
        try {

            $lastId = DB::connection($request->school)->table("announcements")->insertGetId([
                "type" => $request->type,
                "message" => $request->message,
                "pinned" => "1",
            ]);
            if($lastId){
                //remove others from pin
                DB::connection($request->school)->table("announcements")->whereNotIn("id", [$lastId])->update(["pinned" => "0"]);
            }
            return $this->_handler([
                "status" => true,
                "message" => "Announcement created successfully"
            ]);
        } catch (Exception $e) {
            return $this->_handler(["error" => $e->getMessage(), "status" => false, "message" => "Failed to create announcement"]);
        }
    }

    public function update(Request $request){
        try {
            DB::connection($request->school)->table("announcements")->where("id", $request->id)->update([
                "type" => $request->type,
                "message" => $request->message,
            ]);
            return $this->_handler([
                "message" => "Announcement updated successfully",
                "status" => true,
            ]);
        } catch (Exception $e) {
            return $this->_handler([
                "message" => "Announcement update failed",
                "status" => false,
            ]);
        }
    }

    public function delete($school, $id){
        try {
            DB::connection($school)->table("announcements")->where("id", $id)->delete();
            return $this->_handler(["status"=> true , "message" => "Announcement deleted successfully"]);
        } catch (Exception $e) {
            return $this->_handler(["status"=> false , "message" => "Announcement not deleted"]);
        }

    }

    public function pin(Request $request){
        try {
            DB::connection($request->school)->table("announcements")->where("id", $request->id)->update([
                "pinned" => "1",
            ]);
            DB::connection($request->school)->table("announcements")->whereNotIn("id", [$request->id])->update([
                "pinned" => "0",
            ]);
            return $this->_handler([
                "message" => "Announcement pinned successfully",
                "status" => true,
            ]);
        } catch (Exception $e) {
            return $this->_handler([
                "message" => "Announcement pin failed",
                "status" => false,
            ]);
        }
    }

    public function unpin(Request $request){
        try {
            DB::connection($request->school)->table("announcements")->where("id", $request->id)->update([
                "pinned" => "0",
            ]);
            return $this->_handler([
                "message" => "Announcement unpinned successfully",
                "status" => true,
            ]);
        } catch (Exception $e) {
            return $this->_handler([
                "message" => "Announcement unpin failed",
                "status" => false,
            ]);
        }
    }

}
