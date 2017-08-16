<?php
// Command/CreateCommand.php
namespace Butler\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use Symfony\Component\Console\Input\InputArgument;
#use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCommand extends Command
{
    protected function configure()
    {
        $this->setName('project:create');
        $this->setAliases(['c']);
        $this->setDescription('Creates a Neos project.');

        #$this->addArgument('type', InputArgument::REQUIRED);
        $this->addArgument('vendor', InputArgument::REQUIRED);
        $this->addArgument('projectname', InputArgument::REQUIRED);

        #$this->addOption('path', 'p', InputOption::VALUE_REQUIRED, '', getcwd());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $PATH_ROOT = $this->task('pwd | tr -d \'\n\'');
        $PATH_TEMP = $PATH_ROOT.'/temp-install';

        $output->writeln([
            'creating Project',
            '#################',
            'path: '.$PATH_ROOT,
            'path temp: '.$PATH_TEMP,
            'name: vendor/projectname'
        ]);


        # init composer project
        $this->task('composer create-project --no-dev neos/neos-base-distribution '.$PATH_TEMP);

        # move all to root
        $this->task('mv '.$PATH_TEMP.'/* '.$PATH_TEMP.'/.g* '.$PATH_ROOT.'/');



        $this->task('cd '.$PATH_TEMP);
        # import docker config
        $this->task('cp -R ~/Tools/Docker/* ./');

        # copy development Settings.yaml
        $this->task('cp ~/Tools/Neos/Build/Templates/Configuration/Development/Settings.yaml ./Configuration/Development');
        $this->task('mv ./Configuration/Settings.yaml.example ./Configuration/Settings.yaml');

        $this->task('cp ~/Tools/Neos/Build/composer.php ./Build');




        #$output->writeln(sprintf('Created the file %s', $path));
        /*$output->writeln([
            'Configured butlets: (tbd)',
            '===================',
            '- create-neos',
            '- create-typo3',
            '- create-reactjs'
        ]);*/
    }

    protected function task($command) {
        $process = new Process($command);
        $process->setTimeout(600);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }
}
