<?php

namespace App\DTO\Move;
use App\Enum\ElementPosition;
use Symfony\Component\Validator\Constraints as Assert;

class ProductMoveDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public int $newCategoryId,         // ID новой категории
        #[Assert\NotBlank]
        public int $relativeToCategory,    // ID товара, перед/после которого перемещается
        #[Assert\NotBlank]
        public ElementPosition $position,
        #[Assert\NotBlank]
        public int $productId,           // ID продукта, в который нужно переместить
    ) {
    }
}