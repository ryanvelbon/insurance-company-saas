<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;

use App\Models\Claim;

class MasterMigration extends Migration
{
    public function up()
    {
        // Static Data ------------------------------------------------------------

        Schema::create('countries', function (Blueprint $table) {
          $table->id();
          $table->char('iso', 2);
          $table->string('name', 80);
          $table->string('nicename', 80);
          $table->char('iso3', 3)->nullable();
          $table->unsignedSmallInteger('numcode')->nullable();
          $table->integer('phonecode');
          $table->unsignedTinyInteger('phone_nsn')->nullable();
        });

        // Schema::create('jurisdictions', function (Blueprint $table) {});

        Schema::create('industries', function (Blueprint $table) {
          $table->id();
          $table->string('title', 50);
        });

        Schema::create('insurance_types', function (Blueprint $table) {
            $table->id();
            $table->string('title', 60)->unique();
        });

        Schema::create('perils', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('insurance_type_id');
            $table->string('title', 120);

            $table->unique(['insurance_type_id', 'title']);
            $table->foreign('insurance_type_id')->references('id')->on('insurance_types');
        });

        // Users-------------------------------------------------------------------

        Schema::create('users', function (Blueprint $table) {
          $table->id();
          $table->string('first_name', 35);
          $table->string('last_name', 35);
          $table->string('email')->unique();
          $table->timestamp('email_verified_at')->nullable();
          $table->string('password');
          $table->rememberToken();
          $table->timestamps();
        });


        // ---------------------------------------------------------------------------

        Schema::create('insurers', function (Blueprint $table) {
          $table->id();
          $table->string('name', 80);
          $table->string('description', 1000)->nullable();
          $table->tinyInteger('size');
          $table->unsignedBigInteger('country_id');
          $table->string('website', 100);
          $table->string('email')->unique();
          $table->string('phone1', 15);
          $table->string('phone2', 15)->nullable();
          $table->unsignedBigInteger('owner_id');
          // $table->tinyInteger('membership');
          $table->timestamps();

          $table->foreign('country_id')->references('id')->on('countries');
          $table->foreign('owner_id')->references('id')->on('users'); // or executives?
        });


        Schema::create('employees', function (Blueprint $table) {
          $table->id();
          $table->unsignedBigInteger('insurer_id');
          $table->unsignedBigInteger('user_id');
          $table->tinyInteger('role');
          $table->timestamps();
          $table->unsignedBigInteger('created_by');

          $table->unique(['insurer_id', 'user_id']);
          $table->foreign('insurer_id')->references('id')->on('insurers');
          $table->foreign('user_id')->references('id')->on('users');
          $table->foreign('created_by')->references('id')->on('users'); // the executive who created this entry
          $table->softDeletes();
        });


        Schema::create('sales_channels', function (Blueprint $table) {
          $table->id();
          $table->unsignedBigInteger('insurer_id');
          $table->unsignedBigInteger('sales_agent_id');
          $table->tinyInteger('type');
          $table->unsignedBigInteger('created_by')->nullable();

          $table->unique(['insurer_id', 'sales_agent_id']);
          $table->foreign('insurer_id')->references('id')->on('insurers');
          $table->foreign('sales_agent_id')->references('id')->on('users');
          $table->foreign('created_by')->references('id')->on('users'); // created by an executive of the insurance company
        });

        // Clients & Damaged Parties?  -------------------------------------------------------------------

        Schema::create('persons', function (Blueprint $table) {
          $table->id();
          $table->tinyInteger('type'); // natural or juridical
          $table->string('email')->unique();
          $table->string('phone1', 15);
          $table->string('phone2', 15)->nullable();
          $table->unsignedBigInteger('resident_in');
          $table->unsignedBigInteger('data_owned_by');
          // $table->unsignedBigInteger('created_by');
          $table->timestamps();

          $table->unique(['data_owned_by', 'email']);
          $table->unique(['data_owned_by', 'phone1']);
          $table->foreign('resident_in')->references('id')->on('countries');
          $table->foreign('data_owned_by')->references('id')->on('insurers');
          // $table->foreign('created_by')->references('id')->on('users');
        });


        Schema::create('persons_natural', function (Blueprint $table) {
          $table->unsignedBigInteger('person_id')->unique();
          $table->string('passport_no', 12)->unique();
          $table->string('first_name', 40);
          $table->string('last_name', 40);
          $table->unsignedBigInteger('nationality');
          $table->tinyInteger('gender');
          $table->date('dob');

          $table->foreign('person_id')->references('id')->on('persons');
          $table->foreign('nationality')->references('id')->on('countries');
        });


        Schema::create('persons_juridical', function (Blueprint $table) {
          $table->unsignedBigInteger('person_id')->unique();
          $table->string('name', 80);
          $table->string('description', 1000)->nullable();
          $table->string('website', 100)->nullable();
          $table->unsignedBigInteger('industry_id');
          $table->tinyInteger('size')->nullable(); // define dictionary '1-9', '10-49', '50-99', '100-249', '250-499', '500+'
          $table->year('founded')->nullable();
          // $table->tinyInteger('status'); // define dictionary 'active', 'inactive', 'dissolved'

          $table->foreign('person_id')->references('id')->on('persons');
          $table->foreign('industry_id')->references('id')->on('industries');
        });


        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->string('policy_no', 20);
            $table->unsignedBigInteger('insurance_type_id');
            // $table->unsignedTinyInteger('status');
            $table->date('inception_date');
            $table->date('expiration_date');
            // $table->unsignedBigInteger('territory_id');
            $table->unsignedBigInteger('insurer_id');
            $table->unsignedBigInteger('policyholder_id');
            $table->unsignedTinyInteger('layer_type');
            $table->enum('currency', Config::get('constants.policyCurrencies'));
            $table->unsignedInteger('gross_premium'); // *REVISE* data type
            $table->unsignedInteger('brokerage_deduction'); // *REVISE* data type
            $table->unsignedInteger('excess'); // *REVISE* data type
            $table->unsignedInteger('limit_amount'); // *REVISE* data type
            $table->unsignedBigInteger('sales_channel_id');
            $table->timestamps();

            $table->unique(['policy_no', 'insurer_id']);
            $table->foreign('insurance_type_id')->references('id')->on('insurance_types');
            // $table->foreign('territory_id')->references('id')->on('countries');
            $table->foreign('insurer_id')->references('id')->on('insurers');
            $table->foreign('policyholder_id')->references('id')->on('persons');
            $table->foreign('sales_channel_id')->references('id')->on('sales_channels');
        });


        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('policy_id');
            $table->unsignedBigInteger('claimant_id');
            $table->unsignedBigInteger('damaged_party_id')->nullable();
            $table->tinyInteger('status')->default(Claim::STATUS_FILED);
            $table->date('loss_date');
            $table->date('reporting_date');
            $table->unsignedBigInteger('peril_id');
            $table->string('description', 5000);
            $table->tinyInteger('filed_via');
            $table->timestamps();

            $table->foreign('policy_id')->references('id')->on('policies');
            $table->foreign('claimant_id')->references('id')->on('persons');
            $table->foreign('damaged_party_id')->references('id')->on('persons');
            $table->foreign('peril_id')->references('id')->on('perils');
        });


        // Schema::create('', function (Blueprint $table) {});
        // Schema::create('', function (Blueprint $table) {});
        // Schema::create('', function (Blueprint $table) {});
    }

    public function down()
    {
        Schema::dropIfExists('claims');
        Schema::dropIfExists('policies');
        Schema::dropIfExists('persons_juridical');
        Schema::dropIfExists('persons_natural');
        Schema::dropIfExists('persons');
        Schema::dropIfExists('sales_channels');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('insurers');
        Schema::dropIfExists('users');
        Schema::dropIfExists('perils');
        Schema::dropIfExists('insurance_types');
        Schema::dropIfExists('industries');
        Schema::dropIfExists('countries');
    }
}
