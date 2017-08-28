<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\AttachmentRepositoryInterface;

class AttachmentRepository extends Repository implements AttachmentRepositoryInterface
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
    public function storeForAttachable($attachableType, $attachableId, $url, $displayName, $userId, $category = null)
    {
        return $this->store([
            'url'       => $url,
            'display_name'  => $displayName,
            'uploaded_by_user'  => $userId,
            'category'  => $category,
            'attachable_id' => $attachableId,
            'attachable_type'   => $attachableType,
        ]);
    }

    /**
     * Does this attachment belong to this attachable?
     *
     * @param int $attachmentId
     * @param string $attachableType
     * @param int $attachableId
     *
     * @return mixed
     */
    public function belongsToAttachable($attachmentId, $attachableType, $attachableId)
    {
        return $this->model
            ->whereId($attachmentId)
            ->whereAttachableType($attachableType)
            ->whereAttachableId($attachableId)
            ->exists();
    }
}