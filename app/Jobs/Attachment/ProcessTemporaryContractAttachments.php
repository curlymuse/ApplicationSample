<?php

namespace App\Jobs\Attachment;

use App\Models\Contract;
use App\Repositories\Contracts\AttachmentRepositoryInterface;
use App\Support\UploadCoordinator;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessTemporaryContractAttachments implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    private $categories;

    /**
     * @var array
     */
    private $files;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var int
     */
    private $contractId;

    /**
     * Create a new job instance.
     *
     * @param array $categories
     * @param array $files
     * @param int $contractId
     * @param int $userId
     */
    public function __construct($categories = [], $files = [], $contractId, $userId)
    {
        $this->categories = $categories;
        $this->files = $files;
        $this->userId = $userId;
        $this->contractId = $contractId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(UploadCoordinator $uploader, AttachmentRepositoryInterface $attachmentRepo)
    {
        $ids = [];

        if (! $this->files) {
            return $ids;
        }

        $directory = sprintf(
            '%s/%d',
            config('resbeat.storage.contract-attachment-dir'),
            $this->contractId
        );

        foreach ($this->files as $i => $file) {

            $url = $uploader->publicUpload(
                $file,
                $directory
            );

            $attachment = $attachmentRepo->storeForAttachable(
                Contract::class,
                $this->contractId,
                $url,
                $file->getClientOriginalName(),
                $this->userId,
                sprintf(
                    'temp:%s',
                    $this->categories[$i]
                )
            );

            $ids[] = $attachment->id;
        }

        return $ids;
    }
}
