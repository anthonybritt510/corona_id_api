<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    //
    protected $fillable = [
        'user_id',
        'notification_type',
        'notification_date',
        'title',
        'content',
    ];
}
