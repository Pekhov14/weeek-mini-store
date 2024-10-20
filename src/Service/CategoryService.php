<?php

namespace App\Service;

use App\Repository\CategoryRepository;

class CategoryService
{
    private const int MAX_DEPTH = 6;

    public function __construct(
        private readonly CategoryRepository $categoryRepository
    ) {}

    public function getCategoryTree(): array
    {
        $categories = $this->categoryRepository->findCategoriesWithProducts();
        $groupedCategories = $this->groupCategoriesByParent($categories);
        return $this->buildCategoryTree($groupedCategories);
    }

    private function groupCategoriesByParent(array $categories): array
    {
        $grouped = [];

        foreach ($categories as $category) {
            $parentId = $category->getParent() ? $category->getParent()->getId() : null;
            $grouped[$parentId][] = $category;
        }

        return $grouped;
    }

    private function buildCategoryTree(array $groupedCategories, ?int $parentId = null, int $currentDepth = 0): array
    {
        $branch = [];
        if (!isset($groupedCategories[$parentId])) {
            return $branch;
        }

        foreach ($groupedCategories[$parentId] as $category) {
            $products = [];
            foreach ($category->getProducts() as $product) {
                $products[] = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
                ];
            }

            $branch[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'sort' => $category->getSort(),
                'parent' => $category->getParent() ? $category->getParent()->getId() : null,
                'products' => $products,
                'children' => ($currentDepth <  self::MAX_DEPTH)
                    ? $this->buildCategoryTree($groupedCategories, $category->getId(), $currentDepth + 1)
                    : [],
            ];
        }

        return $branch;
    }
}