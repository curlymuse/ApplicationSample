<?php

namespace App\Http\Middleware\ObjectExists;

use App\Repositories\Contracts\ChangeOrderRepositoryInterface;
use Closure;

class ChangeOrderExists extends ObjectExists
{
    /**
     * @var string
     */
    protected $errorMessage = 'This Change Order does not exist.';

    /**
     * @var string
     */
    protected $idKey = 'changeOrderId';

    /**
     * Repository class
     *
     * @var string
     */
    protected $repoClass = ChangeOrderRepositoryInterface::class;
}
