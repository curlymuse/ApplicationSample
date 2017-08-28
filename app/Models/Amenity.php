<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    /**
     * @var string
     */
    protected $table = 'amenities';

    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'amenity_type_id',
        'name',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(AmenityType::class, 'amenity_type_id');
    }
}
