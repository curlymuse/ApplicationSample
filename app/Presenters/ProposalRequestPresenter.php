<?php

namespace App\Presenters;

class ProposalRequestPresenter extends Presenter
{
    /**
     * @return string
     */
    public function stage()
    {
        if ($this->entity->proposals()->whereHas('contracts')->exists()) {
            return 'contract';
        }

        if ($this->entity->proposals()->whereNotNull('submitted_at')->count() > 0 || $this->is_disbursed()) {
            return 'proposal';
        }

        return 'request';
    }

    public function is_disbursed()
    {
        return $this->entity->requestHotels()
            ->whereHas('users', function($query) {
                $query->whereNotNull('request_hotel_user.contact_initiated_at');
            })->exists();
    }
}
