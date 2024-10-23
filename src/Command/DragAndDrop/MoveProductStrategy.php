<?php

namespace App\Command\DragAndDrop;

use App\DTO\Move\ProductMoveDTO;
use App\Enum\ElementPosition;
use App\Service\ProductService;
use Symfony\Component\Console\Style\SymfonyStyle;

readonly class MoveProductStrategy implements DragAndDropStrategy
{
    public function __construct(
        private Validator       $validator,
        private ProductService  $productService,
    ) {}

    public function execute(SymfonyStyle $io): void
    {
        $productToMove = (int) $io->ask(
            'Введите id товара, который нужно переместить',
            validator: $this->validator->validateProductId()
        );
        $newCategory = (int) $io->ask(
            'Укажите id новой категории',
            validator: $this->validator->validateCategoryId()
        );
        $relativeToProduct = $io->ask(
            sprintf('Укажите id товара перед/после которого будет перемещен товар %d', $productToMove),
            validator: $this->validator->validateProductId()
        );

        $position = ElementPosition::getByLabel(
            $io->choice(sprintf('Переместить перед товаром %d или после?', $relativeToProduct), [
                ElementPosition::BEFORE->getLabel(),
                ElementPosition::AFTER->getLabel(),
            ])
        );

        try {
            $dto = new ProductMoveDTO($newCategory, $relativeToProduct, $position, $productToMove);
            $this->productService->moveProduct($dto);
        } catch (\Exception $e) {
            $io->error($e->getMessage());
        }

        $io->success(sprintf(
            "Товар %d перемещен в категорию %d %s товара %d",
            $productToMove, $newCategory, mb_strtolower($position->getLabel()), $relativeToProduct
        ));
    }
}