<?php
namespace Butler\Command;

use Symfony\Component\Finder\Finder;

use Butler\Helper\YamlHelper;
use Butler\Helper\FilesystemHelper;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends Command
{
    /**
     * @var OutputInterface
     */
    protected $output;
    /**
     * @var InputInterface
     */
    protected $input;
    /**
     * @var \Butler\Helper\FilesystemHelper
     */
    protected $fileSystem;
    /**
     * @var boolean
     */
    protected $info = false;

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->fileSystem = new FilesystemHelper();
    }


    protected function configure()
    {
        $this->setName('project:list');
        $this->setAliases(['l']);
        $this->setDescription('List Tasks.');
        $this->addOption('projectPath', null, InputOption::VALUE_REQUIRED, 'Alternative path to project.yaml directory', []);
        $this->addOption('info', 'i', InputOption::VALUE_NONE, 'Print path and file informations');
    }


    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->input = $input;
        if (!empty($input->getOption('projectPath'))) {
            $localButlerPath = $input->getOption('projectPath');
        } else {
            $localButlerPath = '~/Butler/Project/';
        }

        if (!empty($input->getOption('info'))) {
            $this->info = true;
        }

        $this->printProjectNames($this->fileSystem->getPath($localButlerPath), 'Local');
        $this->printProjectNames(dirname(__FILE__).'/../Project/', 'Default');
    }

    /**
     * Printing a list of project names, found in given path
     *
     * @param String $path
     * @param String $name
     */
    private function printProjectNames(String $path, String $name)
    {
        $finder = new Finder();
        $finder->files()->name('/\.yaml$/');
        $this->output->writeln('');
        $this->output->writeln('<fg=green;options=bold>'.$name.':</>');
        foreach (iterator_to_array($finder->in($path), false) as $file) {
            $this->output->writeln(
                '  <options=bold>'
                . $this->yamlFileNameToCommandName($file->getRelativePathname())
                . '</>'
                . ($this->info ? '  <fg=blue>('.$file->getRealPath().')</>' : '')
            );
        }
    }

    /**
     * Transforms a yaml file name to a lowercase string without file ending (.yaml)
     *
     * @param $string
     * @return string
     */
    private function yamlFileNameToCommandName($string)
    {
        return strtolower(
            preg_replace(
                '/\B([A-Z])/',
                '-$1',
                substr($string, 0, -5)
            )
        );
    }

    /**
     * transform a string with "-" as word separator to uppercamelcase classname string
     *
     * @param $string
     * @return mixed
     */
    private function stringToClassName($string)
    {
        return
            str_replace('-', '', ucwords($string, '-'));
    }

    /**
     * Load yaml file and parse to array
     *
     * @param $filepath
     * @return mixed
     */
    private function loadConfigYamlFile($filepath)
    {
        if (!file_exists($filepath)) {
            throw new \Exception('Project config file could not be loaded! "'.$filepath.'"');
        }
        return YamlHelper::parse(file_get_contents($filepath));
    }

    /**
     * Output recursive array path in one line per value
     *
     * @param $varname
     * @param $varval
     */
    public function recursivePrint($varname, $varval)
    {
        if (! is_array($varval)) {
            $this->output->writeln("<fg=red>" . $varname . ": </> = " . $varval);
        } else {
            foreach ($varval as $key => $val) {
                $this->recursivePrint($varname . "." . $key, $val);
            }
        }
    }

    /**
     * Replace ~ in $path with the absolute user path
     *
     * @param $path
     * @return mixed
     */
    private function getLocalButlerPath($path)
    {
        if (function_exists('posix_getuid') && strpos($path, '~') !== false) {
            $userInfo = posix_getpwuid(posix_getuid());
            return str_replace('~', $userInfo['dir'], $path);
        }
        return $path;
    }
}
