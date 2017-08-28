<?php

namespace App\Listeners\Jobs\LogEntry;

use App\Events\Contracts\LoggableEvent;
use App\Events\Licensee\ProposalRequestWasCreated;
use App\Jobs\LogEntry\StoreLogEntry;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ListenThenLogEvent
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param LoggableEvent $event
     */
    public function handle(LoggableEvent $event)
    {
        dispatch(
            new StoreLogEntry(
                $event->getAccountType(),
                $event->getAccountId(),
                $event->getUserId(),
                $event->getAction(),
                $event->getSubjectType(),
                $event->getSubjectId(),
                $event->getDescription(),
                $event->getNotes()
            )
        );
    }
}
