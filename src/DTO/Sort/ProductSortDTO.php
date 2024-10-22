<?php

namespace App\DTO\Sort;

use App\Enum\ElementPosition;
use Symfony\Component\Validator\Constraints as Assert;

class ProductSortDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public int $selectedProduct,      // ID товара, который нужно переместить
        #[Assert\NotBlank]
        public int $relativeToProduct,    // ID товара, перед/после которого перемещается
        #[Assert\NotBlank]
        public ElementPosition $position,
        #[Assert\NotBlank]
        public int $categoryId,           // ID категории, в которой идет перемещение
    ) {
    }
}
