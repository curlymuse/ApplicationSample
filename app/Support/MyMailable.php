<?php

namespace App\Support;

use Illuminate\Mail\Mailable;

abstract class MyMailable extends Mailable
{
    /**
     * @var mixed
     */
    public $recipient;
}