<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CompileApiDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-docs:compile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compile API documentation HTML based on files in docs/api';

    /**
     * Set of API doc sets to compile
     *
     * @var array
     */
    protected $apis = [
        'licensee',
        'hotel',
        'client',
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $urls = [];
        foreach ($this->apis as $key) {
            exec(
                sprintf(
                    'aglio --theme-variables slate -i docs/api/apis/%s/_api.apib -o resources/views/api-docs/%s.blade.php',
                    $key,
                    $key
                )
            );
            $urls[] = sprintf('%s/api/%s', config('url'), $key);
        }

        return $this->info(sprintf('API documents compiled successfully. View them at %s', implode(', ', $urls)));
    }
}
