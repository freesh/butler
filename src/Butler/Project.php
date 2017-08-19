<?php

namespace Butler;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Project
{

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var array
     */
    protected $projectConfig = [];



    public function __construct(Array $config, InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->projectConfig = $config;
    }

    /**
     * init
     */
    public function initProject() {

        $this->output->writeln('Init Project:'.$this->projectConfig['type'] );

        $namespace = '\\Butler\\Project\\'.$this->projectConfig['type'].'Project';

        $task = new $namespace();

        $this->output->writeln($task->createTasks());
    }

    /**
     *
     */
    public function executeTasks() {
        #Task::executeTask('echo "Remove vendor/package"');
    }

}
