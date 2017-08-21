<?php
// Command/CreateCommand.php
namespace Butler\Command;

use Symfony\Component\Console\Command\Command;
use Butler\Project;

use Symfony\Component\Console\Input\InputArgument;
#use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCommand extends Command
{

    protected $composerTask;
    protected $neosTask;

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

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        #$PATH_ROOT = $this->task('pwd | tr -d \'\n\'');
        #$PATH_TEMP = $PATH_ROOT.'/temp-install';

        $project = new Project([
            'type' => str_replace('-','', ucwords($input->getArgument('project type'),'-')),
            'vendor' => $input->getArgument('vendor'),
            'name' => $input->getArgument('project name')
        ],
            $input,
            $output
        );

        $project->initProject();
        $project->executeTasks();


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
    }

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
