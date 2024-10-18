<?php

namespace App\Enum;

enum ReportStatus: string
{
    case accepted = 'accepted';
    case generated = 'generated';
    case processing = 'processing';
    case failed = 'failed';
}
