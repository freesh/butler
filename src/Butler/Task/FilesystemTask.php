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
    protected $fileSystem;

    /**
     * FilesystemTask constructor.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param HelperSet $helperSet
     */
    public function __construct(InputInterface $input, OutputInterface $output, HelperSet $helperSet)
    {
        parent::__construct($input, $output, $helperSet);
        $this->fileSystem = $this->helperSet->get('filesystem'); // init filesystem helper
    }

    /**
     * @param array $config
     * task config:
     *
     */
    public function copy(array $config)
    {
        $this->fileSystem->copy(
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
        $this->fileSystem->mkdir(
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
        return [
            'filesexists' => $this->fileSystem->exists($config['options']['files'])
        ];
    }

    /**
     * @param array $config
     */
    public function touch(array $config)
    {
        $this->fileSystem->touch(
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
        $this->fileSystem->remove($config['options']['files']);
    }


    /**
     * @param array $config
     */
    public function chmod(array $config)
    {
        $this->fileSystem->chmod(
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
        $this->fileSystem->chown(
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
        $this->fileSystem->chgrp(
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
        $this->fileSystem->rename(
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
        $this->fileSystem->symlink(
            $config['options']['origin'],
            $config['options']['target'],
            (isset($config['options']['copyOnWindows']) ? $config['options']['copyOnWindows'] : false)
        );
    }

    /**
     * @param array $config
     */
    public function hardlink(array $config)
    {
        $this->fileSystem->hardlink(
            $config['options']['originFile'],
            $config['options']['targetFiles']
        );
    }

    /**
     * ToDo: how to use returned value?
     * Resolves links in paths.
     *
     * @param array   $config
     * @return string|null
     */
    public function readlink(array $config)
    {
        return [
            'readlink' => $this->fileSystem->readlink(
                $config['options']['path'],
                (isset($config['options']['canonicalize']) ? $config['options']['canonicalize'] : false)
            )
        ];
    }

    /**
     * ToDo: how to use returned value?
     * Given an existing path, convert it to a path relative to a given starting path.
     *
     * @param array $config
     * @return array Path of target relative to starting path
     */
    public function makePathRelative(array $config)
    {
        return [
            'makePathRelative' => $this->fileSystem->makePathRelative($config['options']['endPath'], $config['options']['startPath'])
        ];
    }

    /**
     * Mirrors a directory to another.
     *
     * @param array $config
     * @throws IOException When file type is unknown
     */
    public function mirror(array $config)
    {
        $this->fileSystem->mirror(
            $config['options']['originDir'],
            $config['options']['targetDir'],
            null,
            (isset($config['options']['options']) ? $config['options']['options'] : array())
        );
    }

    /**
     * Moves a directory.
     *
     * @param array $config
     * @throws IOException When file type is unknown
     * @taskOptions:
     *  originDir: 'my/dir' # string
     *  targetDir: 'my/new/dir' # string
     *  iterator: null
     *  options:
     *    override: false
     *    copy_on_windows: true
     *    delete: true
     */
    public function move(array $config)
    {
        if (!$this->fileSystem->move(
            $config['options']['origin'],
            $config['options']['target'],
            null,
            (isset($config['options']['options']) ? $config['options']['options'] : array())
        )) {
            $this->output->writeln('<error><options=bold;bg=red>  ERR </></error>' .'<fg=red>"filesystem:move" Target "' . $this->fileSystem->getPath($config['options']['target']) . '" could not created!</>');
        }
    }

    /**
     * ToDo: how to use returned value?
     * Returns whether the file path is an absolute path.
     *
     * @param array $config
     * @return bool
     */
    public function isAbsolutePath(array $config)
    {
        return [
            'isAbsolutePath' => $this->fileSystem->isAbsolutePath($config['options']['file'])
        ];
    }

    /**
     * ToDo: how to use returned value?
     * Creates a temporary file with support for custom stream wrappers.
     *
     * @param array $config
     * @return string The new temporary filename (with path), or throw an exception on failure
     */
    public function tempnam(array $config)
    {
        return [
            'tempnam' => $this->fileSystem->tempnam(
                $config['options']['dir'],
                $config['options']['prefix']
            )
        ];
    }

    /**
     * Atomically dumps content into a file.
     *
     * @param array $config
     * @throws IOException If the file cannot be written to
     */
    public function dumpFile(array $config)
    {
        if (is_array($config['options']['content'])) {
            $content = '';
            foreach ($config['options']['content'] as $line) {
                $content .= $line."\n";
            }
            $config['options']['content'] = $content;
        }
        $this->fileSystem->dumpFile(
            $config['options']['file'],
            $config['options']['content']
        );
    }

    /**
     * Appends content to an existing file.
     *
     * @param array $config
     * @throws IOException If the file is not writable
     */
    public function appendToFile(array $config)
    {
        $this->fileSystem->appendToFile(
            $this->fileSystem->getPath($config['options']['filename']),
            $config['options']['content']
        );
    }
}
