<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'address',
        'city',
        'state',
        'zip',
        'phone',
        'special_requests',
        'notes_to_hotel',
        'notes_internal',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function reservations()
    {
        return $this->belongsToMany(Reservation::class)->withPivot([
            'is_primary',
            'payment_type',
            'notes_to_hotel',
            'notes_internal',
            'special_requests',
        ]);
    }
}
