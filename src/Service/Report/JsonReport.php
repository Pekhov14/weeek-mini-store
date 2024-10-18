<?php

namespace App\Service\Report;

class JsonReport implements ReportFormat
{
    public function generate(array $data): string
    {
        return json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }
}