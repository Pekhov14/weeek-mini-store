<?php

namespace App\Message;

use App\DTO\ReportOrdersDto;
use Symfony\Component\Uid\Uuid;

readonly class GenerateOrdersReportMessage
{
    public string $reportId;

    public function __construct(
        public ReportOrdersDto $reportOrder,
    ) {
        $this->reportId = Uuid::v4();
    }
}