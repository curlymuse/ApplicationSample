<?php

namespace App\Support\Translator;

class Translator
{
    /**
     * @var TranslatorDriver
     */
    private $driver;

    private static $driverMap = [
        'arn'   => ArnTranslatorDriver::class,
        'expedia'   => ExpediaTranslatorDriver::class,
    ];

    /**
     * Translator constructor.
     * @param TranslatorDriver $driver
     */
    public function __construct(TranslatorDriver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Ingest the data
     *
     * @param PropertyDataSet $set
     *
     * @return $this
     */
    public function ingest(PropertyDataSet $set)
    {
        $this->driver
            ->sync(
                $set->parse()
            );

        return $this;
    }

    /**
     * Swap the driver
     *
     * @param string $driver
     *
     * @return $this
     */
    public function using($driver)
    {
        if (array_has(self::$driverMap, $driver)) {
            $this->driver = app(self::$driverMap[$driver]);
        }

        return $this;
    }
}