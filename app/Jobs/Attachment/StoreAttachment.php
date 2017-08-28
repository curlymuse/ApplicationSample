<?php

namespace App\Jobs\Attachment;

use App\Events\Attachment\AttachmentWasStored;
use App\Events\Licensee\ProposalRequest\AttachmentWasAddedToProposalRequest;
use App\Repositories\Contracts\AttachmentRepositoryInterface;
use App\Repositories\Contracts\ProposalRequestRepositoryInterface;
use App\Support\UploadCoordinator;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class StoreAttachment implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var mixed
     */
    private $file;

    /**
     * @var string
     */
    private $attachableType;

    /**
     * @var int
     */
    private $attachableId;

    /**
     * @var string
     */
    private $storeDirectory;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var null|string
     */
    private $category;

    /**
     * @var string
     */
    private $displayName;

    /**
     * Create a new job instance.
     *
     * @param mixed $file
     * @param string $attachableType
     * @param int $attachableId
     * @param string $storeDirectory
     * @param string $displayName
     * @param int $userId
     * @param string|null $category
     */
    public function __construct(
        $file,
        $attachableType,
        $attachableId,
        $storeDirectory,
        $displayName,
        $userId,
        $category = null
    )
    {
        $this->file = $file;
        $this->attachableType = $attachableType;
        $this->attachableId = $attachableId;
        $this->storeDirectory = $storeDirectory;
        $this->userId = $userId;
        $this->category = $category;
        $this->displayName = $displayName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(UploadCoordinator $uploader, AttachmentRepositoryInterface $attachmentRepo)
    {
        $url = $uploader->publicUpload(
            $this->file,
            $this->storeDirectory
        );

        $attachmentRepo->storeForAttachable(
            $this->attachableType,
            $this->attachableId,
            $url,
            $this->displayName,
            $this->userId,
            $this->category
        );

        event(
            new AttachmentWasStored(
                $this->attachableType,
                $this->attachableId,
                $url,
                $this->userId,
                $this->category
            )
        );

        return $url;
    }
}
