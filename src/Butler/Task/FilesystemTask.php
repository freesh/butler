<?php
namespace Butler\Task;

use Butler\Helper\FilesystemHelper;

class FilesystemTask extends AbstractTask
{

    protected $fs;


    /**
     * @param array $config
     */
    public function create(array $config) {
        $this->fs = $this->helperSet->get('filesystem');
        $this->fs->createFile('test.txt');

    }

}
