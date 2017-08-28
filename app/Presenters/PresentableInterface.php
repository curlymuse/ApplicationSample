<?php

namespace App\Presenters;

interface PresentableInterface
{
    /**
     * @return \App\Presenters\Presenter
     */
    public function present();
}