<?php

namespace App\Models;

use App\Presenters\ChangeOrderPresenter;
use App\Traits\Presentable;
use Illuminate\Database\Eloquent\Model;

class ChangeOrder extends Model
{
    use Presentable;

    /**
     * The presenter
     *
     * @var string
     */
    protected $presenter = ChangeOrderPresenter::class;

    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'contract_id',
        'parent_id',
        'initiated_by_party',
        'initiated_by_user',
        'declined_by_user',
        'declined_at',
        'declined_because',
        'accepted_by_user',
        'accepted_at',
        'change_key',
        'change_display',
        'original_value',
        'proposed_value',
        'change_type',
        'reason',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'declined_at',
        'accepted_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function acceptedByUser()
    {
        return $this->belongsTo(User::class, 'accepted_by_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(ChangeOrder::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function declinedByUser()
    {
        return $this->belongsTo(User::class, 'declined_by_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function initiatedByUser()
    {
        return $this->belongsTo(User::class, 'initiated_by_user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(ChangeOrder::class, 'parent_id');
    }
}
