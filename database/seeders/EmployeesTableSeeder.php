<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

use App\Models\Insurer;
use App\Models\User;
use App\Models\Employee;

class EmployeesTableSeeder extends Seeder
{
    public function run()
    {
        $insurers = Insurer::all();

        foreach ($insurers as $insurer) {

            // register CEO as an employee
            Employee::create([
                'insurer_id' => $insurer->id,
                'user_id' => $insurer->owner_id,
                'role' => Employee::ROLE_EXECUTIVE,
                'created_by' => $insurer->owner_id // REVISE: this 'employees' record is actually created by the system
            ]);

            // create n accounts and register them as employees
            $n = rand(3,10);
            $users = User::factory()->count($n)->create();
            foreach ($users as $user) {
                $user->email = strtolower($user->first_name[0].$user->last_name."@").$insurer->website;
                $user->save();

                Employee::create([
                    'insurer_id' => $insurer->id,
                    'user_id' => $user->id,
                    'role' => rand(1,4),
                    'created_by' => $insurer->owner_id
                ]);
            }
        }
    }
}