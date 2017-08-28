<?php

namespace App\Mail\Licensee;

use App\Models\Licensee;
use App\Support\MyMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DailySummaryNeeded extends MyMailable
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
            'licensee'      => $this->licensee->company_name,
            'submissions'  => $this->formatProposalData(),
        ];

        return $this->view('emails.licensee.daily-summary')
            ->text('emails.licensee.daily-summary_plain')
            ->from(config('mail.from.address'), $data['licensee'])
            ->subject('Daily Summary')
            ->with($data);
    }

    private function formatProposalData()
    {
        $entries = [];

        if (isset($this->loggedEvents['bid:receive'])) {
            foreach ($this->loggedEvents['bid:receive'] as $event) {
                $entry = [];
                $entry['event'] = $event->subject->proposalRequest->event->name;
                $entry['hotel'] = $event->subject->hotel->name;
                $entry['when'] = $event->created_at->format('M d, Y @ g:i a');

                $entries[] = $entry;
            }
        }

        return $entries;
    }
}
