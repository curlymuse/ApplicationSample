<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestQuestion extends Model
{
    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'request_question_group_id',
        'question',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(RequestQuestionGroup::class, 'request_question_group_id');
    }
}
