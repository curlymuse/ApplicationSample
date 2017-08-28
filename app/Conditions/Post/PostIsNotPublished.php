<?php

namespace App\Conditions\Post;

use App\Conditions\Condition;
use App\Jobs\Job;
use App\Repositories\Contracts\PostRepositoryInterface;

class PostIsNotPublished extends Condition
{
    /**
     * @var Job
     */
    protected $job;

    /**
     * @var \App\Repositories\Contracts\PostRepositoryInterface
     */
    private $postRepo;

    /**
     * @param Job $job
     */
    public function __construct(Job $job)
    {
        $this->job = $job;
        $this->postRepo = app(PostRepositoryInterface::class);
    }

    /**
     * Determine whether the job follows the condition
     *
     * @return boolean
     */
    public function holds()
    {
        return ! $this->postRepo->find($this->job->getPostId())->is_published;
    }
}
