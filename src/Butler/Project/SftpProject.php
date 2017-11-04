<?php

namespace Butler\Project;

class SftpProject extends AbstractProject
{

    /**
     * create tasks
     */
    public function createTasks() {


        # get vendor and project name
        /*$this->addTask([
            'key' => 'set github auth token',
            'class' => '\\Butler\\Task\\InputTask',
            'task' => 'question',
            'options' => [
                'ssh-user' => 'SSH User: ',
                'ssh-pwd' => 'SSH Password: ',
            ],
        ]);*/


        # connect to sftp server
        $this->addTask([
            'key' => 'Auth',
            'class' => '\\Butler\\Task\\SftpTask',
            'task' => 'auth',
            'options' => [
                'auth_method' => 'rsa', // string | optional ('rsa', 'userauth') default: userauth
                'rsa_private_file' => '~/.ssh/id_rsa', // string | optional default: ~/.ssh/id_rsa
                'rsa_private_password' => '123456', // string | optional default: ~/.ssh/id_rsa
                'host' => '00.000.000.000', // string
                'port' => '22', // int | optional default: 22
                'timeout' => '10', // int | optional default: 10
                #'username' => '{ssh-user}', //'{ssh-user}', // string
                #'password' => '{ssh-pwd}', //'' // string | optional default: null
            ],
        ]);

        # create folders
        $this->addTask([
            'key' => 'Create folders',
            'class' => '\\Butler\\Task\\SftpTask',
            'task' => 'mkdir',
            'options' => [
                'dir' => [
                    'dirname/s1', // string or array | required relative or absolute path
                    'thisIsATest',
                    'thisIsATesta/mimimi',
                    'thisIsATests',
                    'thisIsATestss'
                ]
            ],
        ]);

        # delete files and folders
        $this->addTask([
            'key' => 'Delete folders',
            'class' => '\\Butler\\Task\\SftpTask',
            'task' => 'delete',
            'options' => [
                'target' => [
                    'thisIsATest',
                    'thisIsATesta'
                ], // string or array | required relative or absolute path
            ],
        ]);

        # delete files and folders
        $this->addTask([
            'key' => 'Create Symlinks',
            'class' => '\\Butler\\Task\\SftpTask',
            'task' => 'symlink',
            'options' => [
                'links' => [ // array | required array with links $key = link $value = target
                    's1' => 'dirname/s1',
                    's2' => 'dirname/s2'
                ],
            ],
        ]);

        # get vendor and project name
        $this->addTask([
            'key' => 'List',
            'class' => '\\Butler\\Task\\SftpTask',
            'task' => 'list',
            'options' => [
                'path' => '.', // string | default: .
            ],
        ]);

        return 'tasks created :))';
    }
}

