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

        $PATH_TEMP="./temp-install";
        #$PATH_ROOT=$(pwd);


        $this->task('composer create-project --no-dev neos/neos-base-distribution '.$PATH_TEMP);
        $this->task('cd '.$PATH_TEMP);
        $this->task('cp -R ~/Tools/Docker/* ./');

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

        echo $process->getOutput();
    }
}
