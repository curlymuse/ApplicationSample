<?php

namespace App\Transformers\Amenity;

use App\Models\Attribute;
use App\Transformers\Attribute\AttributeTransformer;
use App\Transformers\Transformer;

class AmenityTransformer extends Transformer
{
    /**
     * @var AttributeTransformer
     */
    private $attributeTransformer;

    /**
     * AmenityTransformer constructor.
     * @param AttributeTransformer $attributeTransformer
     */
    public function __construct(
        AttributeTransformer $attributeTransformer
    )
    {
        $this->attributeTransformer = $attributeTransformer;
    }

    /**
     * Transform a single object
     *
     * @param $object
     *
     * @return mixed
     */
    public function transform($object)
    {
        $attribute = Attribute::find($object->pivot->attribute_id);

        return (object)collect($object)->only([
            'id',
            'name',
        ])->merge([
            'type'  => ($object->type) ? $object->type->name : null,
            'count' => $object->pivot->amenity_count,
            'attribute' => ($attribute) ? $attribute->name : null,
        ])->toArray();
    }
}
