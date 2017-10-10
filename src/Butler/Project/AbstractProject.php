<?php

namespace Butler\Project;

use Butler\Project\ProjectInterface;

abstract class AbstractProject implements ProjectInterface
{

    /**
     * @var array
     */
    protected $tasks = [];

    /**
     * @var array
     */
    protected $config = [];


    /**
     * AbstractProject constructor.
     * @param array $config
     */
    function __construct(array $config)
    {
        $this->config = $config;
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
            return true;
        }
        return false;
    }

    /**
     * get all tasks‚
     * @param $config
     */
    public function getTasks()
    {
        // return just tasks are configured to execute by --task="... 1" --task="... 2"
        if (!empty($this->config['tasks']))
        {
            return array_intersect_key($this->tasks, array_flip($this->config['tasks']));
        }

        // return all configured project tasks
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

    protected function executeTasks()
    {

    }
}
