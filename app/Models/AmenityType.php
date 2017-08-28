<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmenityType extends Model
{
    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
    ];
}
