<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Insurer;

class InsurersTableSeeder extends Seeder
{
    public function run()
    {
        Insurer::factory(10)->create();
    }
}
