<?php
namespace Butler\Command;

use Butler\Helper\FilesystemHelper;
use Butler\Helper\YamlHelper;
use Butler\Helper\JsonHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class RunCommand extends Command
{
    /**
     * @var ExpressionLanguage
     */
    protected $expLang;
    /**
     * @var OutputInterface
     */
    protected $output;
    /**
     * @var InputInterface
     */
    protected $input;
    /**
     * Project tasks array
     *
     * @var array
     */
    private $projectTasks = [];
    /**
     * Project runtime config array
     *
     * @var array
     */
    private $projectConfig = [];
    /**
     * @var array
     */
    protected $taskObjects = [];

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->expLang = new ExpressionLanguage();
    }


    protected function configure()
    {
        $this->setName('project:run');
        $this->setAliases(['r']);
        $this->setDescription('Run Tasks.');
        $this->addArgument('project type', InputArgument::REQUIRED);
        $this->addOption('task', 't', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Execute only this task(s)', []);
        $this->addOption('projectPath', null, InputOption::VALUE_REQUIRED, 'Alternative path to project.yaml directory', []);
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
            $localButlerPath = $this->getLocalButlerPath($input->getOption('projectPath'));
        } else {
            $localButlerPath = $this->getLocalButlerPath('~/Butler');
        }

        $this->projectTasks = $this->loadConfigYamlFile(
            $localButlerPath . '/Project/' . $this->stringToClassName(
                $input->getArgument('project type')
            ) . '.yaml'
        );
        $this->reduceTasks($input->getOption('task'));
        $this->createHelperSet([new FilesystemHelper(), new YamlHelper(), new JsonHelper()]);

        foreach ($this->projectTasks as $key => $config) {
            $class = (string)$config['class'];
            $task = (string)$config['task'];
            $projectConf = null;

            if ($this->isConditionTrue($config)) {
                $this->output->writeln(
                    '<fg=green;options=bold>Execute Task:</> <fg=blue>' . $key . '</>'
                    . ($output->isVerbose() ? ' <comment>(' . $class . ' -> ' . $task . ')</comment>' : '')
                );

                // Parse variables in task option array
                $taskOptions = $this->parseTaskConfig((isset($config['options']) ? $config['options'] : []));

                // execute task
                $this->updateProjectConfiguration(
                    $this->executeTask(
                        $class,
                        $task,
                        ['project' => $this->projectConfig, 'options' => $taskOptions]
                    )
                );

                // debug task options or/and runtime config
                if (isset($config['debug']) && $config['debug'] == true) {
                    $this->debug(
                        [
                            'project' => $this->projectConfig, 'options' => $taskOptions
                        ],
                        (isset($config['debug-path']) ? $config['debug-path'] : ''),
                        (isset($config['debug-depth']) ? $config['debug-depth'] : -1),
                        (isset($config['debug-type']) ? $config['debug-type'] : null)
                    );
                }
            }
        }
    }

    /**
     * execute task and return its output
     *
     * @param $class
     * @param $task
     * @param array $config
     * @return mixed
     */
    private function executeTask($class, $task, array $config = [])
    {
        if (!isset($this->taskObjects[$class]) || !$this->taskObjects[$class] instanceof $class) {
            $this->taskObjects[$class] = new $class($this->input, $this->output, $this->getHelperSet());
        }
        // ToDo: Implement a validator, that runs before the first task ist executed
        if (!method_exists($this->taskObjects[$class],$task)) {
            $this->output->writeln('<error><options=bold;bg=red>  ERR </></error> <fg=red>Task "' . $task .'" does not exist in "'.$class.'".</>');
            return null;
        }
        return $this->taskObjects[$class]->$task($config);
    }


    /**
     * Merge new data to existing global project configuration
     *
     * @param array $projectConfiguration
     */
    private function updateProjectConfiguration($projectConfiguration)
    {
        if (is_array($projectConfiguration)) {
            $this->projectConfig = array_merge($this->projectConfig, $projectConfiguration);
        }
    }

    /**
     * Parse task config to replace {variables} with values from project runtime config
     *
     * @param array $taskConfig
     * @return array
     */
    private function parseTaskConfig(array $taskConfig = [])
    {
        array_walk_recursive(
            $taskConfig,
            function (&$val) {
                $matches = null;
                // get variable names from taskConfig string ( declaration: {var1} varname: var1 )
                preg_match_all('/{(.*?)}/', $val, $matches, PREG_SET_ORDER, 0);
                if ($matches) {
                    // iterate over multible matches and replace them if they exists in projectConfig
                    foreach ($matches as $match) {
                        // if path is found and a value is returned
                        if (is_string($param = $this->arrayPathValue($match[1], $this->projectConfig))) {
                            // replace variable string with value
                            $val = str_replace($match[0], $param, $val);
                        }
                    }
                }
            }
        );
        return $taskConfig;
    }


    /**
     * Parse and evaluate the condition string from config
     *
     * @param array $config
     * @return string
     */
    private function isConditionTrue(array $config)
    {
        if (isset($config['condition'])) {
            return $this->expLang->evaluate($config['condition'], $this->projectConfig);
        }
        return true;
    }


    /**
     * Reduce the tasks array to the task names given in $tasks
     *
     * @param $tasks
     * @return bool
     */
    private function reduceTasks($tasks)
    {
        // return just tasks are configured to execute by --task="... 1" --task="... 2"
        if (!empty($tasks)) {
            // ToDo: send message if task from --task option does not exist in your project file
            $this->projectTasks = array_intersect_key($this->projectTasks, array_flip($tasks));
            return true;
        }
        return false;
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
     * Load classes to helperset
     *
     * @param array $HelperArray
     */
    private function createHelperSet(array $HelperArray)
    {
        foreach ($HelperArray as $helper) {
            $this->getHelperSet()->set($helper);
        }
    }


    /**
     * debug output for task options, project runtime config or both
     *
     * @param array $array
     * @param string $path
     * @param int $depth
     * @param string $type
     */
    private function debug($array = [], $path = '', $depth = -1, $type = 'print')
    {
        // get ary value by path
        if (isset($path)) {
            $array = $this->arrayPathValue($path, $array);
        }

        // to max depth level
        $array = $this->arrayLevelReduce($array, $depth);
        if ($type === 'export') {
            var_export($array);
        } else {
            $this->recursivePrint('debug', $array);
        }
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
     * get the value of a array by key path example: this.is.my.path
     *
     * @param $path
     * @param array $data
     * @return array|mixed
     */
    private function arrayPathValue($path, array $data)
    {
        $paths = explode(".", $path);
        // iterate over path and data array
        foreach ($paths as $seg) {
            isset($data[$seg]) ? $data = $data[$seg] : null;
        }
        return $data;
    }


    /**
     * returns an array with given max level
     *
     * @param $array
     * @param int $maxLevel
     * @return array
     */
    private function arrayLevelReduce($array, $maxLevel = -1)
    {
        // if no max level is set return whole array
        if ($maxLevel === -1) {
            return $array;
        }
        // reduce if max level is set // ToDO: refactor! this shit works very poor
        $arrayReduced = [];
        if ($maxLevel > 0) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $arrayReduced[$key] = $this->arrayReduce($value, --$maxLevel);
                } else {
                    $arrayReduced[$key] = $value;
                }
            }
        }
        return $arrayReduced;
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
