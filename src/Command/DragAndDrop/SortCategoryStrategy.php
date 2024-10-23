<?php

namespace App\Command\DragAndDrop;

use App\DTO\Sort\CategorySortDTO;
use App\Enum\ElementPosition;
use App\Service\CategoryService;
use Symfony\Component\Console\Style\SymfonyStyle;

readonly class SortCategoryStrategy implements DragAndDropStrategy
{

    public function __construct(
        private Validator       $validator,
        private CategoryService $categoryService,
    ) {}

    public function execute(SymfonyStyle $io): void
    {
        $workCategory = $io->ask(
            'Введите id категории где будет производиться сортировка (оставьте пустым для корневой категории)',
            validator: $this->validator->validateCategoryId()
        );
        $selectedCategory = $io->ask(
            'Введите id категории для изменения сортировки',
            validator: $this->validator->validateCategoryId()
        );
        $relativeToCategory = (int) $io->ask(
            sprintf('Укажите id категории перед/после которой будет размещена выбранная категория %s', $selectedCategory),
            validator: $this->validator->validateCategoryId()
        );

        $position = ElementPosition::getByLabel(
            $io->choice(sprintf('Разместить перед категорией %s или после', $relativeToCategory), [
                ElementPosition::BEFORE->getLabel(),
                ElementPosition::AFTER->getLabel(),
            ])
        );

        try {
            $dto = new CategorySortDTO($selectedCategory, $relativeToCategory, $position, $workCategory);
            $successful = $this->categoryService->sortingCategory($dto);

            if (!$successful) {
                throw new \RuntimeException('Категория не перемещена');
            }
        } catch (\Exception $e) {
            $io->error($e->getMessage());
        }

        $io->success(sprintf(
            "Категория %d перемещена %s категории %d",
            $selectedCategory, mb_strtolower($position->value), $relativeToCategory
        ));
    }
}