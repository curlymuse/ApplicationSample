<?php

namespace Tests\Unit\Jobs\Attachment;

use App\Events\Attachment\AttachmentWasStored;
use App\Events\Licensee\ProposalRequest\AttachmentWasAddedToProposalRequest;
use App\Jobs\Attachment\StoreAttachment;
use App\Repositories\Contracts\AttachmentRepositoryInterface;
use App\Repositories\Contracts\ProposalRequestRepositoryInterface;
use App\Support\UploadCoordinator;
use Tests\TestCase;

/**
 * Class AddProposalRequestAttachmentTest
 *
 * @coversBaseClass App\Jobs\ProposalRequest\AddProposalRequestAttachment
 */
class StoreAttachmentTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Support\UploadCoordinator
     */
    private $uploader;

    /**
     * @var \App\Repositories\Contracts\AttachmentRepositoryInterface
     */
    private $attachmentRepo;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->uploader = $this->mock(UploadCoordinator::class);
        $this->attachmentRepo = $this->expectsRepository(AttachmentRepositoryInterface::class);
    }

    public function test_handle()
    {
        $file = [];
        $userId = $this->faker->numberBetween(1, 1000);
        $attachableId = $this->faker->numberBetween(1, 1000);
        $attachableType = $this->faker->word;
        $directory = $this->faker->word;
        $category = $this->faker->word;
        $displayName = $this->faker->word;

        $url = $this->faker->url;

        $this->uploader->shouldReceive('publicUpload')
            ->once()
            ->with(
                $file,
                $directory
            )
            ->andReturn($url);

        $this->attachmentRepo->shouldReceive('storeForAttachable')
            ->once()
            ->with(
                $attachableType,
                $attachableId,
                $url,
                $displayName,
                $userId,
                $category
            );

        $this->expectsEvents(AttachmentWasStored::class);

        dispatch(
            new StoreAttachment(
                $file,
                $attachableType,
                $attachableId,
                $directory,
                $displayName,
                $userId,
                $category
            )
        );
    }
}