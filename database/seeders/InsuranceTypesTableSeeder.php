<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsuranceTypesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('insurance_types')->delete();

        $sql = file_get_contents(dirname(__DIR__, 1) . "/sql/insurance_types.sql");
        DB::unprepared($sql);
    }
}