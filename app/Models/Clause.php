<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clause extends Model
{
    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'body',
        'licensee_id',
        'title',
        'is_default',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function licensee()
    {
        return $this->belongsTo(Licensee::class);
    }
}
