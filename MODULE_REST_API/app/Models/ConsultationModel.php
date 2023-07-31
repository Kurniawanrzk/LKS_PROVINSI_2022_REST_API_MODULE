<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationModel extends Model
{
    use HasFactory;
    protected $table = "consultations";
    protected $primaryKey = "id";
    protected $fillable = [
        "society_id",
        "doctor_id",
        "status",
        "disease_history",
        "current_symptoms",
        "doctor_notes"
    ];
    public $timestamps = false;
}
