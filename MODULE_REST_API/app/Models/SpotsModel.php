<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpotsModel extends Model
{
    use HasFactory;
    protected $table = "spots";
    protected $primaryKey = "id";
}
