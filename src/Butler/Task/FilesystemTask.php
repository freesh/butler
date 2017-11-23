<?php
namespace Butler\Task;

use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class FilesystemTask extends AbstractTask
{

    /**
     * @var \Butler\Helper\FilesystemHelper
     */
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
    public function copy(array $config)
    {
        $this->fs->copy(
            $config['options']['originFile'],
            $config['options']['targetFile'],
            (isset($config['options']['overwriteNewerFiles']) ? $config['options']['overwriteNewerFiles'] : false)
        );
    }

    /**
     * @param array $config
     */
    public function mkdir(array $config)
    {
        $this->fs->mkdir(
            $config['options']['dirs'],
            (isset($config['options']['mode']) ? $config['options']['mode'] : 0777)
        );
    }

    /**
     * @param array $config
     * @return mixed
     */
    public function exists(array $config)
    {
        return $this->fs->exists($config['options']['files']);
    }

    /**
     * @param array $config
     */
    public function touch(array $config)
    {
        $this->fs->touch(
            $config['options']['files'],
            (isset($config['options']['time']) ? $config['options']['time'] : null),
            (isset($config['options']['atime']) ? $config['options']['atime'] : null)
        );
    }


    /**
     * @param array $config
     */
    public function remove(array $config)
    {
        $this->fs->remove($config['options']['files']);
    }


    /**
     * @param array $config
     */
    public function chmod(array $config)
    {
        $this->fs->chmod(
            $config['options']['files'],
            $config['options']['mode'],
            (isset($config['options']['umask']) ? $config['options']['umask'] : 0000),
            (isset($config['options']['recursive']) ? $config['options']['recursive'] : false)
        );
    }


    /**
     * @param array $config
     */
    public function chown(array $config)
    {
        $this->fs->chown(
            $config['options']['files'],
            $config['options']['user'],
            (isset($config['options']['recursive']) ? $config['options']['recursive'] : false)
        );
    }

    /**
     * @param array $config
     */
    public function chgrp(array $config)
    {
        $this->fs->chgrp(
            $config['options']['files'],
            $config['options']['group'],
            (isset($config['options']['recursive']) ? $config['options']['recursive'] : false)
        );
    }

    /**
     * @param array $config
     */
    public function rename(array $config)
    {
        $this->fs->rename(
            $config['options']['origin'],
            $config['options']['target'],
            (isset($config['options']['overwrite']) ? $config['options']['overwrite'] : false)
        );
    }

    /**
     * @param array $config
     */
    public function symlink(array $config)
    {
        $this->fs->symlink(
            $config['options']['originDir'],
            $config['options']['targetDir'],
            (isset($config['options']['copyOnWindows']) ? $config['options']['copyOnWindows'] : false)
        );
    }

    /**
     * @param array $config
     */
    public function hardlink(array $config)
    {
        $this->fs->hardlink(
            $config['options']['originFile'],
            $config['options']['targetFiles']
        );
    }

    /**
     * ToDo: how to use returned value?
     * Resolves links in paths.
     *
     * @param array   $config
     *
     * @return string|null
     */
    public function readlink(array $config)
    {
        return $this->fs->readlink(
            $config['options']['path'],
            (isset($config['options']['canonicalize']) ? $config['options']['canonicalize'] : false)
        );
    }

    /**
     * ToDo: how to use returned value?
     * Given an existing path, convert it to a path relative to a given starting path.
     *
     * @param array $config
     *
     * @return string Path of target relative to starting path
     */
    public function makePathRelative(array $config)
    {
        return $this->fs->makePathRelative(
            $config['options']['endPath'],
            $config['options']['startPath']
        );
    }

    /**
     * Mirrors a directory to another.
     *
     * @param array $config
     *
     * @throws IOException When file type is unknown
     */
    public function mirror(array $config)
    {
        $this->fs->mirror(
            $config['options']['originDir'],
            $config['options']['targetDir'],
            (isset($config['options']['iterator']) ? $config['options']['iterator'] : null),
            (isset($config['options']['options']) ? $config['options']['options'] : array())
        );
    }

    /**
     * ToDo: how to use returned value?
     * Returns whether the file path is an absolute path.
     *
     * @param array $config
     *
     * @return bool
     */
    public function isAbsolutePath(array $config)
    {
        return $this->fs->isAbsolutePath(
            $config['options']['file']
        );
    }

    /**
     * ToDo: how to use returned value?
     * Creates a temporary file with support for custom stream wrappers.
     *
     * @param array $config
     *
     * @return string The new temporary filename (with path), or throw an exception on failure
     */
    public function tempnam(array $config)
    {
        return $this->fs->tempnam(
            $config['options']['dir'],
            $config['options']['prefix']
        );
    }

    /**
     * Atomically dumps content into a file.
     *
     * @param array $config
     *
     * @throws IOException If the file cannot be written to
     */
    public function dumpFile(array $config)
    {
        $this->fs->dumpFile(
            $config['options']['filename'],
            $config['options']['content']
        );
    }

    /**
     * Appends content to an existing file.

     * @param array $config
     *
     * @throws IOException If the file is not writable
     */
    public function appendToFile(array $config)
    {
        $this->fs->appendToFile(
            $config['options']['filename'],
            $config['options']['content']
        );
    }
}
