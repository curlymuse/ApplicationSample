<?php

namespace App\Presenters;

class EventLocationPresenter extends Presenter
{
    /**
     * Get a prettified version of the city for display
     *
     * @return string
     */
    public function city()
    {
        $locationString = '';

        if ($this->name != $this->locality) {
            $locationString .= sprintf('%s, %s, ', $this->name, $this->locality);
        } else {
            $locationString .= sprintf('%s, ', $this->locality);
        }

        if ($this->country == 'USA') {
            $locationString .= $this->administrative_area_level_1;
        } else {
            $locationString .= $this->country;
        }

        return $locationString;
    }
}