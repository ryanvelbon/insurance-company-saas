<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NaturalPerson extends Model
{
    use HasFactory;

    protected $table = 'persons_natural';
    protected $primaryKey = 'person_id';
    public $timestamps = false;

    protected $fillable = [
        'person_id',
        'passport_no',
        'first_name',
        'last_name',
        'nationality',
        'gender',
        'dob'
    ];

    CONST GENDER_FEMALE = 0;
    CONST GENDER_MALE = 1;
}
