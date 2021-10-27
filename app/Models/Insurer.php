<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurer extends Model
{
    use HasFactory;

    // company size
    const SIZE_1_9 = 1; // 1 to 9 employees
    const SIZE_10_49 = 2; // 10 to 49 employees
    const SIZE_50_99 = 3;
    const SIZE_100_249 = 4;
    const SIZE_250_499 = 5;
    const SIZE_500_PLUS = 6;

}
