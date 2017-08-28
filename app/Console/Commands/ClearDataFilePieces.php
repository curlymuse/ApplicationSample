<?php

namespace App\Console\Commands;

use App\Support\Translator\ArnPropertyDataSet;
use Illuminate\Console\Command;

class ClearDataFilePieces extends Command
{
    const REGEX_PIECE_FILE = '/^[a-zA-Z]+_part[0-9]+\.txt$/';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translator:clear-pieces';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear data file pieces.';

    /**
     * @var ArnPropertyDataSet
     */
    private $dataSet;

    /**
     * Create a new command instance.
     *
     * @param ArnPropertyDataSet $dataSet
     */
    public function __construct(ArnPropertyDataSet $dataSet)
    {
        parent::__construct();

        $this->dataSet = $dataSet;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $i = 0;
        $handle = @opendir($this->dataSet->getStorageDir());
        if ($handle) {
            while (($file = readdir($handle)) !== false) {
                if (preg_match(self::REGEX_PIECE_FILE, $file)) {
                    $i++;
                    $filename = $this->dataSet->getStorageDir() . $file;
                    unlink($filename);
                }
            }
        }

        $this->info(
            sprintf(
                '%d files deleted.',
                $i
            )
        );
    }
}
