<?php

namespace App\Models;

use App\Traits\HasRoles;
use Laravel\Spark\User as SparkUser;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends SparkUser
{
    use SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'password',
        'is_temp_password',
        'hash',
        'email',
        'phone',
        'position',
        'signature_name',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'authy_id',
        'hash',
        'country_code',
        'phone',
        'card_brand',
        'card_last_four',
        'card_country',
        'billing_address',
        'billing_address_line_2',
        'billing_city',
        'billing_zip',
        'billing_country',
        'extra_billing_information',
    ];

    /**
     * What should be cast as a Carbon instance
     *
     * @var array
     */
    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'trial_ends_at' => 'date',
        'uses_two_factor_auth' => 'boolean',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function changeOrdersAccepted()
    {
        return $this->hasMany(ChangeOrder::class, 'accepted_by_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function changeOrdersDeclined()
    {
        return $this->hasMany(ChangeOrder::class, 'declined_by_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function changeOrdersInitiated()
    {
        return $this->hasMany(ChangeOrder::class, 'initiated_by_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function contractsReceived()
    {
        return $this->morphedByMany(Contract::class, 'received', 'request_recipients')
            ->withPivot('hash');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contractsAcceptedForLicensee()
    {
        return $this->hasMany(Contract::class, 'accepted_by_owner_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contractsAcceptedForHotel()
    {
        return $this->hasMany(Contract::class, 'accepted_by_hotel_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function hotels()
    {
        return $this->belongsToMany(Hotel::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function licenseesWithContact()
    {
        return $this->belongsToMany(Licensee::class, 'licensee_contacts')
            ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function proposals()
    {
        return $this->morphedByMany(Proposal::class, 'received', 'request_recipients')
            ->withPivot('hash');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function proposalsDeclined()
    {
        return $this->hasMany(ProposalDateRange::class, 'declined_by_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function proposalsSubmitted()
    {
        return $this->hasMany(ProposalDateRange::class, 'submitted_by_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function proposalRequestActions()
    {
        return $this->hasMany(ProposalRequestAction::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function proposalRequestsAuthored()
    {
        return $this->hasMany(User::class, 'created_by_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function proposalRequestsManaging()
    {
        return $this->morphedByMany(ProposalRequest::class, 'received', 'request_recipients')
            ->withPivot('hash');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     *
     */
    public function proposalRequestsReceiving()
    {
        return $this->hasManyThrough(ProposalRequest::class, RequestHotel::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requestHotels()
    {
        return $this->belongsToMany(RequestHotel::class)
            ->withTimestamps()
            ->withPivot([
                'contact_initiated_at',
                'hash',
            ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requestNotes()
    {
        return $this->hasMany(RequestNote::class, 'author_id');
    }

    /**
     * @return $this
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class)
            ->withTimestamps()
            ->withPivot(['rolable_id', 'rolable_type']);
    }

    /**
     * Get the user's first name.
     *
     * @return string
     */
    public function getFirstNameAttribute()
    {
        return substr($this->name, 0, strpos($this->name, ' '));
    }
}
