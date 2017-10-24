<?php

namespace Butler\Project;

class GithubProject extends AbstractProject
{

    /**
     * create tasks
     */
    public function createTasks() {


        # get vendor and project name
        $this->addTask([
            'key' => 'set github auth token',
            'class' => '\\Butler\\Task\\InputTask',
            'task' => 'question',
            'options' => [
                'githubtoken' => 'Gihub Auth token:'
            ],
        ]);

        $this->addTask([
            'key' => 'github Auth',
            'class' => '\\Butler\\Task\\GithubTask',
            'task' => 'auth',
            'options' => [
                'token' => '{githubtoken}'
            ],
        ]);

        $this->addTask([
            'key' => 'create repository',
            'class' => '\\Butler\\Task\\GithubTask',
            'task' => 'repositoryCreate',
            'options' => [
                'name' => 'butler-test-repo', // string | required
                'description' => 'This is a test :) ', // string | optional
                'homepage' => '', // string | optional
                'public' => false // bool | optional default: false
            ]
        ]);

        $this->addTask([
            'key' => 'remove repository',
            'class' => '\\Butler\\Task\\GithubTask',
            'task' => 'repositoryRemove',
            'options' => [
                'user' => 'freesh', // string | required
                'name' => 'butler-test-repo', // string | required
            ]
        ]);

        return 'tasks created :))';
    }
}

