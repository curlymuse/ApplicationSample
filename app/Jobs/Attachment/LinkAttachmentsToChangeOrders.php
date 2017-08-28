<?php

namespace App\Jobs\Attachment;

use App\Models\ChangeOrder;
use App\Repositories\Contracts\AttachmentRepositoryInterface;
use App\Repositories\Contracts\ChangeOrderRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LinkAttachmentsToChangeOrders implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $changeOrderId;

    /**
     * Create a new job instance.
     *
     * @param int $changeOrderId
     *
     * @return void
     */
    public function __construct($changeOrderId)
    {
        $this->changeOrderId = $changeOrderId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        ChangeOrderRepositoryInterface $changeOrderRepo,
        AttachmentRepositoryInterface $attachmentRepo
    )
    {
        $changeOrder = $changeOrderRepo->find($this->changeOrderId);

        foreach ($changeOrder->children as $item) {
            if ($item->change_type != 'add' || $item->change_key != 'attachments') {
                continue;
            }

            $attachment = $attachmentRepo->find($item->proposed_value);

            $attachmentRepo->update(
                $attachment->id,
                [
                    'attachable_type'   => ChangeOrder::class,
                    'attachable_id'     => $item->id,
                    'category'          => explode(':', $attachment->category)[1],
                ]
            );
        }
    }
}
