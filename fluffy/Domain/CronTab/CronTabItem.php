<?php

namespace Fluffy\Domain\CronTab;

class CronTabItem
{
    public array $params;
    public TimerInfo $currentRunTimerInfo;
    /**
     * 
     * @param callable|array $task 
     * @param string $schedule 
     * @param bool $runOnStartup
     * @param array $params 
     * @return void 
     */
    public function __construct(public $task, public string $schedule, public bool $runOnStartup = false, array $params = [])
    {
        $this->params = $params;
    }
}
