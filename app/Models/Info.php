<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Info extends Model
{

    protected $table = 'info_table';
    protected $primaryKey = 'infoID';

    // 3. Allow Livewire to insert data into these columns later
    protected $guarded = []; 
    
    
    // public $timestamps = false; 
}