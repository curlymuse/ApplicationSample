<?php

namespace App\Jobs\Client;

use App\Events\Licensee\Client\ClientWasUpdated;
use App\Repositories\Contracts\ClientRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateClient implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $placeId;

    /**
     * @var array
     */
    private $attributes;

    /**
     * Create a new job instance.
     *
     * @param string $placeId
     * @param array $attributes
     */
    public function __construct($placeId, $attributes = [])
    {
        $this->placeId = $placeId;
        $this->attributes = $attributes;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ClientRepositoryInterface $clientRepo)
    {
        $clientRepo->updateWithPlaceId(
            $this->placeId,
            $this->attributes
        );

        event(
            new ClientWasUpdated(
                $this->placeId,
                $this->attributes
            )
        );
    }
}
