<?php

namespace App\Console\Commands;

use App\Support\Translator\ArnPropertyDataSet;
use Illuminate\Console\Command;

class SplitDataFiles extends Command
{
    /**
     * How many lines to pull for each file
     *
     * @const int
     */
    const LINES_PER_FILE = 5000;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translator:split-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Split data files into manageable parts.';

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
        set_time_limit(1200);
        ini_set('memory_limit', '1024M');

        $files = [
            $this->dataSet->getPropertyIndexFile(),
            $this->dataSet->getForeignIndexFile('PropertyDescription'),
            $this->dataSet->getForeignIndexFile('PropertyImage'),
            $this->dataSet->getForeignIndexFile('PropertyAmenity'),
        ];
        foreach ($files as $filename) {
            $handle = @fopen($filename, 'r');
            if (! $handle) {
                continue;
            }
            $currentFileIndex = 0;
            $currentLineIndex = 1;
            $currentFileData = '';
            $keyLine = '';
            while (($buffer = stream_get_line($handle, 4096, "\r\n")) !== false) {
                $currentFileData .= $buffer . "\r\n";
                if ($currentLineIndex == 1) {
                    $keyLine = $buffer;
                }
                if ($currentLineIndex++ % self::LINES_PER_FILE == 0) {
                    $fileInfo = explode('.txt', $filename);
                    $saveToFilename = sprintf(
                        '%s_part%d.txt',
                        $fileInfo[0],
                        $currentFileIndex
                    );
                    file_put_contents($saveToFilename, $currentFileData);
                    $currentFileData = $keyLine . "\r\n";
                    $currentFileIndex++;
                }
            }

            //  Save the last part
            $fileInfo = explode('.txt', $filename);
            $saveToFilename = sprintf(
                '%s_part%d.txt',
                $fileInfo[0],
                $currentFileIndex
            );
            file_put_contents($saveToFilename, $currentFileData);

            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        }
    }
}
