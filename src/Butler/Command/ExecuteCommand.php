<?php
// Command/InitGitCommand.php
namespace Butler\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitGitCommand extends Command
{
    protected function configure()
    {
      $this->setName('init-git');
      $this->setDescription('Bootstraps the license file of your project');

      $this->addArgument('author', InputArgument::REQUIRED);
      $this->addArgument('year', InputArgument::REQUIRED);

      $this->addOption('path', 'p', InputOption::VALUE_REQUIRED, '', getcwd());

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        print_r('woekjdf opwefopwefwepo ');
    }
}
