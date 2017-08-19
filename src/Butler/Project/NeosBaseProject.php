<?php

namespace Butler\Project;

use Butler\Project\ProjectInterface;

class NeosBaseProject implements ProjectInterface
{


    /**
     * init
     */
    public function init() {
        #Task::executeTask('touch mofa.txt');
    }

    /**
     * create tasks
     */
    public function createTasks() {
        #Task::executeTask('echo "Add vendor/package"');

        $task = new \Butler\Task();

        $task->create([
            'key' => 'init',
            'class' => '\\Butler\\Task\\ComposerTask',
            'task' => 'create',
            'options' => [],
        ]);

        return 'tasks created :))';
    }

    /**
     *
     */
    public function executeTasks() {}

}
