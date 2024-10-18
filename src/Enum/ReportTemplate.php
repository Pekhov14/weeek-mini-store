<?php

namespace App\Enum;

enum ReportTemplate: string
{
    case LAST_MONTH = 'last_month';

    # TODO: Тут могут быть другие шаблоны отчёта
//    case THIS_MONTH = 'this_month';
//    case LAST_YEAR  = 'last_year';

    public static function getValues(): array
    {
        return array_map(static fn($template) => $template->value, self::cases());
    }
}
