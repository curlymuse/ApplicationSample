<?php

namespace App\Support\Translator;

interface PropertyDataSet
{
    /**
     * @return array
     */
    public function getProperties();

    /**
     * @return array
     */
    public function getPropertyTypes();

    /**
     * @return array
     */
    public function getBrands();

    /**
     * @return array
     */
    public function getAmenities();

    /**
     * @return array
     */
    public function getAttributes();

    /**
     * @return array
     */
    public function getAmenityTypes();

    /**
     * @return array
     */
    public function getDescriptions();

    /**
     * @return array
     */
    public function getImages();

    /**
     * @return array
     */
    public function getPropertyAmenities();

    /**
     * Pull data from remote location and store locally
     *
     * @return $this
     */
    public function pull();

    /**
     * Parse data from source into private properties
     *
     * @return $this
     */
    public function parse();
}