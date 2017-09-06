<?php
namespace Butler\Task;

use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class FilesystemTask extends AbstractTask
{

    protected $fs;

    /**
     * FilesystemTask constructor.
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param HelperSet $helperSet
     */
    public function __construct(InputInterface $input, OutputInterface $output, HelperSet $helperSet)
    {
        parent::__construct($input, $output, $helperSet);
        $this->fs = $this->helperSet->get('filesystem'); // init filesystem helper
    }

    /**
     * @param array $config
     * task config:
     *
     */
    public function copy(array $config) {
        $this->fs->copy(
            $config['options']['originFile'],
            $config['options']['targetFile'],
            (!isset($config['options']['overwriteNewerFiles']) ? true : false)
        );
    }

    /**
     * @param array $config
     */
    public function mkdir(array $config) {
        $this->fs->mkdir(
            $config['options']['dirs'],
            (!isset($config['options']['mode']) ? $config['options']['mode'] : 0777)
        );
    }

    /**
     * @param array $config
     * @return mixed
     */
    public function exists(array $config) {
        return $this->fs->exists($config['options']['files']);
    }

    /**
     * @param array $config
     */
    public function touch(array $config) {
        $this->fs->touch(
            $config['options']['files'],
            (!isset($config['options']['time']) ? $config['options']['time'] : null),
            (!isset($config['options']['atime']) ? $config['options']['atime'] : null)
        );
    }


    /**
     * @param array $config
     */
    public function remove(array $config) {
        $this->fs->remove($config['options']['files']);
    }


    /**
     * @param array $config
     */
    public function chmod(array $config) {
        $this->fs->chmod(
            $config['options']['files'],
            $config['options']['mode'],
            (!isset($config['options']['umask']) ? $config['options']['umask'] : 0000),
            (!isset($config['options']['recursive']) ? $config['options']['recursive'] : false)
        );
    }
}
