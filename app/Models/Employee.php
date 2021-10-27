<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    const ROLE_EXECUTIVE = 1;
    const ROLE_CLAIMS_ADJUSTER = 2;
    const ROLE_ACTUARY = 3;
    const ROLE_SALES_AGENT = 4;
}
