<?php

namespace Butler\Project;

class NeosBaseProject extends AbstractProject
{

    /**
     * create tasks
     */
    public function createTasks() {
        #Task::executeTask('echo "Add vendor/package"');

        $this->addTask([
            'key' => 'init',
            'class' => '\\Butler\\Task\\ComposerTask',
            'task' => 'create',
            'options' => [
                'distribution' => 'neos-base'
            ],
        ]);

        $this->addTask([
            'key' => 'init2',
            'class' => '\\Butler\\Task\\ComposerTask',
            'task' => 'create',
            'options' => [
                'distribution' => 'neos-base'
            ],
        ]);


        return 'tasks created :))';
    }

}
