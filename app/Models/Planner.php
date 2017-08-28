<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Planner extends Model
{
    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'place_id',
        'address1',
        'address2',
        'city',
        'state',
        'zip',
        'country',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function proposalRequests()
    {
        return $this->hasMany(ProposalRequest::class);
    }
}
