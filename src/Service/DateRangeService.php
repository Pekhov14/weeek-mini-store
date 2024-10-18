<?php

namespace App\Service;

use App\Enum\ReportTemplate;
use App\ValueObject\DateInterval;

class DateRangeService
{
    public function getDateRange(ReportTemplate $template): DateInterval
    {
        switch ($template) {
            case ReportTemplate::LAST_MONTH:
                $start = (new \DateTimeImmutable('first day of last month'))->setTime(0, 0);
                $end = (new \DateTimeImmutable('last day of last month'))->setTime(23, 59, 59);
                break;
            default:
                throw new \InvalidArgumentException('Unknown report template.');
        }

        return new DateInterval($start, $end);
    }
}