<?php

namespace Butler\Project;

class GitProject extends AbstractProject
{

    /**
     * create tasks
     */
    public function createTasks() {


        $this->addTask([
            'key' => 'init',
            'class' => '\\Butler\\Task\\GitTask',
            'task' => 'init',
            'options' => [

            ]
        ]);

        $this->addTask([
            'key' => 'ignoreEdit',
            'class' => '\\Butler\\Task\\GitTask',
            'task' => 'ignoreEdit',
            'options' => [
                'replaces' => [
                    '/Packages/' => '/Packages/*'
                ]
            ],
        ]);


        $this->addTask([
            'key' => 'ignore',
            'class' => '\\Butler\\Task\\GitTask',
            'task' => 'ignore',
            'options' => [
                'files' => [
                    'Build/*',
                    '!/Build/Docker/',
                    '!/Packages/Sites/'
                ]
            ],
        ]);

        $this->addTask([
            'key' => 'unignore',
            'class' => '\\Butler\\Task\\GitTask',
            'task' => 'unignore',
            'options' => [
                'files' => [
                    'Build/',
                    'Readme.rst'
                ]
            ],
        ]);



        $this->addTask([
            'key' => 'add',
            'class' => '\\Butler\\Task\\GitTask',
            'task' => 'add',
            'options' => [
                'files' => [ // optional | array of file path
                    '*',
                    '.gitignore'
                ]
            ],
        ]);

        return 'tasks created :))';
    }
}

