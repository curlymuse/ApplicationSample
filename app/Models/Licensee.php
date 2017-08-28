<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Licensee extends Model
{
    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'company_name',
        'default_currency',
        'default_commission',
        'default_rebate',
        'is_suspended',
        'timezone',
        'receive_daily_recap',
        'fax',
        'phone',
        'country_code',
        'region',
        'city',
        'address',
        'org_type',
        'dba',
        'legal_name',
        'logo',
        'email_banner',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function brandContacts()
    {
        return $this->belongsToMany(User::class, 'licensee_contacts')
            ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clauses()
    {
        return $this->hasMany(Clause::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function logEntries()
    {
        return $this->morphMany(LogEntry::class, 'account');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questionGroups()
    {
        return $this->hasMany(LicenseeQuestionGroup::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function termGroups()
    {
        return $this->hasMany(LicenseeTermGroup::class);
    }
}
