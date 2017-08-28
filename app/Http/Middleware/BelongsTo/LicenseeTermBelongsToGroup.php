<?php

namespace App\Http\Middleware\BelongsTo;

use App\Repositories\Contracts\LicenseeTermRepositoryInterface;

class LicenseeTermBelongsToGroup extends BelongsTo
{
    /**
     * The belonging object's key
     *
     * @var string
     */
    protected $belongingIdKey = 'termId';

    /**
     * The belonged-to object's key
     *
     * @var string
     */
    protected $belongedToIdKey = 'groupId';

    /**
     * Name of foreign column on object's table
     *
     * @var string
     */
    protected $foreignColumn = 'licensee_term_group_id';

    /**
     * Class of the repository
     *
     * @var string
     */
    protected $repoClass = LicenseeTermRepositoryInterface::class;
}
