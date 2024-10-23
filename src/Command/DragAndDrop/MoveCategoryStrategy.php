<?php

namespace App\Command\DragAndDrop;

use App\DTO\Move\CategoryMoveDTO;
use App\Enum\ElementPosition;
use App\Service\CategoryService;
use Symfony\Component\Console\Style\SymfonyStyle;

readonly class MoveCategoryStrategy implements DragAndDropStrategy
{
    public function __construct(
        private Validator       $validator,
        private CategoryService $categoryService,
    ) {}

    public function execute(SymfonyStyle $io): void
    {
        $categoryToMove = $io->ask(
            'Введите id категории, которую нужно переместить (оставьте пустым для корневой категории)',
            validator: $this->validator->validateCategoryId()
        );
        $newParentCategory = $io->ask(
            'Укажите id новой родительской категории',
            validator: $this->validator->validateCategoryId()
        );
        $relativeToCategory = $io->ask(
            sprintf('Укажите id категории перед/после которой будет перемещена категория %s', $categoryToMove),
            validator: $this->validator->validateCategoryId()
        );

        $position = ElementPosition::getByLabel(
            $io->choice(sprintf('Переместить перед категорией %s или после?', $relativeToCategory), [
                ElementPosition::BEFORE->getLabel(),
                ElementPosition::AFTER->getLabel(),
            ])
        );

        try {
            $dto = new CategoryMoveDTO($categoryToMove, $newParentCategory, $relativeToCategory, $position);
            $this->categoryService->moveCategory($dto);
        } catch (\Exception $e) {
            $io->error($e->getMessage());
        }

        $io->success(sprintf(
            "Категория %d перемещена в категорию %d %s категории %d",
            $categoryToMove, $newParentCategory, mb_strtolower($position->value), $relativeToCategory
        ));
    }
}