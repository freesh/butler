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
     */
    public function create(array $config)
    {
        $this->execute('composer create-project '. (!isset($config['options']['params'])? '' : implode(' ', $config['options']['params'])) .' '. $config['options']['distribution'].' '.$config['options']['tempPath']);
        $this->fs->mirror($config['options']['tempPath'], './');
        $this->fs->remove($config['options']['tempPath']);
    }


    /**
     * @param array $config
     */
    public function add(array $config)
    {
        $this->execute('composer require '. (!isset($config['options']['params'])? '' : implode(' ', $config['options']['params'])) .' '. $config['options']['package']);
    }


    /**
     * @param array $config
     */
    public function remove(array $config)
    {
        $this->execute('composer remove '.$config['options']['package']);
    }
}
