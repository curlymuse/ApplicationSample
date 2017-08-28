<?php

namespace App\Support\Translator;

interface TranslatorDriver
{
    /**
     * Sync the parsed data
     *
     * @param PropertyDataSet $set
     *
     * @return $this
     */
    public function sync(PropertyDataSet $set);
}