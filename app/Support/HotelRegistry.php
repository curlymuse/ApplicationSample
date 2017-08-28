<?php

namespace App\Support;

use App\Repositories\Contracts\HotelRepositoryInterface;

class HotelRegistry
{
    private static $metaFields = [
        'sleeping_rooms',
        'floors',
        'year_built',
        'year_of_last_renovation',
        'property_phone',
        'property_fax',
        'property_email',
        'mobil_star_rating',
    ];

    private static $addressFields = [
        'address1',
        'address2',
        'city',
        'state',
        'zip',
        'country',
    ];

    /**
     * @var array
     */
    private $itemData = [];

    /**
     * @var \App\Models\Hotel
     */
    private $matchingHotel;

    /**
     * @var array
     */
    private $updates = [];

    /**
     * @var int
     */
    private $matchIntegrityScore;

    /**
     * @var int
     */
    private $inputIntegrityScore;

    /**
     * @var int
     */
    private $matchAddressIntegrityScore;

    /**
     * @var int
     */
    private $inputAddressIntegrityScore;

    /**
     * @var HotelRepositoryInterface
     */
    private $hotelRepo;

    /**
     * @param HotelRepositoryInterface $hotelRepo
     */
    public function __construct(HotelRepositoryInterface $hotelRepo)
    {
        $this->hotelRepo = $hotelRepo;
    }

    /**
     * @return array
     */
    public static function getMetaFields()
    {
        return self::$metaFields;
    }

    /**
     * @return array
     */
    public static function getAddressFields()
    {
        return self::$addressFields;
    }

    /**
     * Check for matching property
     *
     * @return $this
     */
    public function pullMatch()
    {
        $this->matchingHotel = $this->hotelRepo->findPropertyMatch(
            $this->itemData['name'],
            $this->itemData['latitude'],
            $this->itemData['longitude']
        );

        return $this;
    }

    /**
     * Is there a matching hotel?
     *
     * @return bool
     */
    public function hasMatch()
    {
        return (bool)$this->matchingHotel;
    }

    /**
     * Get matching hotel
     *
     * @return \App\Models\Hotel
     */
    public function getMatchingHotel()
    {
        return $this->matchingHotel;
    }

    /**
     * Get an array of fields to update on the matching property
     *
     * @return array|null
     */
    public function getUpdates()
    {
        if (! $this->matchingHotel) {
            return null;
        }

        $this
            ->storeInputIntegrityScore()
            ->storeMatchIntegrityScore()
            ->storeInputAddressIntegrityScore()
            ->storeMatchAddressIntegrityScore()
            ->pullAddressUpdates()
            ->pullMetaUpdates();

        return $this->updates;
    }

    /**
     * Set the item data
     *
     * @param array $itemData
     *
     * @return $this
     */
    public function setItemData($itemData)
    {
        $this->itemData = $itemData;

        return $this;
    }

    /**
     * Get list of address fields to update
     *
     * @return $this
     */
    private function pullAddressUpdates()
    {
        //  If new address has no address1, it is useless - no address updates needed
        if (!(bool)$this->itemData['address1']) {
            return $this;
        }

        //  Otherwise, if old address has no address1, use the entire new address
        if (!(bool)$this->matchingHotel->address1) {
            return $this->addAddressUpdates();
        }

        //  If we are this far, we have 2 addresses

        //  If the new address has a equal or greater integrity score, choose that one
        if ($this->inputAddressIntegrityScore >= $this->matchAddressIntegrityScore) {

            //  But first, make sure it isn't EXACTLY the same - if it is, no need for updates
            $diffFields = 0;
            foreach (self::getAddressFields() as $field) {
                if ($this->itemData[$field] != $this->matchingHotel->$field) {
                    $diffFields++;
                }
            }
            if ($diffFields == 0) {
                return $this;
            }

            return $this->addAddressUpdates();
        }

        //  If we're this far, the old address has higher integrity, so ditch the new one
        return $this;
    }

    /**
     * Get list of meta fields to update
     *
     * @return $this
     */
    private function pullMetaUpdates()
    {
        foreach (self::getMetaFields() as $field) {

            //  If no value is present in existing object
            if (!(bool)$this->matchingHotel->$field) {

                //  If value IS present in new object, add it to the update list
                if ((bool)$this->itemData[$field]) {
                    $this->updates[$field] = $this->itemData[$field];
                }

                //  If not, we're done
                continue;
            }

            //  If we have no data for either entry, we're done
            if (!(bool)$this->itemData[$field]) {
                continue;
            }

            //  If both values are the same, we're done
            if ($this->itemData[$field] == $this->matchingHotel->$field) {
                continue;
            }

            //  If we are this far, we have two non-null values to choose from

            //  If the old object has a higher integrity score, do nothing
            if ($this->matchIntegrityScore > $this->inputIntegrityScore) {
                continue;
            }

            //  If the two scores are equal or the input score is higher, add an update
            $this->updates[$field] = $this->itemData[$field];
        }

        return $this;
    }

    /**
     * Add all address fields from new data to updates list
     *
     * @return $this
     */
    private function addAddressUpdates()
    {
        foreach (self::getAddressFields() as $field) {
            $this->updates[$field] = $this->itemData[$field];
        }

        return $this;
    }

    /**
     * Store the integrity score for the item data
     *
     * @return $this
     */
    private function storeInputIntegrityScore()
    {
        $this->inputIntegrityScore = self::calculateIntegrityScore(
            $this->itemData
        );

        return $this;
    }

    /**
     * Store the integrity score for the matching hotel
     *
     * @return $this
     */
    private function storeMatchIntegrityScore()
    {
        $this->matchIntegrityScore = self::calculateIntegrityScore(
            $this->matchingHotel
        );

        return $this;
    }

    /**
     * Store the integrity score for the item data
     *
     * @return $this
     */
    private function storeInputAddressIntegrityScore()
    {
        $this->inputAddressIntegrityScore = self::calculateIntegrityScore(
            $this->itemData,
            true
        );

        return $this;
    }

    /**
     * Store the integrity score for the matching hotel
     *
     * @return $this
     */
    private function storeMatchAddressIntegrityScore()
    {
        $this->matchAddressIntegrityScore = self::calculateIntegrityScore(
            $this->matchingHotel,
            true
        );

        return $this;
    }

    /**
     * What percentage of these fields are not null?
     *
     * @param $input
     * @param bool $isAddress
     *
     * @return float
     */
    private static function calculateIntegrityScore($input, $isAddress = false)
    {
        $fields = ($isAddress)
            ? collect(self::getAddressFields())->except('address2')->toArray()
            : self::getMetaFields();

        $notNull = 0;
        $collection = collect($input)
            ->only($fields)
            ->toArray();

        foreach ($collection as $key => $value) {
            if ((bool)$value) {
                $notNull++;
            }
        }

        return $notNull / count($fields);
    }
}