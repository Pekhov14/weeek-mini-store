<?php

namespace App\Enum;

enum ReportFileType: string
{
    case JSON = 'json';
    case CSV = 'csv';

    public static function getValues(): array
    {
        return array_map(static fn($fileType) => $fileType->value, self::cases());
    }
}
