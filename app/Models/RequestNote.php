<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestNote extends Model
{
    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'author_id',
        'proposal_request_id',
        'body',
    ];

    /**
     * What should be cast as a Carbon instance
     *
     * @var array
     */
    protected $dates = [
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proposalRequest()
    {
        return $this->belongsTo(ProposalRequest::class);
    }
}
