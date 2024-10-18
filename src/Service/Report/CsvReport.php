<?php

namespace App\Service\Report;

class CsvReport implements ReportFormat
{
    public function generate(array $data): string
    {
        $output = fopen('php://temp', 'rb+');
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}