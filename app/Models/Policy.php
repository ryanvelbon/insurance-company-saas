<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    use HasFactory;

    // *REVISE* technically upcoming, active and expired are calculable. Perhaps display these in a View table?
    const STATUS_UPCOMING = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_EXPIRED = 3;
    const STATUS_LAPSED = 4;
    const STATUS_CANCELLED = 5;

    const LAYER_TYPE_STAND_ALONE = 1;
    const LAYER_TYPE_EXCESS = 2;
    const LAYER_TYPE_PRIMARY = 3;
}
