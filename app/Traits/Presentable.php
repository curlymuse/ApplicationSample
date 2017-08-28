<?php

namespace App\Traits;

use App\Exceptions\PresenterException;

trait Presentable
{
    /**
     * @var
     */
    protected static $presenterInstance;

    /**
     * @return \App\Presenters\Presenter
     *
     * @throws PresenterException
     */
    public function present()
    {
        if (! $this->presenter || ! class_exists($this->presenter)) {
            throw new PresenterException('Please set $presenter property of Model class.');
        }

        if (! isset(static::$presenterInstance)) {
            static::$presenterInstance = new $this->presenter($this);
        }

        return new $this->presenter($this);
    }
}