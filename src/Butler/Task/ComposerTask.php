<?php
// Command/InitGitCommand.php
namespace Butler\Task;

use Butler\Task\Task;

class ComposerTask extends AbstractTask
{

    /**
     * @param string $distribution
     */
    public function create($options) {
        $this->output->writeln('Installing '.$options['distribution']);
        $this->execute('touch '.$options['distribution'].'.txt');
    }

    /**
     * @param string $package
     */
    public function add($options) {
        $this->output->writeln('Add package :'.$options['distribution']);
        $this->execute('echo "Add vendor/package"');
    }

    /**
     * @param string $package
     */
    public function remove($options) {
        $this->output->writeln('Remove '.$options['distribution']);
        $this->execute('echo "Remove vendor/package"');
    }

}
