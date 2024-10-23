<?php

namespace App\Command\DragAndDrop;

use App\DTO\Sort\ProductSortDTO;
use App\Enum\ElementPosition;
use App\Service\ProductService;
use Symfony\Component\Console\Style\SymfonyStyle;

readonly class SortProductStrategy implements DragAndDropStrategy
{
    public function __construct(
        private Validator       $validator,
        private ProductService  $productService,
    ) {}


    public function execute(SymfonyStyle $io): void
    {
        $workCategory = $io->ask(
            'Введите id категории где будет производиться сортировка',
            validator: $this->validator->validateCategoryId()
        );
        $selectedProduct = (int) $io->ask(
            'Введите id товара для изменения сортировки',
            validator: $this->validator->validateProductId()
        );
        $relativeToProduct = (int) $io->ask(
            sprintf('Укажите id товара перед/после которого будет размещен выбранный товар %s', $selectedProduct),
            validator: $this->validator->validateProductId()
        );

        $position = ElementPosition::getByLabel(
            $io->choice(sprintf('Разместить перед товаром %s или после?', $relativeToProduct), [
                ElementPosition::BEFORE->getLabel(),
                ElementPosition::AFTER->getLabel(),
            ])
        );

        try {
            $dto = new ProductSortDTO($selectedProduct, $relativeToProduct, $position, $workCategory);
            $this->productService->sortingProduct($dto);
        } catch (\Exception $e) {
            $io->error($e->getMessage());
        }

        $io->success(sprintf(
            "Товар %d перемещен %s товара %d",
            $selectedProduct, mb_strtolower($position->value), $relativeToProduct
        ));
    }
}