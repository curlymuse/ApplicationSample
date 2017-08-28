<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseeTerm extends Model
{
    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'licensee_term_group_id',
        'title',
        'description',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(LicenseeTermGroup::class);
    }
}
