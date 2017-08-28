<?php

namespace App\Repositories\Contracts;

interface AttachmentRepositoryInterface
{
    /**
     * Store an attachment for this object
     *
     * @param string $attachableType
     * @param int $attachableId
     * @param string $url
     * @param string $displayName
     * @param int $userId
     * @param string|null $category
     *
     * @return mixed
     */
    public function storeForAttachable($attachableType, $attachableId, $url, $displayName, $userId, $category = null);

    /**
     * Does this attachment belong to this attachable?
     *
     * @param int $attachmentId
     * @param string $attachableType
     * @param int $attachableId
     *
     * @return mixed
     */
    public function belongsToAttachable($attachmentId, $attachableType, $attachableId);
}