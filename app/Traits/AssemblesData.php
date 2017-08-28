<?php

namespace App\Traits;

use App\Assemblers\Assembler;
use App\Support\AssemblerDispatcher;
use Illuminate\Foundation\Bus\DispatchesJobs;

trait AssemblesData
{
    /**
     * Dispatch
     *
     * @param Assembler $assembler
     *
     * @return Assembler
     */
    protected function assemble(Assembler $assembler)
    {
        return app(AssemblerDispatcher::class)->assemble($assembler);
    }
}
