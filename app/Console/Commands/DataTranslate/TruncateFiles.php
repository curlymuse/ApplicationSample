<?php

namespace App\Console\Commands\DataTranslate;

use Illuminate\Console\Command;

class TruncateFiles extends Command
{
    private static $files = [
        'PropertyActive.txt',
        'PropertyAmenity.txt',
        'PropertyDescription.txt',
        'PropertyImage.txt',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translator:truncate {--size=100} {--file=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate large files and create smaller ones.';

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
        foreach (self::$files as $file) {
            $contents = file_get_contents(
                sprintf(
                    '%s/../storage/data/ARN/%s',
                    app_path(),
                    $file
                ),
                false,
                null,
                null,
                10000000
            );
            $rows = explode("\r\n", $contents);
            $truncated = collect($rows)->take($this->option('size'))->toArray();

            $newFilename = sprintf(
                '%s/../storage/data/ARN/%s_truncated.%s',
                app_path(),
                explode('.', $file)[0],
                explode('.', $file)[1]
            );

            file_put_contents(
                $newFilename,
                implode("\r\n", $truncated)
            );
        }
    }
}
