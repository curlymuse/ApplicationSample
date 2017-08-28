<?php

namespace Tests\Unit\Jobs\Attachment;

use App\Jobs\Attachment\DeleteAttachment;
use App\Repositories\Contracts\AttachmentRepositoryInterface;
use App\Events\Attachment\AttachmentWasDeleted;

use Tests\TestCase;

/**
 * Class DeleteAttachmentTest
 *
 * @coversBaseClass App\Jobs\Attachment\DeleteAttachment
 */
class DeleteAttachmentTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

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

        $this->attachmentRepo = $this->expectsRepository(AttachmentRepositoryInterface::class);
    }

    public function test_handle()
    {
        $attachmentId = $this->faker->numberBetween(1, 1000);

        $this->attachmentRepo->shouldReceive('delete')
            ->once()
            ->with(
                $attachmentId
            );

        $this->expectsEvents(AttachmentWasDeleted::class);

        dispatch(
            new DeleteAttachment(
                $attachmentId
            )
        );
    }
}