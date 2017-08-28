<?php

namespace App\Support\Translator;

class ArnPropertyDataSet implements PropertyDataSet
{
    /**
     * @var array
     */
    private $properties = [];

    /**
     * @var array
     */
    private $propertyTypes = [];

    /**
     * @var array
     */
    private $brands = [];

    /**
     * @var array
     */
    private $amenities = [];

    /**
     * @var array
     */
    private $amenityTypes = [];

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @var array
     */
    private $descriptions = [];

    /**
     * @var array
     */
    private $images = [];

    /**
     * @var array
     */
    private $propertyAmenities = [];

    /**
     * @return string
     */
    public function getPropertyIndexFile()
    {
        return $this->getStorageDir() . 'PropertyActive.txt';
    }

    /**
     * Get a list of all foreign/related model file paths
     *
     * @return array
     */
    public function getForeignIndexFiles()
    {
        return [
            'Amenity'   => $this->getStorageDir() . 'Amenity.txt',
            'AmenityAttribute'  => $this->getStorageDir() . 'AmenityAttribute.txt',
            'AmenityType'   => $this->getStorageDir() . 'AmenityType.txt',
            'Attribute'     => $this->getStorageDir() . 'Attribute.txt',
            'Brand'         => $this->getStorageDir() . 'Brand.txt',
            'PropertyAmenity'   => $this->getStorageDir() . 'PropertyAmenity.txt',
            'PropertyDescription'   => $this->getStorageDir() . 'PropertyDescription.txt',
            'PropertyImage'   => $this->getStorageDir() . 'PropertyImage.txt',
            'PropertyType'   => $this->getStorageDir() . 'PropertyType.txt',
            'PropertyActive'   => $this->getStorageDir() . 'PropertyActive.txt',
        ];
    }

    /**
     * Get the URL of a remote index
     *
     * @param string $index
     *
     * @return string
     */
    public function getRemoteIndexUrl($index)
    {
        return sprintf(
            '%s/%s.txt',
            $this->getRemoteDir(),
            $index
        );
    }

    /**
     * Get a single foreign/related file path
     *
     * @param $index
     *
     * @return string
     */
    public function getForeignIndexFile($index)
    {
        return $this->getForeignIndexFiles()[$index];
    }

    /**
     * Pull data from remote location and store locally
     *
     * @return $this
     */
    public function pull()
    {
        return $this;
    }

    /**
     * Parse the property data set into memory
     *
     * @return $this
     */
    public function parse()
    {
        $this
            ->parseAmenities()
            ->parseAmenityTypes()
            ->parseAttributes()
            ->parsePropertyTypes()
            ->parseBrands();

        return $this;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @return array
     */
    public function getBrands()
    {
        return $this->brands;
    }

    /**
     * @return array
     */
    public function getAmenities()
    {
        return $this->amenities;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return array
     */
    public function getDescriptions()
    {
        return $this->descriptions;
    }

    /**
     * @return array
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @return array
     */
    public function getPropertyTypes()
    {
        return $this->propertyTypes;
    }

    /**
     * @return array
     */
    public function getAmenityTypes()
    {
        return $this->amenityTypes;
    }

    /**
     * @return string
     */
    public function getStorageDir()
    {
        return app_path() . '/../storage/data/ARN/';
    }

    /**
     * @param int|null $filePart
     *
     * @return $this
     */
    public function parsePropertyList($filePart = null)
    {
        $baseFilename = $filename = $this->getPropertyIndexFile();

        if ($filePart !== null) {
            $filename = sprintf(
                '%s_part%d.txt',
                explode('.txt', $baseFilename)[0],
                $filePart
            );
        }

        if (!file_exists($filename)) {
            return false;
        }

        $this->properties = $this->parseDataFile($filename)
            ->keyBy('PropertyID');

        return $this;
    }

    /**
     * @return array
     */
    public function getPropertyAmenities()
    {
        return $this->propertyAmenities;
    }

    /**
     * @return $this
     */
    private function parseBrands()
    {
        $this->brands = $this->parseDataFile($this->getForeignIndexFile('Brand'))
            ->forget(0)
            ->keyBy('BrandCode');

        return $this;
    }

    /**
     * @return $this
     */
    private function parseAmenities()
    {
        $this->amenities = $this->parseDataFile($this->getForeignIndexFile('Amenity'))
            ->forget(0)
            ->keyBy('AmenityID');

        return $this;
    }

    /**
     * @return $this
     */
    private function parseAmenityTypes()
    {
        $this->amenityTypes = $this->parseDataFile($this->getForeignIndexFile('AmenityType'))
            ->forget(0)
            ->keyBy('AmenityTypeID');

        return $this;
    }

    /**
     * @return $this
     */
    private function parsePropertyTypes()
    {
        $this->propertyTypes = $this->parseDataFile($this->getForeignIndexFile('PropertyType'))
            ->forget(0)
            ->keyBy('PropertyTypeID');

        return $this;
    }

    /**
     * @param int|null $filePart
     *
     * @return $this
     */
    public function parsePropertyDescriptions($filePart = null)
    {
        $baseFilename = $filename = $this->getForeignIndexFile('PropertyDescription');

        if ($filePart !== null) {
            $filename = sprintf(
                '%s_part%d.txt',
                explode('.txt', $baseFilename)[0],
                $filePart
            );
        }

        if (!file_exists($filename)) {
            return false;
        }

        $this->descriptions = $this->parseDataFile($filename)
            ->forget(0)
            ->keyBy('PropertyID');

        return $this;
    }

    /**
     * @return $this
     */
    private function parseAttributes()
    {
        $this->attributes = $this->parseDataFile($this->getForeignIndexFile('Attribute'))
            ->forget(0)
            ->keyBy('AttributeID');

        return $this;
    }

    public function parseImages($filePart = null)
    {
        $baseFilename = $filename = $this->getForeignIndexFile('PropertyImage');

        if ($filePart !== null) {
            $filename = sprintf(
                '%s_part%d.txt',
                explode('.txt', $baseFilename)[0],
                $filePart
            );
        }

        if (!file_exists($filename)) {
            return false;
        }

        $this->images = $this->parseDataFile($filename)
            ->forget(0);

        return $this;
    }

    /**
     * @return $this
     */
    public function parsePropertyAmenities($filePart = null)
    {
        $baseFilename = $filename = $this->getForeignIndexFile('PropertyAmenity');

        if ($filePart !== null) {
            $filename = sprintf(
                '%s_part%d.txt',
                explode('.txt', $baseFilename)[0],
                $filePart
            );
        }

        if (!file_exists($filename)) {
            return false;
        }

        $this->propertyAmenities = $this->parseDataFile($filename)
            ->forget(0);

        return $this;
    }

    /**
     * @return string
     */
    private function getRemoteDir()
    {
        return 'http://admin.reservetravel.com/data/Databases/Property-Information-Files';
    }

    /**
     * Parse a data file
     *
     * @param string $filename
     *
     * @return Collection
     */
    private function parseDataFile($filename)
    {
        $handle = @fopen($filename, 'r');
        $itemArray = [];
        if ($handle) {
            $i = 0;
            $keys = [];
            while (($buffer = stream_get_line($handle, 4096, "\r\n")) !== false) {
                if ($i++ == 0) {
                    $keys = explode('|', $buffer);
                    continue;
                }
                $item = explode('|', $buffer);
                $input = [];
                foreach ($item as $i => $value) {
                    if (!isset ($keys[$i])) {
                        break;
                    }
                    $input[$keys[$i]] = $value;
                }
                $itemArray[] = $input;
            }
            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        }

        return collect($itemArray);
    }
}