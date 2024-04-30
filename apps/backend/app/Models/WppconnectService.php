<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WppconnectService extends Model
{
    use HasFactory;

    protected $table = 'wppconnect_service';

    protected $fillable = [
        'service_name',
    ];
}
