<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    const STATUS_FILED = 1;
    const STATUS_UNDER_REVIEW = 2;
    const STATUS_APPROVED = 3;
    const STATUS_DECLINED = 4;
    const STATUS_DISPUTED = 5;
    const STATUS_WITHDRAWN = 6;
    const STATUS_PAID = 7;

    const FILED_VIA_PHONE = 1;
    const FILED_VIA_EMAIL = 2;
    const FILED_VIA_APP = 3;
    const FILED_VIA_FAX = 4;
}
