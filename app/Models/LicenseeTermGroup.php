<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseeTermGroup extends Model
{
    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'licensee_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function licensee()
    {
        return $this->belongsTo(Licensee::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function terms()
    {
        return $this->hasMany(LicenseeTerm::class);
    }
}
