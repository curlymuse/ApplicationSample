<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractTerm extends Model
{
    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'contract_term_group_id',
        'title',
        'description',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(ContractTermGroup::class);
    }
}
