<?php

namespace App\DTO;

use App\Enum\ReportFileType;
use App\Enum\ReportTemplate;
use Symfony\Component\Validator\Constraints as Assert;

readonly class ReportOrdersDto
{
    # TODO: Тут могут быть другие параметры, например создать отчет от даты и до даты

    public function __construct(
        #[Assert\NotBlank]
        public ReportTemplate $preparedDate,
        #[Assert\NotBlank]
        public ReportFileType $fileType
    ) {
    }
}