<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'service_table';
    protected $primaryKey = 'serviceID';
    protected $guarded = [];
}
