<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
      // \App\Models\User::factory(10)->create();

      $this->call([

        CountriesTableSeeder::class,
        IndustriesTableSeeder::class,
        InsuranceTypesTableSeeder::class,
        PerilsTableSeeder::class,

        InsurersTableSeeder::class,
        EmployeesTableSeeder::class,
        SalesChannelsTableSeeder::class,

        // UsersTableSeeder::class, // no need

        // PersonsTableSeeder::class, // no need
        
        PoliciesTableSeeder::class,
        ClaimsTableSeeder::class,
        // ClaimStagesTableSeeder::class
      ]);
    }
}
