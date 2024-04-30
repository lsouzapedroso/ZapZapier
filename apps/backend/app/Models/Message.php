<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'day_time',
        'message',
        'media',
    ];

    public function groups()
    {
        return $this->belongsToMany(WppconnectGroups::class, 'group_message');
    }
}
