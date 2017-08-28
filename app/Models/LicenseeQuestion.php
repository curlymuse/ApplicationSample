<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseeQuestion extends Model
{
    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'licensee_question_group_id',
        'question',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(LicenseeQuestionGroup::class);
    }
}
