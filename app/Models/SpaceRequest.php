<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpaceRequest extends Model
{
    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'event_date_range_id',
        'proposal_request_id',
        'date_requested',
        'start_time',
        'end_time',
        'type',
        'name',
        'attendees',
        'budget',
        'budget_units',
        'room_type',
        'layout',
        'requests',
        'equipment',
        'meal',
        'notes',
    ];

    /**
     * What should be parsed as a Carbon instance
     *
     * @var array
     */
    protected $dates = [
        'date_requested',
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
