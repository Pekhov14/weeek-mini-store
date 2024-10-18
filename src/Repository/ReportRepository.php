<?php

namespace App\Repository;

use App\Entity\Report;
use App\Enum\ReportFileType;
use App\Enum\ReportStatus;
use App\ValueObject\DateInterval;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\Uid\UuidV4;

/**
 * @extends ServiceEntityRepository<Report>
 */
class ReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly Connection $connection)
    {
        parent::__construct($registry, Report::class);
    }

    //    /**
    //     * @return Report[] Returns an array of Report objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Report
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function save(UuidV4 $reportId, ReportStatus $status, ReportFileType $fileType): void
    {
        $report = new Report();
        $report->setId($reportId);
        $report->setStatus($status->value);
        $report->setFileType($fileType->value);

        $this->getEntityManager()->persist($report);
        $this->getEntityManager()->flush();
    }

    public function getOrdersCountByDay(DateInterval $interval): array
    {
        $sql = '
            SELECT DATE(o.created_at) as orderDate, COUNT(o.id) as totalOrders
            FROM "order" o
            WHERE o.created_at BETWEEN :start AND :end
            GROUP BY orderDate
            ORDER BY orderDate
        ';

        try {
            $stmt = $this->connection->executeQuery($sql, [
                'start' => $interval->start->format('Y-m-d H:i:s'),
                'end' => $interval->end->format('Y-m-d H:i:s')
            ]);

            return $stmt->fetchAllAssociative();
        } catch (Exception $e) {
            throw new \RuntimeException('Error fetching orders count: ' . $e->getMessage());
        }
    }
}
