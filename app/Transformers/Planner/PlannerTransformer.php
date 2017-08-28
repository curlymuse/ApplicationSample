<?php

namespace App\Transformers\Planner;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Transformers\Transformer;
use App\Transformers\User\UserTransformer;

class PlannerTransformer extends Transformer
{
    /**
     * @var UserTransformer
     */
    private $userTransformer;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepo;

    /**
     * PlannerTransformer constructor.
     * @param UserTransformer $userTransformer
     */
    public function __construct(UserTransformer $userTransformer, UserRepositoryInterface $userRepo)
    {
        $this->userTransformer = $userTransformer;
        $this->userRepo = $userRepo;
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
        return (object)(collect($object)->only([
            'id',
            'name',
            'address1',
            'address2',
            'city',
            'state',
            'zip',
            'country',
        ])->merge([
            'users' => $this->userTransformer->transformCollection(
                $this->userRepo->allForPlanner($object->id)
            )
        ])->toArray());
    }
}