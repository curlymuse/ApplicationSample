<?php

namespace App\Http\Controllers\Api\Licensee\Contract;

use App\Http\Requests\ChangeOrder\CreateChangeOrderRequest;
use App\Jobs\Attachment\LinkAttachmentsToChangeOrders;
use App\Jobs\Attachment\ProcessTemporaryContractAttachments;
use App\Jobs\ChangeOrder\CreateChangeOrderSet;
use App\Repositories\Contracts\ChangeOrderRepositoryInterface;
use App\Transformers\ChangeOrder\ChangeOrderSetTransformer;
use App\Transformers\ChangeOrder\ChangeOrderTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChangeOrdersController extends Controller
{
    /**
     * @var ChangeOrderRepositoryInterface
     */
    private $changeOrderRepo;

    /**
     * @var ChangeOrderTransformer
     */
    private $transformer;

    /**
     * ChangeOrdersController constructor.
     * @param ChangeOrderRepositoryInterface $changeOrderRepo
     * @param ChangeOrderTransformer $transformer
     */
    public function __construct(
        ChangeOrderRepositoryInterface $changeOrderRepo,
        ChangeOrderSetTransformer $transformer
    )
    {
        $this->changeOrderRepo = $changeOrderRepo;
        $this->transformer = $transformer;
    }

    /**
     * Get a list of change orders for this contract
     *
     * @param int $contractId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($contractId)
    {
        $change_orders = $this->transformer->transformCollection(
            $this->changeOrderRepo->allForContract(
                $contractId
            )
        );

        return response()->json(compact('change_orders'));
    }

    /**
     * Create a new change order set for this contract
     *
     * @param CreateChangeOrderRequest $request
     * @param int $contractId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateChangeOrderRequest $request, $contractId)
    {
        $attachmentIds = $this->dispatchNow(
            new ProcessTemporaryContractAttachments(
                $request->get('add_attachments')['categories'],
                $request->file('add_attachments.files'),
                $contractId,
                userId()
            )
        );

        $change_order_id = $this->dispatchNow(
            new CreateChangeOrderSet(
                $contractId,
                $request->get('changes'),
                $attachmentIds,
                $request->get('remove_attachments'),
                userId(),
                'licensee',
                $request->get('labels'),
                $request->get('reason')
            )
        );

        //  If there are no changes, end now
        if (! $change_order_id) {
            return response()->json(compact('change_order_id'));
        }

        $this->dispatchNow(
            new LinkAttachmentsToChangeOrders(
                $change_order_id
            )
        );

        return response()->json(compact('change_order_id'));
    }
}
