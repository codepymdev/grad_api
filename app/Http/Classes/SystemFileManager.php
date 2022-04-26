<?php
namespace App\Http\Classes;


class SystemFileManager{

    public static function InternalUploader($file, $type, $school){
        $fname = "uploads/".$school ."/". $type ."/" . rand() . '-'. time() . '.' . $file->getClientOriginalExtension();
        $file->move("uploads" . "/". $school .'/' . $type , $fname);
        return $fname;
    }
}
?>
