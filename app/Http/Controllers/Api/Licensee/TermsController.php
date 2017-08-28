<?php

namespace App\Http\Controllers\Api\Licensee;

use App\Http\Requests\LicenseeTerm\CreateLicenseeTermRequest;
use App\Http\Requests\LicenseeTerm\UpdateLicenseeTermRequest;
use App\Jobs\LicenseeTerm\CreateLicenseeTerm;
use App\Jobs\LicenseeTerm\DeleteLicenseeTerm;
use App\Jobs\LicenseeTerm\UpdateLicenseeTerm;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TermsController extends Controller
{
    /**
     * Create a new term for this licensee
     *
     * @param CreateLicenseeTermRequest $request
     * @param int $groupId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateLicenseeTermRequest $request, $groupId)
    {
        $term_id = $this->dispatchNow(
            new CreateLicenseeTerm(
                $groupId,
                $request->get('title'),
                $request->get('description')
            )
        );

        return response()->json(compact('term_id'));
    }

    /**
     * Update an existing term
     *
     * @param UpdateLicenseeTermRequest $request
     * @param int $groupId
     * @param int $termId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateLicenseeTermRequest $request, $groupId, $termId)
    {
        dispatch(
            new UpdateLicenseeTerm(
                $termId,
                $request->get('title'),
                $request->get('description')
            )
        );

        return response()->json();
    }

    /**
     * Delete an existing term
     *
     * @param int $groupId
     * @param int $termId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($groupId, $termId)
    {
        dispatch(
            new DeleteLicenseeTerm(
                $termId
            )
        );

        return response()->json();
    }
}
