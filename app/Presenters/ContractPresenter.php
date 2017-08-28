<?php

namespace App\Presenters;

use Carbon\Carbon;

class ContractPresenter extends Presenter
{
    /**
     * @return string
     */
    public function status()
    {
        if ((bool)$this->declined_by_hotel_at || (bool)$this->declined_by_owner_at) {
            return 'declined';
        }

        if ((bool)$this->accepted_by_hotel_at && (bool)$this->accepted_by_owner_at) {
            return 'accepted';
        }

        return 'pending';
    }
}