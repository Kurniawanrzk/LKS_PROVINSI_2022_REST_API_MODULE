<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocModel extends Model
{
    use HasFactory;
    protected $table = "societies";
    protected $primaryKey = "id";
    protected $fillable = [
        "login_tokens"
    ];
    public $timestamps = false;

}
