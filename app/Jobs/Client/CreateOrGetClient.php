<?php

namespace App\Jobs\Client;

use App\Events\Licensee\Client\ClientWasCreated;
use App\Repositories\Contracts\ClientRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateOrGetClient implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $plannerId;

    /**
     * @var array
     */
    private $attributes;

    /**
     * Create a new job instance.
     *
     * @param int $plannerId
     * @param array $attributes
     */
    public function __construct($plannerId, $attributes = [])
    {
        $this->plannerId = $plannerId;
        $this->attributes = $attributes;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ClientRepositoryInterface $clientRepo)
    {
        $isNewObject = false;
        $client =  $clientRepo->findOrCreateWithPlaceId(
            $this->plannerId,
            $this->attributes,
            $isNewObject
        );

        if ($isNewObject) {
            event(
                new ClientWasCreated(
                    $client->id
                )
            );
        }

        return $client->id;
    }
}
