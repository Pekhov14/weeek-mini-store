<?php

namespace App\Service\Report;

use App\Enum\ReportFileType;
use App\Enum\ReportStatus;
use App\Message\GenerateOrdersReportMessage;
use App\Repository\ReportRepository;
use App\Service\DateRangeService;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Uid\UuidV4;

readonly class OrdersReportService
{
    public function __construct(
        private DateRangeService $dateRangeService,
        private ParameterBagInterface $parameterBag,
        private ReportRepository $reportRepository,
        private LoggerInterface $logger
    ) {
    }

    public function generateOrdersReport(GenerateOrdersReportMessage $reportData): string
    {
        $ordersReportInterval = $this->dateRangeService->getDateRange($reportData->reportOrder->preparedDate);

        $fileType = $reportData->reportOrder->fileType->value;
        $reportId = $reportData->reportId;

        try {
            $data = $this->reportRepository->getOrdersCountByDay($ordersReportInterval);
        } catch (\RuntimeException $e) {
            $this->logger->error('Failed to generate report: ' . $e->getMessage());
            $this->reportRepository->save($reportId, ReportStatus::failed, ReportFileType::from($fileType));

            return 'report_generation_failed ' . $e->getMessage();
        }

        $report  = ReportFactory::create($fileType);
        $content = $report->generate($data);

        $publicDir = $this->parameterBag->get('orders_reports_directory');
        $filePath = $publicDir . $reportId . '.' . strtolower($fileType);

        if (!is_dir($publicDir)) {
            mkdir($publicDir, 0755, true);
        }

        file_put_contents($filePath, $content);

        $this->reportRepository->save(UuidV4::fromString($reportId), ReportStatus::generated, ReportFileType::from($fileType));

        return $reportId;
    }
}