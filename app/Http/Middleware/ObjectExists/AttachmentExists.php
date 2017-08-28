<?php

namespace App\Http\Middleware\ObjectExists;

use App\Repositories\Contracts\AttachmentRepositoryInterface;
use Closure;

class AttachmentExists extends ObjectExists
{
    /**
     * @var string
     */
    protected $errorMessage = 'This Attachment does not exist.';

    /**
     * @var string
     */
    protected $idKey = 'attachmentId';

    /**
     * Repository class
     *
     * @var string
     */
    protected $repoClass = AttachmentRepositoryInterface::class;
}
