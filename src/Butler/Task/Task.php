<?php
namespace Butler\Task;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;



abstract class Task
{
    public function __construct()
    {

    }

    protected static function executeTask($command) {
        $process = new Process($command);
        $process->setTimeout(600);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }
}