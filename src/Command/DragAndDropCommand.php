<?php

namespace App\Command;

use App\Command\DragAndDrop\MoveCategoryStrategy;
use App\Command\DragAndDrop\MoveProductStrategy;
use App\Command\DragAndDrop\SortCategoryStrategy;
use App\Command\DragAndDrop\SortProductStrategy;
use App\Command\DragAndDrop\Validator;
use App\Enum\DragAndDropInteractionType;
use App\Service\CategoryService;
use App\Service\productService;
use App\Service\StrategyRegistryService;
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
        private readonly StrategyRegistryService $strategyRegistry,
        private readonly Validator $validator,
    ) {
        parent::__construct();
        $this->registerStrategies();
    }

    private function registerStrategies(): void
    {
        $this->strategyRegistry->registerStrategies([
            DragAndDropInteractionType::SORT_CATEGORY->value => new SortCategoryStrategy($this->validator, $this->categoryService),
            DragAndDropInteractionType::SORT_PRODUCT->value  => new SortProductStrategy($this->validator, $this->productService),
            DragAndDropInteractionType::MOVE_CATEGORY->value => new MoveCategoryStrategy($this->validator, $this->categoryService),
            DragAndDropInteractionType::MOVE_PRODUCT->value  => new MoveProductStrategy($this->validator, $this->productService),
        ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $interactionTypes = DragAndDropInteractionType::getValues();

        $selectedChoice = $io->choice('Что будем делать?', $interactionTypes);

        if ($strategy = $this->strategyRegistry->getStrategy($selectedChoice)) {
            $strategy->execute($io);
        } else {
            $io->error('Некорректный выбор.');
            return Command::FAILURE;
        }


//        switch ($selectedChoice) {
//            case DragAndDropInteractionType::SORT_CATEGORY->value:
//                $workCategory = $io->ask('Введите id категории где будет производиться сортировка (оставьте пустым для корневой категории)', validator: $this->validateCategoryId());
//                $selectedCategory = (int) $io->ask('Введите id категории для изменения сортировки', validator: $this->validateCategoryId());
//                $relativeToCategory = (int) $io->ask(
//                    sprintf('Укажите id категории перед/после которой будет размещена выбранная категория %s', $selectedCategory),
//                    validator: $this->validateCategoryId()
//                );
//
//                $position = ElementPosition::getByLabel(
//                    $io->choice(sprintf('Разместить перед категорией %s или после', $relativeToCategory), [
//                        ElementPosition::BEFORE->getLabel(),
//                        ElementPosition::AFTER->getLabel(),
//                    ])
//                );
//
//                try {
//                    $dto = new CategorySortDTO($selectedCategory, $relativeToCategory, $position, $workCategory);
//                    $successful = $this->categoryService->sortingCategory($dto);
//
//                    if (!$successful) {
//                        throw new \RuntimeException('Категория не перемещена');
//                    }
//                } catch (\Exception $e) {
//                    $io->error($e->getMessage());
//                }
//
//                $io->success(sprintf(
//                    "Категория %d перемещена %s категории %d",
//                    $selectedCategory, mb_strtolower($position->value), $relativeToCategory
//                ));
//                break;

//            case DragAndDropInteractionType::SORT_PRODUCT->value:
//                $workCategory = $io->ask('Введите id категории где будет производиться сортировка', validator: $this->validateCategoryId());
//                $selectedProduct = (int) $io->ask('Введите id товара для изменения сортировки', validator: $this->validateProductId());
//                $relativeToProduct = (int) $io->ask(
//                    sprintf('Укажите id товара перед/после которого будет размещен выбранный товар %s', $selectedProduct),
//                    validator: $this->validateProductId()
//                );
//
//                $position = ElementPosition::getByLabel(
//                    $io->choice(sprintf('Разместить перед товаром %s или после?', $relativeToProduct), [
//                        ElementPosition::BEFORE->getLabel(),
//                        ElementPosition::AFTER->getLabel(),
//                    ])
//                );
//
//                try {
//                    $dto = new ProductSortDTO($selectedProduct, $relativeToProduct, $position, $workCategory);
//                    $this->productService->sortingProduct($dto);
//                } catch (\Exception $e) {
//                    $io->error($e->getMessage());
//                }
//
//                $io->success(sprintf(
//                    "Товар %d перемещен %s товара %d",
//                    $selectedProduct, mb_strtolower($position->value), $relativeToProduct
//                ));
//                break;

//            case DragAndDropInteractionType::MOVE_CATEGORY->value:
//                $categoryToMove = $io->ask('Введите id категории, которую нужно переместить (оставьте пустым для корневой категории)');
//                $newParentCategory = $io->ask('Укажите id новой родительской категории');
//                $relativeToCategory = $io->ask(sprintf('Укажите id категории перед/после которой будет перемещена категория %s', $categoryToMove));
//
//                $position = ElementPosition::getByLabel(
//                    $io->choice(sprintf('Переместить перед категорией %s или после?', $relativeToCategory), [
//                        ElementPosition::BEFORE->getLabel(),
//                        ElementPosition::AFTER->getLabel(),
//                    ])
//                );
//
//                try {
//                    $dto = new CategoryMoveDTO($categoryToMove, $newParentCategory, $relativeToCategory, $position);
//                    $this->categoryService->moveCategory($dto);
//                } catch (\Exception $e) {
//                    $io->error($e->getMessage());
//                }
//
//                $io->success(sprintf(
//                    "Категория %d перемещена в категорию %d %s категории %d",
//                    $categoryToMove, $newParentCategory, mb_strtolower($position->value), $relativeToCategory
//                ));
//                break;

//            case DragAndDropInteractionType::MOVE_PRODUCT->value:
//                $productToMove = (int) $io->ask('Введите id товара, который нужно переместить', validator: $this->validateProductId());
//
//                $newCategory = (int) $io->ask('Укажите id новой категории', validator: $this->validateCategoryId());
//                $relativeToProduct = $io->ask(
//                    sprintf('Укажите id товара перед/после которого будет перемещен товар %d', $productToMove),
//                    validator: $this->validateProductId()
//                );
//
//                $position = ElementPosition::getByLabel(
//                    $io->choice(sprintf('Переместить перед товаром %d или после?', $relativeToProduct), [
//                        ElementPosition::BEFORE->getLabel(),
//                        ElementPosition::AFTER->getLabel(),
//                    ])
//                );
//
//                try {
//                    $dto = new ProductMoveDTO($newCategory, $relativeToProduct, $position, $productToMove);
//                    $this->productService->moveProduct($dto);
//                } catch (\Exception $e) {
//                    $io->error($e->getMessage());
//                }
//
//                $io->success(sprintf(
//                    "Товар %d перемещен в категорию %d %s товара %d",
//                    $productToMove, $newCategory, mb_strtolower($position->getLabel()), $relativeToProduct
//                ));
//                break;

//            default:
//                $io->writeln('Некорректный выбор.');
//                return Command::FAILURE;
//        }

        return Command::SUCCESS;
    }

    private function validateCategoryId(): callable
    {
        return static function ($choice) {
            if ($choice === '' || $choice === 'null') {
                $choice = null;
            }

            if (!is_int((int) $choice) || (int) $choice < 0) {
                throw new \RuntimeException('Необходимо ввести id категории');
            }

            return (int) $choice;
        };
    }

    private function validateProductId(): callable
    {
        return static function ($choice) {
            if ($choice === '' || $choice === null || !is_int((int) $choice) || (int) $choice <= 0) {
                throw new \RuntimeException('Необходимо ввести id товара');
            }
            return (int) $choice;
        };
    }
}