<?php

namespace App\Models;

use App\Presenters\EventLocationPresenter;
use App\Presenters\PresentableInterface;
use App\Traits\Presentable;
use Illuminate\Database\Eloquent\Model;

class EventLocation extends Model implements PresentableInterface
{
    use Presentable;

    /**
     * @var string
     */
    protected $presenter = EventLocationPresenter::class;

    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'place_id',
        'latitude',
        'longitude',
        'formatted_address',
        'street_number',
        'route',
        'locality',
        'administrative_area_level_1',
        'administrative_area_level_2',
        'postal_code',
        'postal_code_suffix',
        'country',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function proposalRequests()
    {
        return $this->belongsToMany(ProposalRequest::class);
    }

}
