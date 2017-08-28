<?php

namespace App\Transformers\ChangeOrder;

use App\Transformers\Attachment\AttachmentTransformer;
use App\Transformers\Transformer;
use App\Transformers\User\SimpleUserTransformer;

class ChangeOrderTransformer extends Transformer
{
    /**
     * @var SimpleUserTransformer
     */
    private $userTransformer;

    /**
     * @var AttachmentTransformer
     */
    private $attachmentTransformer;

    /**
     * ChangeOrderTransformer constructor.
     * @param SimpleUserTransformer $userTransformer
     * @param AttachmentTransformer $attachmentTransformer
     */
    public function __construct(
        SimpleUserTransformer $userTransformer,
        AttachmentTransformer $attachmentTransformer
    )
    {
        $this->userTransformer = $userTransformer;
        $this->attachmentTransformer = $attachmentTransformer;
    }

    /**
     * Transform a single object
     *
     * @param $object
     *
     * @return mixed
     */
    public function transform($object)
    {
        //  Pull the attachment, if applicable
        $attachment = null;
        if ($object->change_type == 'add' && $object->change_key == 'attachments') {

            //  The attachment may be joined to the change order itself - this will be the case if the CO is pending
            $attachment = $object->attachments->where('id', $object->proposed_value)->first();

            //  Otherwise, the attachment will be joined to the contract
            if (! $attachment) {
                $attachment = $object->contract->attachments->where('id', $object->proposed_value)->first();
            }

            //  Now that we have the attachment, let's transform it
            $attachment = $this->attachmentTransformer->transform($attachment);
        }

        return (object)collect($object)->only([
            'id',
            'change_type',
            'change_key',
            'change_display',
            'original_value',
            'proposed_value',
            'declined_because',
        ])->merge([
            'declined_by_user' => ($object->declinedByUser) ? $this->userTransformer->transform($object->declinedByUser) : null,
            'accepted_by_user' => ($object->acceptedByUser) ? $this->userTransformer->transform($object->acceptedByUser) : null,
            'declined_at' => self::dateFormatOrNull($object->declined_at, 'Y-m-d H:i:s'),
            'accepted_at' => self::dateFormatOrNull($object->accepted_at, 'Y-m-d H:i:s'),
            'attachment'    => $attachment,
        ])->toArray();
    }
}