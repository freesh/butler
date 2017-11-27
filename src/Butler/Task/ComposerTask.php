<?php
namespace Butler\Task;

use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class ComposerTask extends AbstractTask
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
     * Execute composer create in a temp folder, mv all up and delete temp folder if command is ready
     *
     * @param array $config
     */
    public function create(array $config)
    {
        $this->execute(
            'composer create-project '
            . (!isset($config['options']['params']) ? '' : implode(' ', $config['options']['params']))
            .' '. $config['options']['distribution']
            .' '.$config['options']['tempPath']
        );
        $this->fileSystem->mirror($config['options']['tempPath'], './');
        $this->fileSystem->remove($config['options']['tempPath']);
    }


    /**
     * Add packages
     *
     * @param array $config
     */
    public function add(array $config)
    {
        $this->execute(
            'composer require '
            . (!isset($config['options']['params'])? '' : implode(' ', $config['options']['params']))
            .' '. $config['options']['package']
        );
    }


    /**
     * Remove packages
     *
     * @param array $config
     */
    public function remove(array $config)
    {
        $this->execute('composer remove '.$config['options']['package']);
    }
}
