<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndustriesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('industries')->delete();

        $sql = file_get_contents(dirname(__DIR__, 1) . "/sql/industries.sql");
        DB::unprepared($sql);
    }
}
