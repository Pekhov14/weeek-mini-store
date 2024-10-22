<?php

namespace App\DTO\Move;

use App\Enum\ElementPosition;
use Symfony\Component\Validator\Constraints as Assert;

class CategoryMoveDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public int $selectedCategory,       // ID категории, которую нужно переместить
        #[Assert\NotBlank]
        public int $newParentCategory,     // ID нового родительской категории
        #[Assert\NotBlank]
        public int $relativeToCategory,    // ID категории, перед/после которой перемещается
        #[Assert\NotBlank]
        public ElementPosition $position,
    ) {
    }
}