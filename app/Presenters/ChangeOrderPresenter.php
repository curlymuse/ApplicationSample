<?php

namespace App\Presenters;

use Carbon\Carbon;

class ChangeOrderPresenter extends Presenter
{
    /**
     * @return string
     */
    public function responded_at()
    {
        $firstDeclined = $this->entity->children()
            ->whereNotNull('declined_at')
            ->first();

        if ($firstDeclined) {
            return $firstDeclined->declined_at;
        }

        $firstAccepted = $this->entity->children()
            ->whereNotNull('accepted_at')
            ->first();

        if ($firstAccepted) {
            return $firstAccepted->accepted_at;
        }

        return null;
    }

    /**
     * @return int
     */
    public function responded_by_user()
    {
        $firstDeclined = $this->entity->children()
            ->whereNotNull('declined_by_user')
            ->first();

        if ($firstDeclined) {
            return $firstDeclined->declinedByUser;
        }

        $firstAccepted = $this->entity->children()
            ->whereNotNull('accepted_by_user')
            ->first();

        if ($firstAccepted) {
            return $firstAccepted->acceptedByUser;
        }

        return null;
    }
}