<?php

namespace Butler\Project;

use Butler\Project\ProjectInterface;

abstract class AbstractProject implements ProjectInterface
{

    /**
     * @var array
     */
    protected $tasks = [];


    function __construct()
    {
        $this->createTasks();
    }


    /**
     * add a task
     * @param array $config
     */
    protected function addTask(array $config)
    {
        if(isset($config['key']))
        {
            $this->tasks[$config['key']] = $config;
        } else
        {
            // exeption: missing task key
            // ToDo: Exeption handling
        }
    }

    /**
     * get all tasks‚
     * @param $config
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * get a task by key
     * @param string $key
     * @return bool|mixed
     */
    public function getTask($key)
    {
        if (isset($this->tasks[$key]))
        {
            return $this->tasks[$key];
        }
        return false;
    }

}
