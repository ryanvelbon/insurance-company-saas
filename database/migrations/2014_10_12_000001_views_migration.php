<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ViewsMigration extends Migration
{
    public function up()
    {

        DB::statement("
            CREATE OR REPLACE VIEW view_employees
            AS
            SELECT
              employees.id,
              employees.user_id,
              users.first_name,
              users.last_name,
              users.email,
              insurers.name AS company_name,
              insurers.website AS company_website,
              countries.iso AS company_country
            FROM employees
            LEFT JOIN users ON employees.user_id = users.id
            LEFT JOIN insurers ON employees.insurer_id = insurers.id
            LEFT JOIN countries ON insurers.country_id = countries.id;
        ");


        DB::statement("
            CREATE OR REPLACE VIEW view_policies
            AS
            SELECT
              policies.id,
              -- IF (policies.expiration_date < NOW(), 'expired', '') AS status, -- REVISE this is wrong
              policies.policy_no,
              insurance_types.title AS category,
              policies.inception_date,
              policies.expiration_date,
              insurers.id AS insurer_id,
              insurers.name AS insurer_name,
              insurers.website AS insurer_website,
              persons.email AS policyholder_email,
              persons.phone1 AS policyholder_phone1,

              policies.layer_type,
              policies.currency,
              policies.gross_premium,
              policies.brokerage_deduction,
              policies.gross_premium - policies.brokerage_deduction AS net_premium,
              policies.excess,
              policies.limit_amount,
              -- sales channel and agent's details
              policies.created_at
            FROM policies
            LEFT JOIN insurers ON policies.insurer_id = insurers.id
            LEFT JOIN persons ON policies.policyholder_id = persons.id
            LEFT JOIN insurance_types ON policies.insurance_type_id = insurance_types.id
        ");


        // *PENDING* calculable columns
        DB::statement("
            CREATE OR REPLACE VIEW view_claims
            AS
            SELECT
              insurers.name AS insurer,
              policies.policy_no,
              policies.inception_date AS policy_inception_date,
              policies.expiration_date AS policy_expiration_date,
              insurance_types.title AS category,
              perils.title AS peril,
              claims.description,
              claims.loss_date,
              claims.reporting_date,
              DATEDIFF(reporting_date, loss_date) AS reported_n_days_after_incident,
              policies.currency,
              0 AS paid,
              0 AS outstanding,
              0 AS total_incurred,
              0 AS fees,
              0 AS recoveries,
              0 AS overall_amount,
              0 AS non_zero,
              0 AS destination_currency,
              0 AS overall_amount_revalued,
              0 AS retained,
              0 AS ceded,
              0 AS exposure
            FROM claims
            LEFT JOIN perils ON perils.id = claims.peril_id
            LEFT JOIN policies ON policies.id = claims.policy_id
            LEFT JOIN insurers ON insurers.id = policies.insurer_id
            LEFT JOIN insurance_types ON insurance_types.id = policies.insurance_type_id
        ");
    }

    public function down()
    {
        Schema::dropIfExists('view_claims');
        Schema::dropIfExists('view_employees');
        Schema::dropIfExists('view_sales_channels');
        Schema::dropIfExists('view_policies');
    }
}
