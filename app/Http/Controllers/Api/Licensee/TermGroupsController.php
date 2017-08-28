<?php

namespace App\Http\Controllers\Api\Licensee;

use App\Http\Requests\LicenseeTermGroup\CreateLicenseeTermGroupRequest;
use App\Http\Requests\LicenseeTermGroup\UpdateLicenseeTermGroupRequest;
use App\Jobs\LicenseeTermGroup\CreateLicenseeTermGroup;
use App\Jobs\LicenseeTermGroup\DeleteLicenseeTermGroup;
use App\Jobs\LicenseeTermGroup\UpdateLicenseeTermGroup;
use App\Repositories\Contracts\LicenseeTermGroupRepositoryInterface;
use App\Transformers\LicenseeTermGroup\LicenseeTermGroupTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TermGroupsController extends Controller
{

    /**
     * @var LicenseeTermGroupRepositoryInterface
     */
    private $groupRepo;

    /**
     * @var LicenseeTermGroupTransformer
     */
    private $transformer;

    /**
     * @param LicenseeTermGroupRepositoryInterface $groupRepo
     * @param LicenseeTermGroupTransformer $transformer
     */
    public function __construct(
        LicenseeTermGroupRepositoryInterface $groupRepo,
        LicenseeTermGroupTransformer $transformer
    )
    {
        $this->groupRepo = $groupRepo;
        $this->transformer = $transformer;
    }

    /**
     * Get a list of all term groups for this licensee
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $groups = $this->transformer->transformCollection(
            $this->groupRepo->allForLicensee(
                licenseeId()
            )
        );

        return response()->json(compact('groups'));
    }

    /**
     * Create a new term group for this licensee
     *
     * @param CreateLicenseeTermGroupRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateLicenseeTermGroupRequest $request)
    {
        $group_id = $this->dispatchNow(
            new CreateLicenseeTermGroup(
                licenseeId(),
                $request->get('name')
            )
        );

        return response()->json(compact('group_id'));
    }

    /**
     * Update an existing term group
     *
     * @param UpdateLicenseeTermGroupRequest $request
     * @param int $groupId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateLicenseeTermGroupRequest $request, $groupId)
    {
        dispatch(
            new UpdateLicenseeTermGroup(
                $groupId,
                $request->get('name')
            )
        );

        return response()->json();
    }

    /**
     * Delete a term group
     *
     * @param int $groupId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($groupId)
    {
        dispatch(
            new DeleteLicenseeTermGroup(
                $groupId
            )
        );

        return response()->json();
    }

}
