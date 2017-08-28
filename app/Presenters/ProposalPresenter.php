<?php

namespace App\Presenters;

use Carbon\Carbon;

class ProposalPresenter extends Presenter
{
    /**
     * @return string
     */
    public function status()
    {
        if (count($this->contracts) > 0) {
            return 'accepted';
        }

        if (Carbon::today()->gt($this->proposalRequest->cutoff_date) ||
            (!empty($this->honor_bid_until) && Carbon::today()->gt($this->honor_bid_until))) {

            return 'expired';
        }

        if ($this->entity->dateRanges()->whereNotNull('declined_at')->count() == count($this->entity->dateRanges)) {
            return 'declined';
        }

        if ($this->entity->dateRanges()->whereNotNull('submitted_at')->exists()) {
            return 'submitted';
        }

        return 'pending';
    }
}