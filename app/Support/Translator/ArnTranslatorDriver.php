<?php

namespace App\Support\Translator;

use App\Repositories\Contracts\AmenityRepositoryInterface;
use App\Repositories\Contracts\AmenityTypeRepositoryInterface;
use App\Repositories\Contracts\AttributeRepositoryInterface;
use App\Repositories\Contracts\BrandRepositoryInterface;
use App\Repositories\Contracts\HotelCorrelationRepositoryInterface;
use App\Repositories\Contracts\HotelImageRepositoryInterface;
use App\Repositories\Contracts\HotelRepositoryInterface;
use App\Repositories\Contracts\PropertyTypeRepositoryInterface;
use App\Support\HotelRegistry;
use Illuminate\Support\Collection;

class ArnTranslatorDriver implements TranslatorDriver
{
    /**
     * @const int
     */
    const MAX_LOOPS = null;

    /**
     * @var PropertyDataSet
     */
    private $set;

    /**
     * @var array
     */
    private $correlationTable = [];

    /**
     * @var BrandRepositoryInterface
     */
    private $brandRepo;

    /**
     * @var HotelRepositoryInterface
     */
    private $hotelRepo;

    /**
     * @var HotelCorrelationRepositoryInterface
     */
    private $hotelCorrelationRepo;

    /**
     * @var AmenityTypeRepositoryInterface
     */
    private $amenityTypeRepo;

    /**
     * @var PropertyTypeRepositoryInterface
     */
    private $propertyTypeRepo;

    /**
     * @var AmenityRepositoryInterface
     */
    private $amenityRepo;

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepo;

    /**
     * @var HotelImageRepositoryInterface
     */
    private $imageRepo;

    /**
     * @var HotelRegistry
     */
    private $hotelRegistry;

    /**
     * ArnTranslatorDriver constructor.
     * @param BrandRepositoryInterface $brandRepo
     * @param HotelRepositoryInterface|HotelRepositoryInterface $hotelRepo
     * @param HotelCorrelationRepositoryInterface $hotelCorrelationRepo
     * @param AmenityRepositoryInterface $amenityRepo
     * @param AttributeRepositoryInterface $attributeRepo
     * @param AmenityTypeRepositoryInterface $amenityTypeRepo
     * @param PropertyTypeRepositoryInterface $propertyTypeRepo
     * @param HotelImageRepositoryInterface $imageRepo
     * @param HotelRegistry $hotelRegistry
     */
    public function __construct(
        BrandRepositoryInterface $brandRepo,
        HotelRepositoryInterface $hotelRepo,
        HotelCorrelationRepositoryInterface $hotelCorrelationRepo,
        AmenityRepositoryInterface $amenityRepo,
        AttributeRepositoryInterface $attributeRepo,
        AmenityTypeRepositoryInterface $amenityTypeRepo,
        PropertyTypeRepositoryInterface $propertyTypeRepo,
        HotelImageRepositoryInterface $imageRepo,
        HotelRegistry $hotelRegistry
    )
    {
        $this->brandRepo = $brandRepo;
        $this->hotelRepo = $hotelRepo;
        $this->hotelCorrelationRepo = $hotelCorrelationRepo;
        $this->amenityTypeRepo = $amenityTypeRepo;
        $this->propertyTypeRepo = $propertyTypeRepo;
        $this->amenityRepo = $amenityRepo;
        $this->attributeRepo = $attributeRepo;
        $this->imageRepo = $imageRepo;
        $this->hotelRegistry = $hotelRegistry;
    }

    /**
     * Sync the parsed data
     *
     * @param PropertyDataSet $set
     *
     * @return $this
     */
    public function sync(PropertyDataSet $set)
    {
        $this->set = $set->parse();

        $this
            ->syncBrands()
            ->syncAmenityTypes()
            ->syncPropertyTypes()
            ->syncAmenities()
            ->syncAttributes()
            ->syncAmenities()
            ->syncHotels()
            ->syncDescriptions()
            ->syncImages()
            ->syncPropertyAmenities();
    }

    /**
     * @return $this
     */
    private function syncHotels()
    {
        $translator = [
            'PropertyName' => 'name',
            'PropertyTypeID'    => 'property_type_id',
            'Address1' => 'address1',
            'Address2' => 'address2',
            'City' => 'city',
            'StateCode' => 'state',
            'Postal' => 'zip',
            'CountryCode' => 'country',
            'Latitude' => 'latitude',
            'Longitude' => 'longitude',
            'NumRooms'  => 'sleeping_rooms',
            'NumFloors' => 'floors',
            'YearBuilt' => 'year_built',
            'YearOfLastRenov' => 'year_of_last_renovation',
            'PropertyPhone' => 'property_phone',
            'PropertyFax' => 'property_fax',
            'PropertyEmail' => 'property_email',
            'MobilStarRating' => 'mobil_star_rating',
        ];
        $brandTable = $this->brandRepo->all()->keyBy('code');

        $this->correlationTable = $this->hotelCorrelationRepo->allHotelsUsingSource('arn');

        $i = 0;
        //  Each piece file of the data set
        while ($this->set->parsePropertyList($i)) {

            //  Each row in the file
            foreach ($this->set->getProperties() as $correlationId => $property) {

                //  Create a translation table
                foreach ($translator as $dataKey => $dbKey) {
                    $translated[$dbKey] = (isset($property[$dataKey])) ? $property[$dataKey] : null;
                }

                //  If property name isn't set, this is a column title row - skip it
                if (!isset($property['PropertyName'])) {
                    continue;
                }

                // If there is a brand, lookup the internal brand code
                if (isset($brandTable[$property['BrandCode']])) {
                    $translated['brand_id'] = $brandTable[$property['BrandCode']]->id;
                }

                //  Make sure sleeping rooms isn't null
                $translated['sleeping_rooms'] = $translated['sleeping_rooms'] ?: 0;

                //  If there is a record existing with this correlation ID, update that row
                if (isset($this->correlationTable[$correlationId])) {
                    $this->hotelRepo->update(
                        $this->correlationTable[$correlationId],
                        $translated
                    );
                    continue;
                }

                //  If there is a matching item (which will NOT have the same correlation ID) - run the HotelRegistry
                //  merge algorithm, rather than inserting a new record. This is based on hotels that have the same
                //  name and similar coordinates
                $this->hotelRegistry
                    ->setItemData($translated)
                    ->pullMatch();

                if ($this->hotelRegistry->hasMatch()) {

                    $updates = $this->hotelRegistry->getUpdates();

                    if (count($updates) > 0) {
                        $this->hotelRepo->update(
                            $this->hotelRegistry->getMatchingHotel()->id,
                            $updates
                        );
                    }

                    continue;
                }

                //  If we're this far, this hotel is brand new, so store it
                $hotel = $this->hotelRepo->store(
                    $translated
                );
                $this->hotelCorrelationRepo->store([
                    'hotel_id' => $hotel->id,
                    'correlation_id' => $correlationId,
                    'source' => 'arn',
                ]);
            }
            $i++;
            if (self::MAX_LOOPS != null && $i > self::MAX_LOOPS) {
                break;
            }
        }

        $this->correlationTable = $this->hotelCorrelationRepo->allHotelsUsingSource('arn');

        return $this;
    }

    private function syncDescriptions()
    {
        $i = 0;
        while ($this->set->parsePropertyDescriptions($i)) {
            foreach ($this->set->getDescriptions() as $correlationId => $item) {
                if (!isset($item['PropertyDescription'])) {
                    continue;
                }
                if (isset($this->correlationTable[$correlationId])) {
                    $this->hotelRepo
                        ->update(
                            $this->correlationTable[$correlationId],
                            [
                                'description' => $item['PropertyDescription'],
                            ]
                        );
                }
            }
            $i++;
            if (self::MAX_LOOPS != null && $i > self::MAX_LOOPS) {
                break;
            }
        }

        return $this;
    }

    private function syncImages()
    {
        $translator = [
            'ImagePath' => 'main_path',
            'ImageThumbnailPath' => 'thumbnail_path',
            'SourceImagePath' => 'source_path',
            'ImageCaption' => 'caption',
            'DisplayOrder' => 'display_order',
        ];

        $i = 0;
        while ($this->set->parseImages($i)) {
            foreach ($this->set->getImages() as $item) {
                if (!isset($item['ImagePath'])) {
                    continue;
                }
                if (!isset($this->correlationTable[$item['PropertyID']])) {
                    continue;
                }
                foreach ($translator as $dataKey => $dbKey) {
                    $translated[$dbKey] = $item[$dataKey];
                }
                $translated['hotel_id'] = $this->correlationTable[$item['PropertyID']];
                $this->imageRepo->store(
                    $translated
                );
            }
            $i++;
            if ($i > 10) {
                break;
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function syncBrands()
    {
        $translator = [
            'BrandName' => 'name',
        ];
        $brandCodes = $this->brandRepo->all()->pluck('code');
        foreach ($this->set->getBrands() as $code => $item) {
            $translated = [];
            if (!isset($item['BrandName'])) {
                continue;
            }
            if ($brandCodes->contains($code)) {
                foreach ($translator as $dataKey => $dbKey) {
                    $translated[$dbKey] = $item[$dataKey];
                }
                $this->brandRepo->findWhere([
                    'code'  => $code,
                ])->update(
                    $translated
                );
                continue;
            }
            $this->brandRepo->store([
                'code'  => $code,
                'name'  => $item['BrandName'],
            ]);
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function syncPropertyTypes()
    {
        $existingTypes = $this->propertyTypeRepo->all()->pluck('id');
        foreach ($this->set->getPropertyTypes() as $id => $item) {
            if (!isset($item['PropertyType'])) {
                continue;
            }
            if (! $existingTypes->contains($id)) {
                $this->propertyTypeRepo->store([
                    'id'    => $id,
                    'name'  => $item['PropertyType'],
                ]);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function syncAttributes()
    {
        $existingAttributes = $this->attributeRepo->all()->pluck('id');
        foreach ($this->set->getAttributes() as $id => $item) {
            if (!isset($item['Description'])) {
                continue;
            }
            if (! $existingAttributes->contains($id)) {
                $this->attributeRepo->store([
                    'id'    => $id,
                    'name'  => $item['Description'],
                    'has_numeric_entry'  => ($item['HasNumericEntry'] == 'True'),
                ]);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function syncAmenities()
    {
        $existingAmenities = $this->amenityRepo->all()->pluck('id');
        foreach ($this->set->getAmenities() as $id => $item) {
            if (!isset($item['AmenityDescription'])) {
                continue;
            }
            if (! $existingAmenities->contains($id)) {
                $this->amenityRepo->store([
                    'id'    => $id,
                    'amenity_type_id'   => $item['AmenityTypeID'],
                    'name'  => $item['AmenityDescription'],
                ]);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function syncAmenityTypes()
    {
        $existingTypes = $this->amenityTypeRepo->all()->pluck('id');
        foreach ($this->set->getAmenityTypes() as $id => $item) {
            if (!isset($item['AmenityType'])) {
                continue;
            }
            if (! $existingTypes->contains($id)) {
                $this->amenityTypeRepo->store([
                    'id'    => $id,
                    'name'  => $item['AmenityType'],
                ]);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    private function syncPropertyAmenities()
    {
        \DB::table('amenity_hotel')->truncate();

        $i = 0;
        while ($this->set->parsePropertyAmenities($i)) {
            foreach ($this->set->getPropertyAmenities() as $item) {
                if (!isset($this->correlationTable[$item['PropertyID']])) {
                    continue;
                }
                $hotel = $this->hotelRepo->find(
                    $this->correlationTable[$item['PropertyID']]
                );
                $hotel->amenities()->attach(
                    $item['AmenityID'],
                    [
                        'attribute_id' => $item['AttributeID'],
                        'amenity_count' => ($item['AmenityCount']) ?: null,
                    ]
                );
            }
            $i++;
            if (self::MAX_LOOPS != null && $i > self::MAX_LOOPS) {
                break;
            }
        }
        return $this;
    }
}