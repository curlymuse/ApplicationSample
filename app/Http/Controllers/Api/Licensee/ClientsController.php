<?php

namespace App\Http\Controllers\Api\Licensee;

use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Jobs\Client\CreateOrGetClient;
use App\Jobs\Client\UpdateClient;
use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Transformers\Client\ClientTransformer;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ClientsController extends Controller
{
    /**
     * @var ClientRepositoryInterface
     */
    private $clientRepo;

    /**
     * @var ClientTransformer
     */
    private $transformer;

    /**
     * ClientsController constructor.
     * @param ClientRepositoryInterface $clientRepo
     * @param ClientTransformer $transformer
     */
    public function __construct(
        ClientRepositoryInterface $clientRepo,
        ClientTransformer $transformer
    )
    {
        $this->clientRepo = $clientRepo;
        $this->transformer = $transformer;
    }

    /**
     * Get clients for this licensee
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $clients = $this->transformer->transformCollection(
            $this->clientRepo->allForLicensee(
                licenseeId()
            )
        );

        return response()->json(compact('clients'));
    }

    /**
     * Store a new client if one with this google ID does not exist. In either case, return the internal ID
     * of the resource
     *
     * @param StoreClientRequest|Request $request
     * @param $placeId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreClientRequest $request, $placeId)
    {
        $client_id = $this->dispatchNow(
            new CreateOrGetClient(
                $placeId,
                $request->get('attributes')
            )
        );

        return response()->json(compact('client_id'));
    }

    /**
     * Update the client with this placeID
     *
     * @param UpdateClientRequest|Request $request
     * @param string $placeId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateClientRequest $request, $placeId)
    {
        $this->dispatch(
            new UpdateClient(
                $placeId,
                $request->get('attributes')
            )
        );

        return response()->json();
    }
}
