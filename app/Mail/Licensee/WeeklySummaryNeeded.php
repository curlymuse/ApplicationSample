<?php

namespace App\Mail\Licensee;

use App\Models\Licensee;
use App\Support\MyMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WeeklySummaryNeeded extends MyMailable
{
    use Queueable, SerializesModels;

    /**
     * @var Licensee
     */
    private $licensee;

    /**
     * @var array
     */
    private $loggedEvents;

    /**
     * Create a new message instance.
     *
     * @param Licensee $licensee
     * @param array $loggedEvents
     */
    public function __construct(Licensee $licensee, $loggedEvents = [])
    {
        $this->licensee = $licensee;
        $this->loggedEvents = $loggedEvents;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = [
            'requests'  => $this->formatRequestData(),
            'licensee'      => $this->licensee->company_name,
        ];

        return $this->view('emails.licensee.weekly-summary')
            ->text('emails.licensee.weekly-summary_plain')
            ->from(config('mail.from.address'), $data['licensee'])
            ->subject('Weekly Summary')
            ->with($data);
    }

    private function formatRequestData()
    {
        $requests = [];

        if (isset($this->loggedEvents['proposal-request:disburse'])) {
            foreach ($this->loggedEvents['proposal-request:disburse'] as $event) {
                if (!isset($requests[$event->subject_id])) {
                    $requests[$event->subject_id] = [
                        'event' => $event->subject->event->name,
                    ];
                }
                if (!isset($requests[$event->subject_id]['sent'])) {
                    $requests[$event->subject_id]['sent'] = 0;
                }
                $requests[$event->subject_id]['sent']++;
            }
        }

        if (isset($this->loggedEvents['bid:receive'])) {
            foreach ($this->loggedEvents['bid:receive'] as $event) {
                if (!isset($requests[$event->subject->proposal_request_id])) {
                    $requests[$event->subject->proposal_request_id] = [
                        'event' => $event->subject->proposalRequest->event->name,
                    ];
                }
                if (!isset($requests[$event->subject->proposal_request_id]['received'])) {
                    $requests[$event->subject->proposal_request_id]['received'] = 0;
                }
                $requests[$event->subject->proposal_request_id]['received']++;
            }
        }

        if (isset($this->loggedEvents['proposal:decline'])) {
            foreach ($this->loggedEvents['proposal:decline'] as $event) {
                if (!isset($requests[$event->subject->proposal_request_id])) {
                    $requests[$event->subject->proposal_request_id] = [
                        'event' => $event->subject->proposalRequest->event->name,
                    ];
                }
                if (!isset($requests[$event->subject->proposal_request_id]['declined'])) {
                    $requests[$event->subject->proposal_request_id]['declined'] = 0;
                }
                $requests[$event->subject->proposal_request_id]['declined']++;
            }
        }

        if (isset($this->loggedEvents['proposal:accept'])) {
            foreach ($this->loggedEvents['proposal:accept'] as $event) {
                if (!isset($requests[$event->subject->proposal_request_id])) {
                    $requests[$event->subject->proposal_request_id] = [
                        'event' => $event->subject->proposalRequest->event->name,
                    ];
                }
                if (!isset($requests[$event->subject->proposal_request_id]['accepted'])) {
                    $requests[$event->subject->proposal_request_id]['accepted'] = 0;
                }
                $requests[$event->subject->proposal_request_id]['accepted']++;
            }
        }

        return $requests;
    }
}
