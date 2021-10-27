<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

use App\Models\Person;
use App\Models\User;
use App\Models\Insurer;
use App\Models\SalesChannel;
use App\Models\Policy;

use App\Helpers\MyMath;

class PoliciesTableSeeder extends Seeder
{
    public function run()
    {
        $day = 3600; // n seconds in 1 day

        $currencies = Config::get('constants.policyCurrencies');

        $timestampFinal = strtotime(now());
        $timestampCursor = strtotime("-6 month"); //strtotime("-10 year");

        while ($timestampCursor < $timestampFinal) {

            // select random sales channel
            $salesChannel = SalesChannel::inRandomOrder()->first();

            $insurer = Insurer::find($salesChannel->insurer_id);
            $agent = User::find($salesChannel->sales_agent_id);

            // *PENDING* policyholder is either new client or existing client
            $policyholder = Person::factory()->create(['data_owned_by' => $insurer->id]);

            // create policy
            $inceptionDate = date("Y-m-d", $timestampCursor + rand(0,7) * $day);
            $nMonths = rand(1,4) *3;
            $expirationDate = date('Y-m-d', strtotime($inceptionDate . "+ $nMonths month"));

            $minPremium = 300;
            $grossPremium = $minPremium + (rand(0,1000)**2)/100;

            $limitAmount = $grossPremium * rand(500,2000);
            $limitAmount = MyMath::roundup($limitAmount);

            $excess = (rand(5,50)/10) * $grossPremium;
            $excess = MyMath::roundup($excess);

            // *REFACTOR* define the broker's commission rate in `sales_channels` table
            $brokerCommissionRate = rand(5,20)/100; // 5% to 20%

            Policy::create([
                'policy_no' => rand(1000000, 9999999), // *PENDING* each insurer should have its own format
                // 'status' => ,
                'inception_date' => $inceptionDate,
                'expiration_date' => $expirationDate ,
                'insurer_id' => $insurer->id,
                'policyholder_id' => $policyholder->id,
                'currency' => 'USD', // *REFACTOR* $insurer->currency,
                'gross_premium' => $grossPremium,
                'brokerage_deduction' => $grossPremium * $brokerCommissionRate,
                'excess' => $excess,
                'limit_amount' => $limitAmount,
                'layer_type' => rand(1,3),
                'sales_channel_id' => $salesChannel->id,
                'created_at' => date("Y-m-d H:i:s", $timestampCursor),
                'updated_at' => null                
            ]);

            // increment time
            $timestampCursor += rand(3600, 36000); // rand(1,3600);
        }
    }
}
