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
     * @return void
     */
    public function init(array $config) {
        $this->execute( 'git init' );
    }


    /**
     * @param array $config
     * @return void
     */
    public function add(array $config) {
        $this->execute( 'git add ' . (!isset($config['options']['files'])? '*' : implode(' ', $config['options']['files'])) );
    }


    /**
     * @param array $config
     * @return void
     */
    public function ignore(array $config) {
        $cmds = "";

        if (!$this->fs->exists('.gitignore')) {

            // iterate filepaths
            foreach ($config['options']['files'] as $cmd) {
                $cmds .= $cmd . " \n";
            }
            // dump to file
            $this->fs->dumpFile( '.gitignore', $cmds );

        } else {

            $cmds .= "\n";

            // load file
            $file = array_map('trim', file('.gitignore', FILE_IGNORE_NEW_LINES));

            // get new unique rows
            $unique_files = array_diff($config['options']['files'], $file);

            // iterate filepaths
            foreach ($unique_files as $cmd) {
                $cmds .= $cmd . "\n";
            }

            // append to file
            $this->fs->appendToFile('.gitignore', $cmds );
        }

    }


    /**
     * @param array $config
     * @return void
     */
    public function unignore(array $config) {
        $cmds = "";

        if ($this->fs->exists('.gitignore')) {

            // load file
            $file = array_map('trim', file('.gitignore', FILE_IGNORE_NEW_LINES));

            // get not matching rows
            $filepaths = array_diff($file, $config['options']['files']);

            // iterate filepaths
            foreach ($filepaths as $cmd) {
                $cmds .= $cmd . "\n";
            }

            // dump to file
            $this->fs->dumpFile( '.gitignore', $cmds );

        }
    }


    /**
     * overwrites existing value with new value in .gitignore file
     * @param array $config
     * @return void
     */
    public function ignoreEdit(array $config) {
        $cmds = "";

        if ($this->fs->exists('.gitignore')) {

            // load file
            $file = array_map('trim', file('.gitignore', FILE_IGNORE_NEW_LINES));

            // iterate replaces
            foreach ($config['options']['replaces'] as $old => $new) {

                // find in existing rows and replace
                if ( $key = array_search($old, $file)) {
                    $file[$key] = $new;
                }
            }

            // iterate filepaths
            foreach ($file as $cmd) {
                $cmds .= $cmd . "\n";
            }

            // dump to file
            $this->fs->dumpFile( '.gitignore', $cmds );

        }
    }


    /**
     * @param array $config
     * @return void
     */
    public function commit(array $config) {
        $this->execute( 'git commit -m "' . $config['options']['message'] .'"' );
    }


    /**
     * @param array $config
     * @return void
     */
    public function remoteAdd(array $config) {
        $this->execute( 'git remote add '
            . (!isset($config['options']['origin'])? 'origin' : $config['options']['origin'])
            .' '
            . $config['options']['url']
        );
    }


    /**
     * @param array $config
     * @return void
     */
    public function push(array $config) {
        $this->execute( 'git push '
            . (!isset($config['options']['params'])? '' : implode(' ', $config['options']['params']))
            .' ' . (!isset($config['options']['origin'])? 'origin' : $config['options']['origin'])
            .' ' . $config['options']['branch']
        );
    }


    /**
     * @param array $config
     * @return void
     */
    public function pull(array $config) {
        $this->execute( 'git pull '
            . (!isset($config['options']['origin'])? 'origin' : $config['options']['origin'])
            .' ' . $config['options']['branch']
        );
    }



}
