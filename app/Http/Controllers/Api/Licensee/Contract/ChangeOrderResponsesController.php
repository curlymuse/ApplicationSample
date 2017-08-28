<?php

namespace App\Http\Controllers\Api\Licensee\Contract;

use App\Http\Requests\ChangeOrder\ChangeOrderResponseRequest;
use App\Jobs\ChangeOrder\ProcessChangeOrderResponses;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChangeOrderResponsesController extends Controller
{
    /**
     * Respond to change orders
     *
     * @param ChangeOrderResponseRequest $request
     * @param int $contractId
     * @param int $changeOrderId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ChangeOrderResponseRequest $request, $contractId, $changeOrderId)
    {
        dispatch(
            new ProcessChangeOrderResponses(
                $changeOrderId,
                userId(),
                $request->get('changes')
            )
        );

        return response()->json();
    }
}
