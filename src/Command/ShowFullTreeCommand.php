<?php

namespace App\Command;

use App\Repository\CategoryRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:show-full-tree',
    description: 'Tree of all categories and subcategories with products',
)]
class ShowFullTreeCommand extends Command
{
    private const int MAX_DEPTH = 6;

    public function __construct(
        private readonly CategoryRepository $categoryRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $categories = $this->categoryRepository->findCategoriesWithProducts();

        $groupedCategories = $this->groupCategoriesByParent($categories);
        $categoryTree = $this->buildCategoryTree($groupedCategories);

        $this->printCategoryTreeConsole($io, $categoryTree);

        return Command::SUCCESS;
    }

    private function printCategoryTreeConsole(SymfonyStyle $io, array $categories, $prefix = '', $isLast = true): void
    {
        $childPrefix = $isLast ? '    ' : '│   ';

        foreach ($categories as $index => $category) {
            $isCategoryLast = $index === array_key_last($categories);
            $currentPrefix = $isLast ? '└── ' : '├── ';

            $io->writeln(sprintf('%s <fg=green>%s</>', $prefix . $currentPrefix, $category['name']));


            if (!empty($category['products'])) {
                foreach ($category['products'] as $productIndex => $product) {
                    $isProductLast = $productIndex === array_key_last($category['products']);
                    $productPrefix = $isProductLast ? '└── ' : '├── ';

                    $message = $prefix . $childPrefix . $productPrefix;
                    $product = $product['name'] . ' (Цена: ' . $product['price'] . ')';

                    $io->writeln(sprintf('%s <fg=#c0392b>%s</>', $message, $product));
                }
            }

            if (!empty($category['children'])) {
                $newPrefix = $prefix . $childPrefix;
                $this->printCategoryTreeConsole($io, $category['children'], $newPrefix, $isCategoryLast);
            }
        }
    }

    private function groupCategoriesByParent($categories): array
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
                'children' => ($currentDepth < self::MAX_DEPTH)
                    ? $this->buildCategoryTree($groupedCategories, $category->getId(), $currentDepth + 1)
                    : [],
            ];
        }

        return $branch;
    }
}
