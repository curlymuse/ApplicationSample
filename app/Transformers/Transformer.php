<?php

namespace App\Transformers;

use Carbon\Carbon;
use Illuminate\Support\Collection;

abstract class Transformer
{
    /**
     * Transform a collection of objects
     *
     * @param Collection $objects
     *
     * @return array
     */
    public function transformCollection(Collection $objects)
    {
        return $objects->map([$this, 'transform']);
    }

    /**
     * Transform a single object
     *
     * @param $object
     *
     * @return mixed
     */
    abstract public function transform($object);

    /**
     * Return either null or a formatted date
     *
     * @param null|Carbon $item
     * @param string $format
     *
     * @return null|string
     */
    protected static function dateFormatOrNull($item = null, $format)
    {
        return ($item) ? $item->format($format) : null;
    }
}
