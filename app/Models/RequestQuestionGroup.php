<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestQuestionGroup extends Model
{
    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'proposal_request_id',
        'name',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proposalRequest()
    {
        return $this->belongsTo(ProposalRequest::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions()
    {
        return $this->hasMany(RequestQuestion::class);
    }
}
