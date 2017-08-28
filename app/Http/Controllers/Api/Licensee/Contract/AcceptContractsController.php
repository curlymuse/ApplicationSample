<?php

namespace App\Http\Controllers\Api\Licensee\Contract;

use App\Http\Requests\Contract\SignContractRequest;
use App\Jobs\Contract\AcceptContract;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AcceptContractsController extends Controller
{
    /**
     * Accept a contract on behalf of this licensee
     *
     * @param SignContractRequest $request
     * @param int $contractId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SignContractRequest $request, $contractId)
    {
        dispatch(
            new AcceptContract(
                $contractId,
                userId(),
                'owner',
                $request->get('signature')
            )
        );

        return response()->json();
    }
}
