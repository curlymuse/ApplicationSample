<?php

namespace App\Http\Middleware\BelongsTo;

use App\Repositories\Contracts\EventRepositoryInterface;

class EventBelongsToClient extends BelongsTo
{
    /**
     * The belonging object's key
     *
     * @var string
     */
    protected $belongingIdKey = 'eventId';

    /**
     * The belonged-to object's key
     *
     * @var string
     */
    protected $belongedToIdKey = 'clientId';

    /**
     * Name of foreign column on object's table
     *
     * @var string
     */
    protected $foreignColumn = 'client_id';

    /**
     * Class of the repository
     *
     * @var string
     */
    protected $repoClass = EventRepositoryInterface::class;
}
