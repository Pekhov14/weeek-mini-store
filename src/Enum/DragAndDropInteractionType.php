<?php

namespace App\Enum;

enum DragAndDropInteractionType: string
{
    case SORT_CATEGORY = 'Сортировать категорию';
    case SORT_PRODUCT  = 'Сортировать товар';
    case MOVE_CATEGORY = 'Перенести категорию';
    case MOVE_PRODUCT  = 'Перенести товар';

    public static function getValues(): array
    {
        return array_map(static fn($fileType) => $fileType->value, self::cases());
    }
}
