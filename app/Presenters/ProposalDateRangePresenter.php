<?php

namespace App\Presenters;

use Carbon\Carbon;

class ProposalDateRangePresenter extends Presenter
{
    /**
     * @return string
     */
    public function status()
    {
        if ((bool)$this->declined_at) {
            return 'declined';
        }

        if ($this->proposal->contracts()->whereEventDateRangeId($this->event_date_range_id)->count() > 0) {
            return 'accepted';
        }

        if ($this->proposal->proposalRequest->cutoff_date->lt(Carbon::today()) ||
            (!empty($this->proposal->honor_bid_until) && $this->proposal->honor_bid_until->lt(Carbon::today()))) {

            return 'expired';
        }

        if ((bool)$this->submitted_at) {
            return 'submitted';
        }

        return 'pending';
    }
}
