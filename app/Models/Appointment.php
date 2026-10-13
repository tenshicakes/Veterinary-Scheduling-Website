<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointment_table';

    protected $primaryKey = 'appointmentID';

    protected $fillable = [
        'userID',
        'infoID',
        'serviceID',
        'appointmentdate',
        'appointmenttime',
        'status',
        'notes',
    ];
}
