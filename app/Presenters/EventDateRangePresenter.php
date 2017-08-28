<?php

namespace App\Presenters;

class EventDateRangePresenter extends Presenter
{
    /**
     * @return string
     */
    public function range()
    {
        $firstDateFormat = ($this->start_date->format('Y') == $this->end_date->format('Y')) ? 'M. d' : 'M. d, Y';

        return sprintf(
            '%s - %s',
            $this->start_date->format($firstDateFormat),
            $this->end_date->format('M. d, Y')
        );
    }
}