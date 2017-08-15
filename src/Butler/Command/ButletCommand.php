<?php
// Command/InitGitCommand.php
namespace Butler\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ButletCommand extends Command
{
    protected function configure()
    {
      $this->setName('butlet:list');
      $this->setDescription('Shows a list of configured butlets.');

      #$this->addArgument('vendor', InputArgument::REQUIRED);
      #$this->addArgument('projectname', InputArgument::REQUIRED);

      #$this->addOption('path', 'p', InputOption::VALUE_REQUIRED, '', getcwd());

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        #$output->writeln(sprintf('Created the file %s', $path));
        $output->writeln([
          'Configured butlets: (tbd)',
          '===================',
          '- create-neos',
          '- create-typo3',
          '- create-reactjs'
        ]);
    }
}
