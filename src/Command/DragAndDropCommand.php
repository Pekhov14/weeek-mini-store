<?php

namespace App\Command;

use App\DTO\Move\ProductMoveDTO;
use App\DTO\Sort\CategorySortDTO;
use App\DTO\Sort\ProductSortDTO;
use App\Enum\DragAndDropInteractionType;
use App\Enum\ElementPosition;
use App\Service\CategoryService;
use App\Service\productService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:drag-and-drop',
    description: 'Add a short description for your command',
)]
class DragAndDropCommand extends Command
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly productService $productService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $before = ElementPosition::BEFORE;
        $after  = ElementPosition::AFTER;

        $selectedChoice = (string)$io->choice('Что будем делать?', DragAndDropInteractionType::getValues());

        switch ($selectedChoice) {
            // Сортировка категории
            case DragAndDropInteractionType::SORT_CATEGORY->value:
                $workCategory = (int) $io->ask('Введите id категории где будет производиться сортировка', null, function ($choice) {
                    if ($choice === '' || !is_numeric($choice)) {
                        throw new \RuntimeException('Необходимо ввести id категории');
                    }
                });

                $selectedCategory = (int) $io->ask('Введите id категории для изменения сортировки', null, function ($choice) {
                    if ($choice === '' || !is_numeric($choice)) {
                        throw new \RuntimeException('Необходимо ввести id категории которую нужно переместить');
                    }
                });

                $relativeToCategory = (int) $io->ask(
                    sprintf('Укажите id категории перед/после которой будет размещена выбранная категория %s', $selectedCategory),
                    null, function ($choice) {
                        if ($choice === '' || !is_numeric($choice)) {
                            throw new \RuntimeException('Необходимо ввести id категории куда нужно переместить');
                        }
                    }
                );

                $position = $io->choice(sprintf('Разместить перед категорией %s или после?', $relativeToCategory), [
                    $before->value => $before->getLabel(),
                    $after->value  => $after->getLabel(),
                ]);

                try {
                    $dto = new CategorySortDTO($selectedCategory, $relativeToCategory, $position, $workCategory);
                    $this->categoryService->sortingCategory($dto);
                } catch (\Exception $e) {
                    $io->error($e->getMessage());
                }

                $io->success("Категория $selectedCategory перемещена $position категории $relativeToCategory.");
                break;

            // Сортировка товара
            case DragAndDropInteractionType::SORT_PRODUCT->value:
                $workCategory = (int) $io->ask('Введите id категории где будет производиться сортировка', null, function ($choice) {
                    if ($choice === '' || !is_numeric($choice)) {
                        throw new \RuntimeException('Необходимо ввести id категории');
                    }
                });

                $selectedProduct = (int) $io->ask('Введите id товара для изменения сортировки', null, function ($choice) {
                    if ($choice === '' || !is_numeric($choice)) {
                        throw new \RuntimeException('Необходимо ввести id товара');
                    }
                });

                $relativeToProduct = (int) $io->ask(
                    sprintf('Укажите id товара перед/после которого будет размещен выбранный товар %s', $selectedProduct),
                    null, function ($choice) {
                        if ($choice === '' || !is_numeric($choice)) {
                            throw new \RuntimeException('Необходимо ввести id товара перед/после которого перемещаем');
                        }
                    }
                );

                $position = $io->choice(sprintf('Разместить перед товаром %s или после?', $relativeToProduct), [
                    $before->value => $before->getLabel(),
                    $after->value  => $after->getLabel(),
                ]);

                try {
                    $dto = new ProductSortDTO($selectedProduct, $relativeToProduct, $position, $workCategory);
                    $this->productService->sortingProduct($dto);
                } catch (\Exception $e) {
                    $io->error($e->getMessage());
                }

                $io->success("Товар $selectedProduct перемещен $position товара $relativeToProduct.");
                break;

            // Перемещение категории
            case DragAndDropInteractionType::MOVE_CATEGORY->value:
                $categoryToMove = $io->ask('Введите id категории, которую нужно переместить');

                $newParentCategory = $io->ask('Укажите id новой родительской категории');

                $relativeToCategory = $io->ask(sprintf('Укажите id категории перед/после которой будет перемещена категория %s', $categoryToMove));

                $position = $io->choice(sprintf('Переместить перед категорией %s или после?', $relativeToCategory), [
                    'before' => 'Перед',
                    'after'  => 'После',
                ]);

                // Логика перемещения категории (например, вызов API)
                $io->success("Категория $categoryToMove перемещена в категорию $newParentCategory $position категории $relativeToCategory.");
                break;

            // Перемещение товара
            case DragAndDropInteractionType::MOVE_PRODUCT->value:
                $productToMove = (int) $io->ask('Введите id товара, который нужно переместить', null, function ($choice) {
                    if ($choice === '' || !is_numeric($choice)) {
                        throw new \RuntimeException('Необходимо ввести id товара');
                    }
                });

                $newCategory = (int) $io->ask('Укажите id новой категории', null, function ($choice) {
                    if ($choice === '' || !is_numeric($choice)) {
                        throw new \RuntimeException('Необходимо ввести id новой категории');
                    }
                });

                $relativeToProduct = (int) $io->ask(
                    sprintf('Укажите id товара перед/после которого будет перемещен товар %s', $productToMove),
                    null, function ($choice) {
                    if ($choice === '' || !is_numeric($choice)) {
                        throw new \RuntimeException('Необходимо ввести id товара перед/после которого перемещаем');
                    }
                }
                );

                $position = $io->choice(sprintf('Переместить перед товаром %s или после?', $relativeToProduct), [
                    $before->value => $before->getLabel(),
                    $after->value  => $after->getLabel(),
                ]);

                try {
                    $dto = new ProductMoveDTO($newCategory, $relativeToProduct, $position, $productToMove);
                    $this->productService->moveProduct($dto);
                } catch (\Exception $e) {
                    $io->error($e->getMessage());
                }

                $io->success("Товар $productToMove перемещен в категорию $newCategory $position товара $relativeToProduct.");
                break;

            default:
                $io->writeln('Некорректный выбор.');
                return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}