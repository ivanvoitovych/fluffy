<?php

namespace Fluffy\Domain\CronTab;

class TimerInfo
{
    public function __construct(
        public string $schedule,
        public bool $isPastDue,
        public ?int $lastRun,
        public int $currentRun,
        public int $nextRun,
        public ?int $missedRun
    ) {
    }
}
