<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestHotel extends Model
{
    /**
     * Which table this is attached to
     *
     * @var string
     */
    protected $table = 'hotel_proposal_request';

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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps()
            ->withPivot([
                'contact_initiated_at',
                'hash',
            ]);
    }
}
