<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMessage extends Model
{
    use HasFactory;

    protected $table = 'group_message';

    protected $fillable = [
        'group_id',
        'message_id',
        'service_id',
        'send',
    ];
}
