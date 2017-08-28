<?php

namespace App\Models;

use App\Presenters\EventDateRangePresenter;
use App\Presenters\PresentableInterface;
use App\Traits\Presentable;
use Illuminate\Database\Eloquent\Model;

class EventDateRange extends Model implements PresentableInterface
{
    use Presentable;

    /**
     * @var string
     */
    protected $presenter = EventDateRangePresenter::class;

    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'event_id',
        'start_date',
        'end_date',
        'check_in_date',
        'check_out_date',
    ];

    /**
     * What columns to parse as dates
     *
     * @var array
     */
    protected $dates = [
        'start_date',
        'end_date',
        'check_in_date',
        'check_out_date',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(\App\Models\Event::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function proposalDateRanges()
    {
        return $this->hasMany(ProposalDateRange::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function proposals()
    {
        return $this->belongsToMany(Proposal::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roomRequestDates()
    {
        return $this->hasMany(RoomRequestDate::class, 'event_date_range_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function spaceRequests()
    {
        return $this->hasMany(SpaceRequest::class);
    }
}
