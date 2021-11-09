<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerilsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('perils')->delete();

        $sql = file_get_contents(dirname(__DIR__, 1) . "/sql/perils.sql");
        DB::unprepared($sql);
    }
}
