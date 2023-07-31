<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalModel extends Model
{
    use HasFactory;
    protected $table = "medicals";
    protected $primaryKey = "id";
    protected $fillable = [
        
    ];
}
