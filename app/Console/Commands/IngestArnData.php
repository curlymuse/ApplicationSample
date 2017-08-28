<?php

namespace App\Console\Commands;

use App\Mail\Admin\ArnIngestCompleted;
use App\Support\Translator\ArnPropertyDataSet;
use App\Support\Translator\Translator;
use Illuminate\Console\Command;
use Mail;

class IngestArnData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translator:ingest-arn';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ingest locally stored ARN data.';

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
        $this->info(
            sprintf(
                '[%s] Are you ready for this? It\'s gonna be awesome!',
                date('Y-m-d H:i:s')
            )
        );

        //  48 hours
        set_time_limit(172800);
        ini_set('memory_limit', '1024M');

        app(Translator::class)->using('arn')->ingest(
            (new ArnPropertyDataSet())->pull()
        );

        $this->info(
            sprintf(
                '[%s] Whew! All done. 10 trillion hotels added to the database.',
                date('Y-m-d H:i:s')
            )
        );

        Mail::to(['robin.arenson@gmail.com','nate.ritter@resbeat.com'])
            ->send(
                new ArnIngestCompleted()
            );
    }
}
