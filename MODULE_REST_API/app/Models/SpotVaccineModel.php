<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpotVaccineModel extends Model
{
    use HasFactory;
    protected $table = 'spot_vaccines';
    protected $primaryKey = 'id';
}
