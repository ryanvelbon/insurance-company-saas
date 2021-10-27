<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JuridicalPerson extends Model
{
    use HasFactory;

    protected $table = 'persons_juridical';
    protected $primaryKey = 'person_id';
    public $timestamps = false;

    protected $fillable = [
        'person_id',
        'name',
        'description',
        'website',
        'industry_id',
        'size',
        'founded',
        'status'
    ];
}
