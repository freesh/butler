<?php
// Command/CreateCommand.php
namespace Butler\Command;

use Butler\Helper\FilesystemHelper;
use Butler\Helper\YamlHelper;
use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;


#use Symfony\Component\Process\ProcessBuilder;

class CreateCommand extends Command
{

    protected $taskObjects; // Array with task objects
    protected $expLang; // ExpressionLanguage
    private $projectConfig = []; // Runtime project config array

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var InputInterface
     */
    protected $input;



    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->expLang = new ExpressionLanguage();
    }

    protected function configure ()
    {
        $this->setName('project:create');
        $this->setAliases(['c']);
        $this->setDescription('Creates a Neos project.');

        $this->addArgument('project type', InputArgument::REQUIRED);
        $this->addOption('task', 't', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Execute only this task(s)', []);
    }

    /**
     * @param array $config
     * @return mixed
     */
    protected  function dispatchProject (array $config)
    {
        // load Project
        $namespace = '\\Butler\\Project\\'.$config['type'].'Project';
        $project = new $namespace($config);

        return $project;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute (InputInterface $input, OutputInterface $output)
    {

        $this->output = $output;
        $this->input = $input;

        // set additional helpers
        $this->getHelperSet()->set(new FilesystemHelper());
        $this->getHelperSet()->set(new YamlHelper());

        #$output->writeln('Init Project: ' . $input->getArgument('project type'));
        // create project object
        $project = $this->dispatchProject([
            'type' => str_replace('-', '', ucwords($input->getArgument('project type'), '-')),
            'tasks' => $input->getOption('task')
        ]);

        // execute tasks
        foreach ($project->getTasks() as $key => $config) {
            $task = (string)$config['task'];
            $class = (string)$config['class'];
            $projectConf = null;

            // execute if no condition is configured or condition is true
            if ( !isset($config['condition']) || $this->parseTaskCondition($config['condition']) ) {

                $this->output->writeln('<fg=green;options=bold>Execute Task:</> <fg=blue>' . $key . '</>' . ($output->isVerbose() ? ' <comment>(' . $class . ' -> ' . $task . ')</comment>' : ''));

                # create task object
                if (!isset($this->taskObjects[$class]) || !$this->taskObjects[$class] instanceof $class) {
                    $this->taskObjects[$class] = new $class($input, $output, $this->getHelperSet());
                }

                // Parse variables in task option array
                $taskOptions = $this->parseTaskConfig($config['options']);

                // execute task
                $projectConf = $this->taskObjects[$class]->$task(
                    [
                        'project' => $this->projectConfig,
                        'options' => $taskOptions
                    ]
                );

                // merge project if task returns array with options
                if (is_array($projectConf)) {
                    $this->updateProjectConfiguration($projectConf);
                }

                // debug task options or/and runtime config
                if (isset($config['debug']) && $config['debug'] == true) {
                    $this->debug(
                        [
                            'project' => $this->projectConfig,
                            'options' => $taskOptions
                        ],
                        (isset($config['debug-path']) ? $config['debug-path'] : ''),
                        (isset($config['debug-depth']) ? $config['debug-depth'] : -1),
                        (isset($config['debug-type']) ? $config['debug-type'] : null)
                    );
                }

            } else {
                $this->output->writeln('<fg=green;options=bold>Execute Task:</> <fg=blue>' . $key . '</> <fg=red>Skipped by condition.</> ' . ($output->isVerbose() ? ' <comment>(' . $class . ' -> ' . $task . ')</comment>' : ''));
            }
        }
    }


    /**
     * Merge new data to existing global project configuration
     *
     * @param array $projectConfiguration
     */
    private function updateProjectConfiguration (array $projectConfiguration = [])
    {
        $this->projectConfig = array_merge( $this->projectConfig, $projectConfiguration);
    }


    /**
     * Parse and evaluate the condition string from config
     *
     * @param string $condition
     * @return string
     */
    private function parseTaskCondition ($condition = '')
    {
        $res = $this->expLang->evaluate(
                $condition,
                $this->projectConfig
            );

        return $res;
    }


    /**
     * @param array $taskConfig
     */
    private function parseTaskConfig (array $taskConfig = [])
    {
        array_walk_recursive(
            $taskConfig,
            function (&$val) {
                $matches = null;
                // get variablenames from taskConfig string ( declaration: {var1} varname: var1 )
                preg_match_all('/{(.*?)}/', $val, $matches, PREG_SET_ORDER, 0);

                if ($matches) {
                    // iterate over multible matches and replace them if they exists in projectConfig
                    foreach ($matches as $match) {

                        // if path is found and a value is returned
                        if ( is_string( $param = $this->arrayPathValue( $match[1], $this->projectConfig ) ) ) {

                            // replace variable string with value
                            $val = str_replace( $match[0], $param, $val );
                        }
                    }
                }
            }
        );

        return $taskConfig;
    }

    /**
     * debug output for task options, project runtime config or both
     *
     * @param array $array
     * @param string $path
     * @param int $depth
     * @param string $type
     */
    private function debug ($array=[], $path='', $depth=-1, $type='print') {

        // get ary value by path
        if (isset($path)) {
            $array = $this->arrayPathValue(
                $path,
                $array
            );
        }

        // to max depth level
        $array = $this->arrayReduce(
            $array,
            $depth
        );

        if ($type == 'export') {
            var_export($array);
        } else {
            $this->recursive_print('debug', $array);
        }

    }

    function recursive_print ($varname, $varval) {
        if (! is_array($varval)):
            $this->output->writeln( "<fg=red>".$varname . ": </> = " . $varval );
        else:
            foreach ($varval as $key => $val):
                $this->recursive_print ($varname . "." . $key, $val);
            endforeach;
        endif;
    }

    /**
     * get the value of a array by key path example: this.is.my.path
     * @param $path
     * @param array $array
     * @return array|mixed
     */
    private function arrayPathValue ( $path, array $data ) {
        $paths = explode(".", $path);

        // iterate over path and data array
        foreach($paths as $seg){
            isset($data[$seg]) ? $data = $data[$seg] : null;
        }

        return $data;
    }

    /**
     * returns an array with given max level
     * @param $array
     * @param int $maxLevel
     * @return array
     */
    private function arrayReduce ($array, $maxLevel = -1) {

        // if no max level is set return whole array
        if ($maxLevel == -1)
            return $array;

        // reduce if max level is set // ToDO: refactor! this shit works very poor
        $arrayReduced = [];
        if ($maxLevel > 0 ) {

            foreach ($array as $key => $value) {

                if(is_array($value)) {
                    $arrayReduced[$key] = $this->arrayReduce($value, --$maxLevel);
                } else {
                    $arrayReduced[$key] = $value;
                }
            }
        }

        return $arrayReduced;
    }
}
