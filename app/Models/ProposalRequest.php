<?php

namespace App\Models;

use App\Presenters\ProposalRequestPresenter;
use App\Traits\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProposalRequest extends Model
{
    use Presentable, SoftDeletes;

    /**
     * @var string
     */
    protected $presenter = ProposalRequestPresenter::class;

    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'created_by_user',
        'event_id',
        'client_id',
        'planner_id',
        'cutoff_date',
        'is_visible_to_planner',
        'is_visible_to_client',
        'is_attrition_acceptable',
        'is_meeting_space_required',
        'is_food_and_beverage_required',
        'occupancy_per_room_typical',
        'occupancy_per_room_max',
        'room_nights_consumed_per_comp_request',
        'description',
        'antipicated_attendance',
        'commission',
        'rebate',
        'currency',
        'specificity',
    ];

    /**
     * List of columns to be cast as Carbon dates
     *
     * @var array
     */
    protected $dates = [
        'cutoff_date',
        'deleted_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function actions()
    {
        return $this->hasMany(ProposalRequestAction::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'created_by_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function eventLocations()
    {
        return $this->belongsToMany(EventLocation::class)
            ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function hotels()
    {
        return $this->belongsToMany(Hotel::class)
            ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notes()
    {
        return $this->hasMany(RequestNote::class);
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
    public function planner()
    {
        return $this->belongsTo(Planner::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requestHotels()
    {
        return $this->hasMany(RequestHotel::class);
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
    public function questionGroups()
    {
        return $this->hasMany(RequestQuestionGroup::class);
    }

    /**
     * @return $this
     */
    public function usersManaging()
    {
        return $this->morphToMany(User::class, 'received', 'request_recipients')
            ->withPivot('hash');
    }

}
