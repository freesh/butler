<?php
namespace Butler\Task;

use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GitTask extends AbstractTask
{

    /**
     * @var \Butler\Helper\FilesystemHelper
     */
    protected $fileSystem;

    /**
     * GitTask constructor.
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
     * @return void
     */
    public function init(array $config)
    {
        $this->execute('git init');
    }


    /**
     * @param array $config
     * @return void
     */
    public function add(array $config)
    {
        $this->execute('git add ' . (!isset($config['options']['files'])? '.' : implode(' ', $config['options']['files'])));
    }


    /**
     * @param array $config
     * @return void
     */
    public function ignore(array $config)
    {
        $lines = "";
        if (!$this->fileSystem->exists($this->fileSystem->getPath('.gitignore'))) {
            // iterate filepaths
            foreach ($config['options']['files'] as $line) {
                $lines .= $line . " \n";
            }
            // dump to file
            $this->fileSystem->dumpFile($this->fileSystem->getPath('.gitignore'), $lines);
        } else {
            $lines .= "\n";
            // load file
            $file = array_map('trim', file($this->fileSystem->getPath('.gitignore'), FILE_IGNORE_NEW_LINES));
            // get new unique rows
            $unique_files = array_diff($config['options']['files'], $file);
            // iterate filepaths
            foreach ($unique_files as $cmd) {
                $lines .= $cmd . "\n";
            }
            // append to file
            $this->fileSystem->appendToFile($this->fileSystem->getPath('.gitignore'), $lines);
        }
    }


    /**
     * @param array $config
     * @return void
     */
    public function unignore(array $config)
    {
        $lines = "";
        if ($this->fileSystem->exists($this->fileSystem->getPath('.gitignore'))) {
            // load file
            $file = array_map('trim', file($this->fileSystem->getPath('.gitignore'), FILE_IGNORE_NEW_LINES));
            // get not matching rows
            $filepaths = array_diff($file, $config['options']['files']);
            // iterate filepaths
            foreach ($filepaths as $cmd) {
                $lines .= $cmd . "\n";
            }
            // dump to file
            $this->fileSystem->dumpFile($this->fileSystem->getPath('.gitignore'), $lines);
        }
    }


    /**
     * overwrites existing value with new value in .gitignore file
     * @param array $config
     * @return void
     */
    public function ignoreEdit(array $config)
    {
        $lines = "";
        if ($this->fileSystem->exists($this->fileSystem->getPath('.gitignore'))) {
            // load file
            $file = array_map('trim', file($this->fileSystem->getPath('.gitignore'), FILE_IGNORE_NEW_LINES));
            // iterate replaces
            foreach ($config['options']['replaces'] as $old => $new) {
                // find in existing rows and replace
                if ($key = array_search($old, $file)) {
                    $file[$key] = $new;
                }
            }
            // iterate filepaths
            foreach ($file as $cmd) {
                $lines .= $cmd . "\n";
            }
            // dump to file
            $this->fileSystem->dumpFile($this->fileSystem->getPath('.gitignore'), $lines);
        }
    }


    /**
     * @param array $config
     * @return void
     */
    public function commit(array $config)
    {
        $this->execute('git commit -m "' . $config['options']['message'] .'"');
    }


    /**
     * @param array $config
     * @return void
     */
    public function remoteAdd(array $config)
    {
        $this->execute(
            'git remote add '
            . (!isset($config['options']['origin'])? 'origin' : $config['options']['origin'])
            .' '
            . (isset($config['options']['url'])? $config['options']['url'] : $this->setQuestion('<options=bold;bg=cyan>  ASK </> <fg=cyan>Please add your git remote url: </> ', null))
        );
    }


    /**
     * @param array $config
     * @return void
     */
    public function push(array $config)
    {
        $this->execute(
            'git push '
            . (!isset($config['options']['params'])? '' : implode(' ', $config['options']['params']))
            .' ' . (!isset($config['options']['origin'])? 'origin' : $config['options']['origin'])
            .' ' . $config['options']['branch']
        );
    }


    /**
     * @param array $config
     * @return void
     */
    public function pull(array $config)
    {
        $this->execute(
            'git pull '
            . (!isset($config['options']['origin'])? 'origin' : $config['options']['origin'])
            .' ' . $config['options']['branch']
        );
    }
}
