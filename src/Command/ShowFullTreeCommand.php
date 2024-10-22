<?php

namespace App\Command;

use App\Service\CategoryService;
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
    public function __construct(
        private readonly CategoryService $categoryService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $categoryTree = $this->categoryService->getCategoryTree();
        $this->printCategoryTreeConsole($io, $categoryTree);

        return Command::SUCCESS;
    }

    private function printCategoryTreeConsole(SymfonyStyle $io, array $categories, $prefix = '', $isLast = true): void
    {
        $childPrefix = $isLast ? '    ' : '│   ';

        foreach ($categories as $index => $category) {
            $isCategoryLast = $index === array_key_last($categories);
            $currentPrefix = $isLast ? '└── ' : '├── ';

            $io->writeln(sprintf(
                '%s <fg=green>%s</> [id: %s, sort: %s]',
                $prefix . $currentPrefix, $category['name'], $category['id'], $category['sort']
            ));


            if (!empty($category['products'])) {
                foreach ($category['products'] as $productIndex => $product) {
                    $isProductLast = $productIndex === array_key_last($category['products']);
                    $productPrefix = $isProductLast ? '└── ' : '├── ';

                    $message = $prefix . $childPrefix . $productPrefix;
                    $product = sprintf(
                        '%s (Цена: %s) [id: %s, sort: %s]',
                        $product['name'], $product['price'], $product['id'], $product['sort']
                    );

                    $io->writeln(sprintf('%s <fg=#c0392b>%s</>', $message, $product));
                }
            }

            if (!empty($category['children'])) {
                $newPrefix = $prefix . $childPrefix;
                $this->printCategoryTreeConsole($io, $category['children'], $newPrefix, $isCategoryLast);
            }
        }
    }
}
