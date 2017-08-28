<?php

namespace App\Http\Middleware\ObjectExists;

use App\Repositories\Contracts\BrandRepositoryInterface;
use App\Repositories\Contracts\ClientRepositoryInterface;
use Closure;

class BrandExists extends ObjectExists
{
    /**
     * @var string
     */
    protected $errorMessage = 'This Brand does not exist.';

    /**
     * @var string
     */
    protected $idKey = 'brandId';

    /**
     * Repository class
     *
     * @var string
     */
    protected $repoClass = BrandRepositoryInterface::class;
}
