<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'has_numeric_entry',
    ];
}