<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users_table';

    protected $primaryKey = 'userID';

    protected $fillable = [
        'fullname',
        'email',
        'password',
        'phone_number',
        'address',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
