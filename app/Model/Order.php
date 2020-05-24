<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = [
        'user_id', 'product_name', 'quantity', 'card_holder', 'card_number', 'card_expire_date', 'card_cvc',
    ];
}
