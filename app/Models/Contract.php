<?php

namespace App\Models;

use App\Presenters\ContractPresenter;
use App\Traits\Presentable;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use Presentable;

    /**
     * @var string
     */
    protected $presenter = ContractPresenter::class;

    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'proposal_id',
        'event_date_range_id',
        'is_client_owned',
        'is_offline_contract',
        'client_hash',
        'start_date',
        'end_date',
        'check_in_date',
        'check_out_date',
        'declined_by_owner_at',
        'declined_by_owner_user',
        'declined_by_owner_because',
        'declined_by_hotel_at',
        'declined_by_hotel_user',
        'declined_by_hotel_because',
        'accepted_by_hotel_user',
        'accepted_by_hotel_at',
        'accepted_by_hotel_signature',
        'accepted_by_owner_user',
        'accepted_by_owner_at',
        'accepted_by_owner_signature',
        'is_meeting_space_required',
        'is_food_and_beverage_required',
        'commission',
        'rebate',
        'additional_charge_per_adult',
        'tax_rate',
        'min_age_to_check_in',
        'min_length_of_stay',
        'additional_fees',
        'additional_fees_units',
        'deposit_policy',
        'attrition_rate',
        'cancellation_policy',
        'cancellation_policy_days',
        'cancellation_policy_file',
        'notes',
        'attachments',
        'questions',
        'meeting_spaces',
        'food_and_beverage',
        'cutoff_date',
        'snapshot',
    ];

    /**
     * What should be parsed as a Carbon instance
     *
     * @var array
     */
    protected $dates = [
        'declined_by_owner_at',
        'declined_by_hotel_at',
        'accepted_by_hotel_at',
        'accepted_by_owner_at',
        'cutoff_date',
        'start_date',
        'end_date',
        'check_in_date',
        'check_out_date',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'date_ranges'   => 'json',
    ];

    /**
     * @return \Illuminate\Support\Collection
     */
    public function addendums()
    {
        if (!(bool)$this->accepted_by_hotel_at || !(bool)$this->accepted_by_owner_at) {
            return collect([]);
        }

        $cutoffStamp = ($this->accepted_by_hotel_at->gt($this->accepted_by_owner_at))
            ? $this->accepted_by_hotel_at
            : $this->accepted_by_owner_at;

        return $this->changeOrders()
            ->where('created_at', '>', $cutoffStamp)
            ->where('parent_id', null)
            ->orderBy('id', 'desc')
            ->get();
    }

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
    public function changeOrders()
    {
        return $this->hasMany(ChangeOrder::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dateRange()
    {
        return $this->belongsTo(EventDateRange::class, 'event_date_range_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hotelUserWhoAccepted()
    {
        return $this->belongsTo(User::class, 'accepted_by_hotel_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ownerUserWhoAccepted()
    {
        return $this->belongsTo(User::class, 'accepted_by_owner_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hotelUserWhoDeclined()
    {
        return $this->belongsTo(User::class, 'declined_by_hotel_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ownerUserWhoDeclined()
    {
        return $this->belongsTo(User::class, 'declined_by_owner_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function paymentMethods()
    {
        return $this->belongsToMany(PaymentMethod::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function reservationMethods()
    {
        return $this->belongsToMany(ReservationMethod::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roomSets()
    {
        return $this->hasMany(RoomSet::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function termGroups()
    {
        return $this->hasMany(ContractTermGroup::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function usersReceived()
    {
        return $this->morphToMany(User::class, 'received', 'request_recipients');
    }
}
