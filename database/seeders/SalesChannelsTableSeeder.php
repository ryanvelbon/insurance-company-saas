<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Employee;
use App\Models\SalesChannel;
use App\Models\Insurer;
use App\Models\User;

class SalesChannelsTableSeeder extends Seeder
{
    /*
     * Sets up channels for already existing inhouse salespersons
     */
    private function createDirectSalesChannels()
    {
        $agents = Employee::where('role', Employee::ROLE_SALES_AGENT)->get();

        foreach ($agents as $agent) {
            SalesChannel::create([
                'insurer_id' => $agent->insurer_id,
                'sales_agent_id' => $agent->user_id,
                'type' => SalesChannel::TYPE_DIRECT
            ]);
        }
    }

    /*
     * Creates dummy users who will act as external sales channels.
     *
     * A TII is authorised to sell insurance policies on behalf of one company.
     * Brokers and Price Aggregators can serve as a sales channel to 1 or more companies.
     */
    private function createExternalSalesChannels()
    {
        $agents = User::factory(100)->create();

        foreach ($agents as $agent) {

            $type = rand(1,3); // TYPE_DIRECT is excluded

            $nInsurers = ($type == SalesChannel::TYPE_TII) ? 1 : rand(1,5);
            $insurers = Insurer::inRandomOrder()->take($nInsurers)->get();
            foreach ($insurers as $insurer) {
                SalesChannel::create([
                    'insurer_id' => $insurer->id,
                    'sales_agent_id' => $agent->id,
                    'type' => $type
                ]);
            }
        }
    }

    public function run()
    {
        $this->createDirectSalesChannels();
        $this->createExternalSalesChannels();
    }
}
