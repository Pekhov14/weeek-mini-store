<?php

namespace App\DTO\Sort;

use App\Enum\ElementPosition;
use Symfony\Component\Validator\Constraints as Assert;

class CategorySortDTO
{
    public function __construct(
        #[Assert\NotBlank(allowNull: true)]
        public int $selectedCategory,      // ID категории, которую нужно переместить
        #[Assert\NotBlank]
        public int $relativeToCategory,    // ID категории, перед/после которой перемещается
        #[Assert\NotBlank]
        public ElementPosition $position,
        #[Assert\NotBlank(allowNull: true)]
        public ?int $categoryId,           // ID категории, в которой идет перемещение (если категория в корне то null)
    ) {
    }
}