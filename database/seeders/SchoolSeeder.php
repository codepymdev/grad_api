<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //fkka
        School::create([
            "name" => "First Kingdom kids Academy",
            "slug" => "fkka",
            "status" => '0',
            'logo' => "",
        ]);
        //kings
        School::create([
            "name" => "Kings International School",
            "slug" => "kings",
            "status" => '0',
            'logo' => "",
        ]);
        //golden
        School::create([
            "name" => "Golden Gilead Academcy",
            "slug" => "golden",
            "status" => '0',
        ]);
        School::create([
            "name" => "Victory Kiddes Academcy",
            "slug" => "victory",
            "status" => '0',
            'logo' => "",
        ]);
    }
}
