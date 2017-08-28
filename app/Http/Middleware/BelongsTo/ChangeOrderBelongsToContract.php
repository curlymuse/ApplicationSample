<?php

namespace App\Http\Middleware\BelongsTo;


use App\Repositories\Contracts\ChangeOrderRepositoryInterface;

class ChangeOrderBelongsToContract extends BelongsTo
{
    /**
     * The belonging object's key
     *
     * @var string
     */
    protected $belongingIdKey = 'changeOrderId';

    /**
     * The belonged-to object's key
     *
     * @var string
     */
    protected $belongedToIdKey = 'contractId';

    /**
     * Name of foreign column on object's table
     *
     * @var string
     */
    protected $foreignColumn = 'contract_id';

    /**
     * Class of the repository
     *
     * @var string
     */
    protected $repoClass = ChangeOrderRepositoryInterface::class;
}
