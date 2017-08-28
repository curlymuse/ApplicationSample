<?php

namespace App\Models;

use App\Presenters\ProposalPresenter;
use App\Traits\Presentable;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use Presentable;

    /**
     * @var string
     */
    protected $presenter = ProposalPresenter::class;

    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'proposal_request_id',
        'hotel_id',
        'commission',
        'rebate',
        'additional_charge_per_adult',
        'tax_rate',
        'min_age_to_check_in',
        'additional_fees',
        'additional_fees_units',
        'honor_bid_until',
        'min_length_of_stay',
        'deposit_policy',
        'attrition_rate',
        'cancellation_policy',
        'cancellation_policy_days',
        'cancellation_policy_file',
        'notes',
        'questions',
    ];

    /**
     * What should be cast as a date
     *
     * @var array
     */
    protected $dates = [
        'declined_at',
        'honor_bid_until',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function dateRanges()
    {
        return $this->hasMany(ProposalDateRange::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proposalRequest()
    {
        return $this->belongsTo(ProposalRequest::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function usersReceived()
    {
        return $this->morphToMany(User::class, 'received', 'request_recipients')
            ->withPivot('hash');
    }
}
