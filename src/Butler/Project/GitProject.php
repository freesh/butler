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

        $this->addTask([
            'key' => 'commit',
            'class' => '\\Butler\\Task\\GitTask',
            'task' => 'commit',
            'options' => [
                'message' => 'initial commit'
            ],
        ]);

        $this->addTask([
            'key' => 'add remote',
            'class' => '\\Butler\\Task\\GitTask',
            'task' => 'remoteAdd',
            'options' => [
                'origin' => 'origin', // optional | string default: origin
                'url' => 'git@github.com:freesh/butlertest.git'
            ],
        ]);

        $this->addTask([
            'key' => 'push',
            'class' => '\\Butler\\Task\\GitTask',
            'task' => 'push',
            'options' => [
                'params' => [ // optional | string
                    '-u'
                ],
                'origin' => 'origin', // optional | string default: origin
                'branch' => 'master'
            ],
        ]);

        $this->addTask([
            'key' => 'pull',
            'class' => '\\Butler\\Task\\GitTask',
            'task' => 'pull',
            'options' => [
                'origin' => 'origin', // optional | string default: origin
                'branch' => 'master'
            ],
        ]);

        return 'tasks created :))';
    }
}

