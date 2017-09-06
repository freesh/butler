<?php

namespace Butler\Project;

class FilesystemProject extends AbstractProject
{

    /**
     * create tasks
     */
    public function createTasks() {

        // init docker
        $this->addTask([
            'key' => 'touch file',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'touch',
            'options' => [
                'files' => 'test.txt', // string|array|\Traversable A filename, an array of files, or a \Traversable instance to create
                'time' => null, // (optional) int The touch time as a Unix timestamp
                'atime' => null // (optional) int The access time as a Unix timestamp
            ],
        ]);

        $this->addTask([
            'key' => 'copy file',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'copy',
            'options' => [
                'originFile' => 'test.txt', // string
                'targetFile' => 'copy.txt', // string
                'overwriteNewerFiles' => false // (optional) bool (default: false)
            ],
        ]);

        /**
         * ToDo: how to use this task? What to retun?
         */
        $this->addTask([
            'key' => 'exists',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'exists',
            'options' => [
                'files' => 'test.txt', // string|array|\Traversable A filename, an array of files, or a \Traversable instance to check
                'condition' => '', // ToDo: exist oe !exist
                'action' => '' // ToDo: what to do if condition is true?
            ],
        ]);

        $this->addTask([
            'key' => 'mkdir',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'mkdir',
            'options' => [
                'dirs' => 'testDir', // string|array|\Traversable $dirs The directory path
                'mode' => 0755 // (optional) int (default: 0777)
            ],
        ]);

        $this->addTask([
            'key' => 'remove',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'remove',
            'options' => [
                'files' => 'testDir', // string|array|\Traversable A filename, an array of files, or a \Traversable instance to remove
            ],
        ]);

        $this->addTask([
            'key' => 'mkdir lala',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'mkdir',
            'options' => [
                'dirs' => 'lala/ohohoh/huhuhu', // string|array|\Traversable $dirs The directory path
                'mode' => 0755 // (optional) int (default: 0777)
            ],
        ]);

        $this->addTask([
            'key' => 'chmod',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'chmod',
            'options' => [
                'files' => 'lala', // string|array|\Traversable A filename, an array of files, or a \Traversable instance to change mode
                'mode' => 0777, // int The new mode (octal)
                'umask' => 0000, // (optional) int The mode mask (octal) (default: 0000)
                'recursive' => true // (optional) bool Whether change the mod recursively or not (default: false)
            ],
        ]);
    }
}
