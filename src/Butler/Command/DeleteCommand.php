<?php
// Command/InitGitCommand.php
namespace Butler\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use Symfony\Component\Console\Input\InputArgument;
#use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteCommand extends Command
{
    protected function configure()
    {
        $this->setName('project:delete');
        $this->setAliases(['d']);
        $this->setDescription('Deletes a project.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->task('docker-compose down && docker-compose rm -f');
        $this->task('sudo rm -Rf ./* ./.g*');
        $this->task('ls -la');
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
