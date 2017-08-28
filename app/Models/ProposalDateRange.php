<?php

namespace App\Models;

use App\Presenters\ProposalDateRangePresenter;
use App\Traits\Presentable;
use Illuminate\Database\Eloquent\Model;

class ProposalDateRange extends Model
{
    use Presentable;

    /**
     * @var string
     */
    protected $presenter = ProposalDateRangePresenter::class;

    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'proposal_id',
        'event_date_range_id',
        'declined_by_user',
        'declined_by_user_type',
        'declined_because',
        'declined_at',
        'submitted_by_user',
        'submitted_at',
        'rooms',
        'meeting_spaces',
        'food_and_beverage_spaces',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'declined_at',
        'submitted_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function eventDateRange()
    {
        return $this->belongsTo(EventDateRange::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userWhoSubmitted()
    {
        return $this->belongsTo(User::class, 'submitted_by_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userWhoDeclined()
    {
        return $this->belongsTo(User::class, 'declined_by_user');
    }
}
