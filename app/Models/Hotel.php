<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    /**
     * What can be mass-assigned
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'address1',
        'address2',
        'property_type_id',
        'city',
        'zip',
        'state',
        'country',
        'brand_id',
        'description',
        'sleeping_rooms',
        'meeting_rooms',
        'largest_meeting_room_sq_ft',
        'total_meeting_room_sq_ft',
        'rate_min',
        'rate_max',
        'travelocity_stars',
        'travelocity_rating',
        'travelocity_reviews',
        'latitude',
        'longitude',
        'sleeping_rooms',
        'floors',
        'year_built',
        'year_of_last_renovation',
        'property_phone',
        'property_fax',
        'property_email',
        'mobil_star_rating',
        'place_id',
        'google_stars',
        'google_latitude',
        'google_longitude',
        'google_updated_at',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'google_updated_at',
    ];

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class)
            ->withPivot([
                'attribute_id',
                'amenity_count',
            ])
            ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function hoteliers()
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(HotelImage::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function logEntries()
    {
        return $this->morphMany(LogEntry::class, 'account');
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class, 'property_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function proposalRequests()
    {
        return $this->belongsToMany(ProposalRequest::class)
            ->withTimestamps();
    }
}
