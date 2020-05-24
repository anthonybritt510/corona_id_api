<?php

namespace App\Model;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
        'first_name',
        'last_name',
        'gender',
        'birthday',
        'document_number',
        'document_type',
        'citizenship_country',
        'expiration_date',
        'issuing_authority',
        'document_photo',
        'user_photo',
        'professional_license_photo',
        'is_professional',
        'is_activated',
        'email',
        'order_contact_name',
        'order_email',
        'order_phone_number',
        'order_address_1',
        'order_address_2',
        'card_number',
        'card_holder',
        'card_expire_date',
        'card_cvv',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function username()
    {
        return 'document_number';
    }

    public function getFullname()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

}
