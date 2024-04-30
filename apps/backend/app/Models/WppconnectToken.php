<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WppconnectToken extends Model
{
    use HasFactory;

    protected $table = 'wppconnect_tokens';

    protected $fillable = [
        'token',
        'session_id',
        'init',
    ];
}
