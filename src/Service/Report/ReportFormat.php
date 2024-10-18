<?php

namespace App\Service\Report;

interface ReportFormat
{
    public function generate(array $data): string;
}