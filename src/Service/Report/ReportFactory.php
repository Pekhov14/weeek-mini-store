<?php

namespace App\Service\Report;

use InvalidArgumentException;

class ReportFactory
{
    public static function create(string $format): ReportFormat
    {
        return match ($format) {
            'json' => new JsonReport(),
            'csv' => new CsvReport(),
            default => throw new InvalidArgumentException("Unsupported format: $format"),
        };
    }
}