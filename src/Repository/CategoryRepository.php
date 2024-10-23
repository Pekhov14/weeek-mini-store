<?php

namespace App\Repository;

use App\DTO\Sort\CategorySortDTO;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    //    /**
    //     * @return Category[] Returns an array of Category objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Category
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findCategoriesWithProducts(): array
    {
        return $this->createQueryBuilder('c')
                    ->leftJoin('c.products', 'p')
                    ->addSelect('p')
                    ->orderBy('c.parent', 'DESC')
                    ->addOrderBy('c.sort', 'ASC')
                    ->getQuery()
                    ->getResult();
    }

    public function sortingCategory(CategorySortDTO $sortingCategoryDTO): true | \RuntimeException
    {
        $selectedCategoryId = $sortingCategoryDTO->selectedCategory;
        $relativeToCategoryId = $sortingCategoryDTO->relativeToCategory;
        $categoryId = $sortingCategoryDTO->categoryId;
        $position = $sortingCategoryDTO->position->value;

        // Получаем все подкатегории в данной категории, отсортированные по полю sort
        $qb = $this->createQueryBuilder('c')
                   ->where('c.parent = :categoryId')
                   ->setParameter('categoryId', $categoryId)
                   ->orderBy('c.sort', 'ASC')
                   ->getQuery();

        $categories = $qb->getResult();

        // Найти текущие позиции выбранной и целевой категорий
        $selectedCategory = null;
        $relativeToCategory = null;

        foreach ($categories as $category) {
            if ($category->getId() === $selectedCategoryId) {
                $selectedCategory = $category;
            } elseif ($category->getId() === $relativeToCategoryId) {
                $relativeToCategory = $category;
            }
        }

        if (!$selectedCategory || !$relativeToCategory) {
            throw new \RuntimeException('Невозможно найти выбранную или целевую категорию.');
        }

        $selectedSort = $selectedCategory->getSort();
        $relativeToSort = $relativeToCategory->getSort();

        // Определяем новый индекс для перемещаемой категории
        $newSort = $position === 'before' ? $relativeToSort : $relativeToSort + 1;

        // Если перемещаем категорию "вверх"
        if ($selectedSort > $newSort) {
            // Увеличиваем sort для всех категорий между новой позицией и старой позицией на 1
            $qb = $this->createQueryBuilder('c')
                       ->update(Category::class, 'c')
                       ->set('c.sort', 'c.sort + 1')
                       ->where('c.parent = :categoryId')
                       ->andWhere('c.sort >= :newSort')
                       ->andWhere('c.sort < :selectedSort')
                       ->setParameter('categoryId', $categoryId)
                       ->setParameter('newSort', $newSort)
                       ->setParameter('selectedSort', $selectedSort)
                       ->getQuery();

            $qb->execute();
        }

        // Если перемещаем категорию "вниз"
        else {
            // Уменьшаем sort для всех категорий между старой позицией и новой позицией на 1
            $qb = $this->createQueryBuilder('c')
                       ->update(Category::class, 'c')
                       ->set('c.sort', 'c.sort - 1')
                       ->where('c.parent = :categoryId')
                       ->andWhere('c.sort > :selectedSort')
                       ->andWhere('c.sort <= :newSort')
                       ->setParameter('categoryId', $categoryId)
                       ->setParameter('selectedSort', $selectedSort)
                       ->setParameter('newSort', $newSort)
                       ->getQuery();

            $qb->execute();
        }

        // Устанавливаем новый сорт для перемещаемой категории
        $selectedCategory->setSort($newSort);

        $this->getEntityManager()->persist($selectedCategory);
        $this->getEntityManager()->flush();

        return true;
//        $this->_em->persist($selectedCategory);
//        $this->_em->flush();
    }
}
