<?php

namespace App\Http\Controllers\Api\Licensee;

use App\Http\Requests\Guest\StoreGuestRequest;
use App\Http\Requests\Guest\UpdateGuestRequest;
use App\Jobs\Guest\CreateOrGetGuest;
use App\Jobs\Guest\DeleteGuest;
use App\Jobs\Guest\UpdateGuest;
use App\Repositories\Contracts\GuestRepositoryInterface;
use App\Transformers\Guest\BasicGuestTransformer;
use App\Transformers\Guest\GuestProfileTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GuestsController extends Controller
{
    /**
     * @var GuestRepositoryInterface
     */
    private $guestRepo;

    /**
     * @var GuestProfileTransformer
     */
    private $transformer;

    /**
     * @var BasicGuestTransformer
     */
    private $basicTransformer;

    /**
     * GuestsController constructor.
     * @param GuestRepositoryInterface $guestRepo
     * @param BasicGuestTransformer $basicTransformer
     * @param GuestProfileTransformer $transformer
     */
    public function __construct(
        GuestRepositoryInterface $guestRepo,
        BasicGuestTransformer $basicTransformer,
        GuestProfileTransformer $transformer
    )
    {
        $this->guestRepo = $guestRepo;
        $this->transformer = $transformer;
        $this->basicTransformer = $basicTransformer;
    }

    /**
     * Create a new guest, or pull an existing one
     *
     * @param StoreGuestRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreGuestRequest $request)
    {
        $guest = $this->basicTransformer->transform(
            $this->dispatchNow(
                new CreateOrGetGuest(
                    $request->get('email'),
                    $request->get('attributes')
                )
            )
        );

        return response()->json(compact('guest'));
    }

    /**
     * Show a guest's profile
     *
     * @param int $guestId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($guestId)
    {
        $guest = $this->transformer->transform(
            $this->guestRepo->find(
                $guestId
            )
        );

        return response()->json(compact('guest'));
    }

    /**
     * Delete a guest
     *
     * @param int $guestId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($guestId)
    {
        $this->dispatch(
            new DeleteGuest(
                $guestId
            )
        );

        return response()->json();
    }

    /**
     * Update a guest account
     *
     * @param UpdateGuestRequest $request
     * @param int $guestId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateGuestRequest $request, $guestId)
    {
        dispatch(
            new UpdateGuest(
                $guestId,
                collect($request->get('attributes'))->merge([
                    'email' => $request->get('email')
                ])->toArray()
            )
        );

        return response()->json();
    }
}
