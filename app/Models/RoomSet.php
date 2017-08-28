<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomSet extends Model
{
    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'reservation_date',
        'rooms_offered',
        'name',
        'description',
        'rate',
    ];

    /**
     * What should be cast as a Carbon instance
     *
     * @var array
     */
    protected $dates = [
        'reservation_date',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'room_nights');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roomNights()
    {
        return $this->hasMany(RoomNight::class);
    }
}
