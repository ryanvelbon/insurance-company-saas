<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesChannel extends Model
{
    use HasFactory;

    public $timestamps = false;

    // do not change the ordering
    const TYPE_BROKER = 1;
    const TYPE_TII = 2;
    const TYPE_PRICE_AGGREGATOR = 3;
    const TYPE_DIRECT = 4;
}
