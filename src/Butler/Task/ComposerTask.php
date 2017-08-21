<?php
// Command/InitGitCommand.php
namespace Butler\Task;

use Butler\Task\Task;

class ComposerTask extends AbstractTask
{

    /**
     * @param string $distribution
     */
    public function create($distribution) {
        $this->output->writeln('Installing '.$distribution);
        $this->execute('touch '.$distribution.'.txt');
    }

    /**
     * @param string $package
     */
    public function add($package) {
        $this->output->writeln('Add package :'.$package);
        $this->execute('echo "Add vendor/package"');
    }

    /**
     * @param string $package
     */
    public function remove($package) {
        $this->output->writeln('Remove '.$package);
        $this->execute('echo "Remove vendor/package"');
    }

}
