<?php

namespace App\ValueObject;

use DateTimeImmutable;

readonly class DateInterval
{
    public function __construct(public DateTimeImmutable $start, public DateTimeImmutable $end)
    {
    }
}