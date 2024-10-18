<?php

namespace App\Controller;

use App\DTO\ReportOrdersDto;
use App\Enum\ReportStatus;
use App\Message\GenerateOrdersReportMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class ReportController extends AbstractController
{
    public function __construct(private readonly MessageBusInterface $bus)
    {
    }

    # TODO: Можно было бы сделать так:
    # Маршрут просмотра статуса отчета по ID и типу
    # Маршрут скачивания отчета по ID и типу
    # Маршрут просмотра (получение) отчета по ID и типу отчета

    #[Route('/api/report/orders', name: 'app_report', methods: ['POST'])]
    public function generateOrdersReport(
        #[MapRequestPayload] ReportOrdersDto $reportOrder
    ): JsonResponse
    {
        $message = new GenerateOrdersReportMessage($reportOrder);
        $this->bus->dispatch($message);

        return $this->json([
            'reportID' => $message->reportId,
            'status' => ReportStatus::accepted->value,
        ],Response::HTTP_ACCEPTED);
    }
}
