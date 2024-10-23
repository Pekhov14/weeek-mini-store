<?php

namespace App\Service;

class StrategyRegistryService
{
    private array $strategies = [];

    public function registerStrategy(string $interactionType, object $strategy): void
    {
        $this->strategies[$interactionType] = $strategy;
    }

    public function getStrategy(string $interactionType): ?object
    {
        return $this->strategies[$interactionType] ?? null;
    }

    public function registerStrategies(array $strategies): array
    {
        foreach ($strategies as $interactionType => $strategy) {
            $this->registerStrategy($interactionType, $strategy);
        }
        return $this->strategies;
    }
}