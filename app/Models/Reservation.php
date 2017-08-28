<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'guest_name',
        'guest_address',
        'guest_city',
        'guest_state',
        'guest_country',
        'guest_zip',
        'guest_phone',
        'guest_special_requests',
        'guest_notes_to_hotel',
        'guest_notes_internal',
        'confirmation_number',
        'cancellation_number',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function guests()
    {
        return $this->belongsToMany(Guest::class)->withPivot([
            'is_primary',
            'payment_type',
            'notes_to_hotel',
            'notes_internal',
            'special_requests',
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roomNights()
    {
        return $this->hasMany(RoomNight::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roomSets()
    {
        return $this->belongsToMany(RoomSet::class, 'room_nights');
    }
}
