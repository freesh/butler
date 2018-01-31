<?php
namespace Butler\Task;

use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class FindTask extends AbstractTask
{

    /**
     * @var \Butler\Helper\FilesystemHelper
     */
    protected $fileSystem;

    /**
     * FindTask constructor.
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
    public function replaceInFile(array $config)
    {
        // load content
        if ($this->fileSystem->exists($this->fileSystem->getPath($config['options']['filename']))) {
            $content = file_get_contents($this->fileSystem->getPath($config['options']['filename']));
        } else {
            $this->output->writeln('<error><options=bold;bg=red>  ERR </></error> <fg=red>"find:replaceInFile" File "' . $this->fileSystem->getPath($config['options']['filename']) .' not found.</>');
            return;
        }
        // replace
        foreach ($config['options']['replaces'] as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }
        // dump file
        $this->fileSystem->dumpFile($this->fileSystem->getPath($config['options']['filename']), $content);
    }
}
