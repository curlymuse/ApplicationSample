<?php

namespace App\Jobs\Attachment;

use App\Events\Attachment\AttachmentWasDeleted;
use App\Models\Attachment;
use App\Repositories\Contracts\AttachmentRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteAttachment implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $attachmentId;

    /**
     * Create a new job instance.
     *
     * @param int $attachmentId
     *
     * @return void
     */
    public function __construct($attachmentId)
    {
        //
        $this->attachmentId = $attachmentId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(AttachmentRepositoryInterface $attachmentRepo)
    {
        $attachmentRepo->delete($this->attachmentId);

        event(
            new AttachmentWasDeleted(
                $this->attachmentId
            )
        );
    }
}
