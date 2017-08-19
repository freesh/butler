<?php
// Command/InitGitCommand.php
namespace Butler\Task;

use Butler\Task\Task;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ComposerTask extends Task
{


    public function create(InputInterface $input, OutputInterface $output, $package) {
        Task::executeTask('touch mofa.txt');
    }

    public function add() {
        Task::executeTask('echo "Add vendor/package"');
    }

    public function remove() {
        Task::executeTask('echo "Remove vendor/package"');
    }

}
