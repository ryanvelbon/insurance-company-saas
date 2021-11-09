<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker;

use App\Models\Claim;
use App\Models\Policy;
use App\Models\Person;
use App\Models\Peril;

class ClaimsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker\Factory::create();

        $freq = 10; // 1 in every 10 policies

        // select random policies for which claim will be filed
        $policies = Policy::inRandomOrder()->take((int) (Policy::count()/$freq))->get();

        foreach ($policies as $policy) {

            $lossDate = date("Y-m-d",
                rand(
                    strtotime($policy->inception_date),
                    strtotime($policy->expiration_date)));

            $nDaysPassed = (int)(((rand(1,1000)**3)/(1000**3))*365);
            $reportingDate = date("Y-m-d", strtotime($lossDate . "+ $nDaysPassed day"));

            $damagedParty = (rand(1,10)>1) 
                                ? null
                                : Person::factory()->create(['data_owned_by' => $policy->insurer_id]);

            Claim::create([
                'policy_id' => $policy->id,
                'claimant_id' => $policy->policyholder_id,
                'damaged_party_id' => $damagedParty ? $damagedParty->id : null,
                // 'status' => ,
                'loss_date' => $lossDate,
                'reporting_date' => $reportingDate,
                'peril_id' => Peril::where('insurance_type_id', 2)->inRandomOrder()->first()->id,
                'description' => $faker->paragraph($nbSentences=rand(1,20)),
                'filed_via' => rand(1,3),
                'created_at' => $reportingDate
            ]);
        }
    }
}
