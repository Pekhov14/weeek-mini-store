<?php

namespace App\Enum;

enum ElementPosition: string
{
    case BEFORE = 'before';
    case AFTER = 'after';

    public  function getLabel(): string
    {
        return match ($this) {
            self::BEFORE => 'Перед',
            self::AFTER => 'После',
        };
    }

    public static function getByLabel(string $label): self
    {
        return match ($label) {
            'Перед' => self::BEFORE,
            'После' => self::AFTER,
        };
    }
}
