<?php

namespace App\Support;

use App\Assemblers\Assembler;
use Illuminate\Contracts\Bus\Dispatcher;

class AssemblerDispatcher
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Dispatch and return the Assembler
     *
     * @param Assembler $assembler
     *
     * @return mixed
     */
    public function assemble(Assembler $assembler)
    {
        return $this->dispatcher->dispatchNow($assembler);
    }
}
