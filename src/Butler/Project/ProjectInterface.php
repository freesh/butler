<?php

namespace Butler\Project;

interface ProjectInterface
{

    /**
     * init
     */
    public function init();

    /**
     * create tasks
     */
    public function createTasks();

    /**
     * execute tasks
     */
    public function executeTasks();

}
