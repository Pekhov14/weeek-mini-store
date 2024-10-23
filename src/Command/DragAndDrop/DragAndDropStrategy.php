<?php

namespace App\Command\DragAndDrop;

use Symfony\Component\Console\Style\SymfonyStyle;

interface DragAndDropStrategy
{

    public function execute(SymfonyStyle $io): void;
}