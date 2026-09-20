<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    // 1. Point to your custom table
    protected $table = 'service_table';
    protected $primaryKey = 'serviceID';

    // 2. Allow mass assignment
    protected $guarded = [];

    
}