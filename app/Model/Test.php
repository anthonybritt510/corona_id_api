<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    //
    protected $fillable = [
        'user_id', 'user_name', 'tester_id', 'tester_name', 'viral_count', 'is_positive', 'is_confirmed', 'testkit_number'
    ];

}
