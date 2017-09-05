<?php
namespace Butler\Task;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class FilesystemTask extends AbstractTask
{

    protected $fs;


    /**
     * @param array $config
     */
    public function touch(array $config) {
        $this->fs = $this->helperSet->get('filesystem');
        $this->fs->touch('test.txt');
    }

}
