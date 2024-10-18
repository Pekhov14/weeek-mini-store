<?php

namespace App\MessageHandler;

use App\Message\GenerateOrdersReportMessage;
use App\Service\Report\OrdersReportService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class GenerateOrdersReportHandler
{
    public function __construct(
        private OrdersReportService $ordersReportService,
    ) {}

    public function __invoke(GenerateOrdersReportMessage $message): string
    {
        return $this->ordersReportService->generateOrdersReport($message);
    }
}