<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelCorrelation extends Model
{
    /**
     * @var string
     */
    protected $table = 'hotel_correlation';

    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'hotel_id',
        'correlation_id',
        'source',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
