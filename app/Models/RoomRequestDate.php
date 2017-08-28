<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomRequestDate extends Model
{
    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'event_date_range_id',
        'proposal_request_id',
        'room_date',
        'rooms_requested',
        'room_type_name',
        'preferred_rate_min',
        'preferred_rate_max',
    ];

    /**
     * Which columns to parse as dates
     *
     * @var array
     */
    protected $dates = [
        'room_date',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dateRange()
    {
        return $this->belongsTo(EventDateRange::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proposalRequest()
    {
        return $this->belongsTo(ProposalRequest::class);
    }
}
