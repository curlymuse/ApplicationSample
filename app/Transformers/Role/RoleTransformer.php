<?php

namespace App\Transformers\Role;

use App\Models\Licensee;
use App\Repositories\Contracts\LicenseeRepositoryInterface;
use App\Transformers\Transformer;

class RoleTransformer extends Transformer
{
    /**
     * @var LicenseeRepositoryInterface
     */
    private $licenseeRepo;

    /**
     * @param LicenseeRepositoryInterface $licenseeRepo
     */
    public function __construct(LicenseeRepositoryInterface $licenseeRepo)
    {
        $this->licenseeRepo = $licenseeRepo;
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
        $rolable = $this->getRolable($object);

        $return = (object)[
            'name'      => $object->name,
            'slug'      => $object->slug,
        ];

        if ($rolable) {
            $return->rolable_type = class_basename($rolable);
            $return->rolable_name = $rolable->company_name;
        }

        return $return;
    }

    private function getRolable($object)
    {
        if ($object->pivot->rolable_type == Licensee::class) {
            return $this->licenseeRepo->find($object->pivot->rolable_id);
        }
    }
}
