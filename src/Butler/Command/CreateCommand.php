<?php
// Command/CreateCommand.php
namespace Butler\Command;

use Butler\Helper\FileSystemHelper;
use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputArgument;
#use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;


use Symfony\Component\Process\ProcessBuilder;

class CreateCommand extends Command
{

    protected $taskObjects; // Array with task objects
    protected $expLang; // ExpressionLanguage
    private $projectConfig = []; // Runtime project config array

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->expLang = new ExpressionLanguage();
    }

    protected function configure()
    {
        $this->setName('project:create');
        $this->setAliases(['c']);
        $this->setDescription('Creates a Neos project.');

        $this->addArgument('project type', InputArgument::REQUIRED);
        $this->addArgument('vendor', InputArgument::REQUIRED);
        $this->addArgument('project name', InputArgument::REQUIRED);

        #$this->addOption('path', 'p', InputOption::VALUE_REQUIRED, '', getcwd());
    }

    /**
     * @param array $config
     * @return mixed
     */
    protected  function dispatchProject(array $config)
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
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        #$output->writeln('Init Project: ' . $input->getArgument('project type'));
        // create project object
        $project = $this->dispatchProject([
            'type' => str_replace('-', '', ucwords($input->getArgument('project type'), '-')),
            'vendor' => $input->getArgument('vendor'),
            'name' => $input->getArgument('project name')
        ]);

        // set additional helpers
        $this->getHelperSet()->set(new FileSystemHelper());


        // execute tasks
        foreach ($project->getTasks() as $key => $config) {
            $task = (string)$config['task'];
            $class = (string)$config['class'];
            $projectConf = null;

            // execute if no condition is configured or condition is true
            if ( !isset($config['condition']) || $this->parseTaskCondition($config['condition']) ) {

                $output->writeln('<fg=green;options=bold>Execute Task:</> <fg=blue>' . $key . '</>' . ($output->isVerbose() ? ' <comment>(' . $class . ' -> ' . $task . ')</comment>' : ''));

                # create task object
                if (!isset($this->taskObjects[$class]) || !$this->taskObjects[$class] instanceof $class) {
                    $this->taskObjects[$class] = new $class($input, $output, $this->getHelperSet());
                }

                // execute task
                $projectConf = $this->taskObjects[$class]->$task(
                    [
                        'project' => $this->projectConfig,
                        'options' => $this->parseTaskConfig($config['options'])
                    ]
                );
            } else {
                $output->writeln('<fg=green;options=bold>Execute Task:</> <fg=blue>' . $key . '</> <fg=red>Skipped by condition.</> ' . ($output->isVerbose() ? ' <comment>(' . $class . ' -> ' . $task . ')</comment>' : ''));
            }

            // merge project if task returns array with options
            if (is_array($projectConf)) {
                $this->updateProjectConfiguration($projectConf);
            }
        }
    }


    /**
     * @param array $projectConfiguration
     */
    private function updateProjectConfiguration(array $projectConfiguration = [])
    {
        $this->projectConfig = array_merge( $this->projectConfig, $projectConfiguration);
    }


    /**
     * @param array $condition
     */
    private function parseTaskCondition($condition = '')
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
    private function parseTaskConfig(array $taskConfig = [])
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
                        if ($param = isset($match[1]) ? $match[1] : false) {
                            if (isset($this->projectConfig[$param])) {
                                $val = str_replace('{'.$param.'}', $this->projectConfig[$param], $val);
                            }
                        }
                    }
                }
            }
        );

        return $taskConfig;
    }

        /* $output->writeln([
            'creating Project',
            '#################',
            #'path: '.$PATH_ROOT,
            #'path temp: '.$PATH_TEMP,
            'name: '.$input->getArgument('vendor').'/'.$input->getArgument('projectname')
        ]);*/

        # init taskdispatcher


        # create task


        # init composer project
        #$this->composerTask = new ComposerTask();



        #$this->composerTask->create('neos/neos-base-distribution');
        #$this->task('composer create-project --no-dev neos/neos-base-distribution '.$PATH_TEMP);



/*
        # move all to root
        $this->task('mv '.$PATH_TEMP.'/* '.$PATH_TEMP.'/.g* '.$PATH_ROOT.'/');



        $this->task('cd '.$PATH_TEMP);
        # import docker config
        $this->task('cp -R ~/Tools/Docker/* ./');

        # copy development Settings.yaml
        $this->task('cp ~/Tools/Neos/Build/Templates/Configuration/Development/Settings.yaml ./Configuration/Development');
        $this->task('mv ./Configuration/Settings.yaml.example ./Configuration/Settings.yaml');

        $this->task('cp ~/Tools/Neos/Build/composer.php ./Build');

        # update composer.json
        $this->task('php ./Build/composer.php');

        # Init Docker ...
        $this->task('docker-compose up -d');

        # Init mySQL
        $this->task('while ! mysqladmin ping -h0.0.0.0 --port=8086 --silent; do sleep 1 ;done');

        # migrate database
        $this->task('export FLOW_CONTEXT=Development && ./flow doctrine:migrate');

        # Create Admin [admin:admin]'
        $this->task('export FLOW_CONTEXT=Development && ./flow user:create admin admin King Loui --roles Neos.Neos:Administrator');

        # import site package
        #export FLOW_CONTEXT=Development && ./flow site:import --package-key Neos.Demo

        # create site package
        #echo "Create Sitepackage $VENDOR_NAME.Site"
        $this->task('export FLOW_CONTEXT=Development && ./flow kickstart:site --package-key '.$input->getArgument('vendor').'.Site --site-name '.$input->getArgument('projectname'));

        # create page
        #echo "Create Page $PAGE_NAME"
        $this->task('export FLOW_CONTEXT=Development && ./flow site:create '.$input->getArgument('projectname').' '.$input->getArgument('vendor').'.Site');

        #####################
        # init atomic fusion
        #####################
        #start_spinner "Add packagefactory/atomicfusion"
        $this->task('composer require packagefactory/atomicfusion');

        # TODO: configure

        ########################
        # init atomic fusion-afx
        ########################
        #start_spinner "Add packagefactory/atomicfusion-afx"
        $this->task('composer require packagefactory/atomicfusion-afx:~3.0.0');

        #TODO: Configure

        #####################
        # init monocle
        #####################
        #start_spinner "Add sitegeist/monocle"
        $this->task('composer require sitegeist/monocle');

        # ToDo: configure

        #####################
        # init magickwand
        #####################
        #start_spinner "Add sitegeist/magicwand"
        $this->task('composer require --dev sitegeist/magicwand:dev-master');

        # ToDo: configure


        ########################
        # init tests and linting
        ########################
        #start_spinner "Add sitegeist/neosguidelines"
        $this->task('composer require --dev sitegeist/neosguidelines');

        # ToDo: configure


        ########################
        # remove unused packages
        ########################
        #start_spinner "Remove neos/demo"
        $this->task('composer remove neos/demo');


        #####################
        # init git repository
        #####################
        #echo "Init git"
        #start_spinner 'Init git'
        $this->task('git init');

        #####################
        # init deployment
        #####################
        # tbd: call deployment.sh

        #####################
        # init README.md
        #####################
        # tbd: call readme.sh
        #start_spinner 'Create README.md'
        $this->task('touch README.md');


        #####################
        # tidy up
        #####################
        # remove initial temp folder
        $this->task('rm -Rf $PATH_TEMP');
        $this->task('docker-compose down');

        # remove unused composer packages

        # flush cache (because of deleted neos packages)
        $this->task('export FLOW_CONTEXT=Development && ./flow flow:cache:flush');

        # start neos server
        #$this->task('export FLOW_CONTEXT=Development && ./flow server:run');
*/

        #$output->writeln(sprintf('Created the file %s', $path));
        /*$output->writeln([
            'Neos is configured:',
            '===================',
            'To start Server run: ',
            'docker-compose up -d',
            'export FLOW_CONTEXT=Development && ./flow server:run'
        ]);*/
    #}

    /*protected function task($command) {
        $process = new Process($command);
        $process->setTimeout(600);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }*/
}
