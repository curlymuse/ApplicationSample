<?php

namespace App\Support;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class CSVToJsonParser
{

    /**
     * @var string
     */
    private $csvData;

    public function parse($file)
    {
        $path = $file->getRealPath();
        $this->csvData = file_get_contents($path);

        $objects = [];
        $rows = explode("\n", $this->csvData);
        $columnHeadingRow = explode(',', $rows[0]);

        foreach ($rows as $i => $row) {
            $rowData = explode(',', $row);
            if ($i == 0) {
                continue;
            }
            $object = (object)[];
            foreach ($columnHeadingRow as $j => $columnName) {
                $object->$columnName = (isset($rowData[$j])) ? $rowData[$j] : '';
            }
            $objects[] = $object;
        }

        return $objects;
    }
}