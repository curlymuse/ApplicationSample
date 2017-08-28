<?php

namespace App\Http\Controllers\Api\Licensee;

use App\Http\Requests\Clause\CreateClauseRequest;
use App\Http\Requests\Clause\UpdateClauseRequest;
use App\Jobs\Clause\CreateClause;
use App\Jobs\Clause\DeleteClause;
use App\Jobs\Clause\UpdateClause;
use App\Repositories\Contracts\ClauseRepositoryInterface;
use App\Transformers\Clause\ClauseTransformer;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ClausesController extends Controller
{
    /**
     * @var ClauseRepositoryInterface
     */
    private $clauseRepo;

    /**
     * @var ClauseTransformer
     */
    private $transformer;

    /**
     * ClausesController constructor.
     * @param ClauseRepositoryInterface $clauseRepo
     * @param ClauseTransformer $transformer
     */
    public function __construct(ClauseRepositoryInterface $clauseRepo, ClauseTransformer $transformer)
    {
        $this->clauseRepo = $clauseRepo;
        $this->transformer = $transformer;
    }

    /**
     * Return an index of all of this licensee's clauses
     *
     * @return mixed
     */
    public function index()
    {
        $clauses = $this->transformer->transformCollection(
            $this->clauseRepo->allForLicensee(
                licenseeId()
            )
        );

        return response()->json(compact('clauses'));
    }

    /**
     * Create a new clause
     *
     * @param CreateClauseRequest $request
     * @return mixed
     */
    public function store(CreateClauseRequest $request)
    {
        $clause_id = $this->dispatchNow(
            new CreateClause(
                licenseeId(),
                $request->get('title'),
                $request->get('body'),
                $request->get('is_default')
            )
        );

        return response()->json(compact('clause_id'));
    }

    /**
     * Update an existing Clause
     *
     * @param UpdateClauseRequest $request
     * @param int $clauseId
     * @return mixed
     */
    public function update(UpdateClauseRequest $request, $clauseId)
    {
        dispatch(
            new UpdateClause(
                $clauseId,
                $request->get('attributes')
            )
        );

        return response()->json();
    }

    /**
     * Delete a Clause
     *
     * @param int $clauseId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($clauseId)
    {
        dispatch(
            new DeleteClause(
                $clauseId
            )
        );

        return response()->json();
    }
}
