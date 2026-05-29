<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    // Save data only to these specific columns
    protected $fillable = ['name', 'shape', 'color'];
}