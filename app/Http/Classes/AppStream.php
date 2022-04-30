<?php
namespace App\Http\Classes;

use GetStream\StreamChat\Client;

class AppStream{
    public static function generateToken($userId, $name, $image){
        $server_client = new Client("26weyexcgwar", "fxkqvs7ubah253tzq7ypueqwfj8qww8ynf64jkqugcaqkbgvhgd2x9p2hx3sjg4a");
        $server_client->upsertUsers([
            [
                "id" => $userId,
                "name" => $name,
                "role" => "user",
                "image" => $image,
            ],
        ]);
        $token = $server_client->createToken($userId);
        return $token;
    }
}
?>
