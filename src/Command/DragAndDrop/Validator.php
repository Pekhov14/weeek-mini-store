<?php

namespace App\Command\DragAndDrop;

class Validator
{
    public function validateCategoryId(): callable
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

    public function validateProductId(): callable
    {
        return static function ($choice) {
            if ($choice === '' || $choice === null || !is_int((int) $choice) || (int) $choice <= 0) {
                throw new \RuntimeException('Необходимо ввести id товара');
            }
            return (int) $choice;
        };
    }
}