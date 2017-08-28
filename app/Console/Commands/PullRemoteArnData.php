<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Support\Translator\ArnPropertyDataSet;

class PullRemoteArnData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translator:pull-arn';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download all ARN files to local storage';

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
        set_time_limit(7200);

        $set = app(ArnPropertyDataSet::class);

        $files = [
            'Amenity',
            'AmenityAttribute',
            'AmenityType',
            'Attribute',
            'Brand',
            'PropertyAmenity',
            'PropertyDescription',
            'PropertyImage',
            'PropertyType',
            'PropertyActive',
        ];

        $this->info(
            sprintf(
                'BEGIN: Copying %d files from ARN server',
                count($files)
            )
        );

        foreach ($files as $i => $file) {
            $url = $set->getRemoteIndexUrl($file);
            $local = $set->getForeignIndexFile($file);
            $this->info(
                sprintf(
                    '[%s] [%d/%d] STARTING: %s from %s',
                    date('Y-m-d H:i:s'),
                    $i + 1,
                    count($files),
                    $file,
                    $url
                )
            );
            copy($url, $local);
            $this->info(
                sprintf(
                    '[%s] [%d/%d] DONE: Successfully copied %s from %s',
                    date('Y-m-d H:i:s'),
                    $i + 1,
                    count($files),
                    $file,
                    $url
                )
            );
        }
    }
}
