<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\Industry;
use App\Models\Person;
use App\Models\NaturalPerson;
use App\Models\JuridicalPerson;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Config;


class PersonFactory extends Factory
{
    protected $model = Person::class;

    /*
     * Generates a unique website that no other JuridicalPerson has.
     */
    private function randomUniqueWebsite() {

        $isUnique = false;

        while(!$isUnique) {
            $website = explode("@", $this->faker->companyEmail())[1];

            $isUnique = (JuridicalPerson::where('website', $website)->exists()) ? false : true;
        }

        return $website;
    }

    private function createNaturalPerson($person) {

        // adds a 5% chance of person being a foreigner/expat
        $country = (rand(1,20) > 19)
                        ? Country::inRandomOrder()->first()
                        : Country::findOrFail($person->resident_in);

        NaturalPerson::create([
            'person_id' => $person->id,
            'passport_no' => $country->iso.rand(10000000,99999999), // *REVISE*
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'nationality' => $country->id,
            'gender' => rand(0,1),
            'dob' => date('Y-m-d', rand(strtotime("-80 year"), strtotime("-18 year")))
        ]);
    }

    private function createJuridicalPerson($person) {

        $website = $this->randomUniqueWebsite();

        $person->email = ['hello', 'admin', 'info'][rand(0,2)]  . '@' . $website;
        $person->save();

        JuridicalPerson::create([
            'person_id' => $person->id,
            'name' => ucfirst(explode(".", $website)[0]) . " " . $this->faker->companySuffix(),
            'description' => $this->faker->text($maxNbChars=100),
            'website' => $website,
            'industry_id' => Industry::inRandomOrder()->first()->id,
            // 'size' => ,
            'founded' => date('Y') - rand(0,40)
        ]);
    }

    public function configure()
    {
        return $this->afterCreating(function (Person $person) {
            if ($person->type == Person::TYPE_NATURAL) {
                $this->createNaturalPerson($person);
            } elseif ($person->type == Person::TYPE_JURIDICAL) {
                $this->createJuridicalPerson($person);
            } else {
                // echo "Whoops! something went wrong!";
            }
        });
    }

    /**
     * 'data_owned_by' must be passed in as an argument like so:
     * Person::factory()->create(['data_owned_by' => $insurer->id])
     */
    public function definition()
    {
        $isos = Config::get('constants.targetMarketCountries');
        $iso = $isos[array_rand($isos)];
        $country = Country::where('iso', $iso)->first();

        return [
            'type' => rand(1,2),
            'email' => $this->faker->email(),
            'resident_in' => $country->id,
            'phone1' => '+'.$country->phonecode.rand(0,9999999999), // *REVISE* find DRY solution. Use code from InsurerFactory.php
        ];
    }
}
