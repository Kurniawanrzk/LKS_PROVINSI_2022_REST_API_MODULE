<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaccinationModel extends Model
{
    use HasFactory;
    protected $table = 'vaccinations';
    protected $primaryKey = 'id';
    protected $fillable = [
        "spot_id",
        "date",
        "society_id"
    ];
    public $timestamps = false;
}
