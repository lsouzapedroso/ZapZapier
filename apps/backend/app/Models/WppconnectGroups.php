<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WppconnectGroups extends Model
{
    use HasFactory;

    protected $table = 'wppconnect_groups';

    protected $fillable = [
        'session_id',
        'serialized_id',
    ];
    public function messages()
    {
        return $this->belongsToMany(Message::class, 'group_message');
    }
}
