<?php
// Command/InitGitCommand.php
namespace Butler\Task;

#use Butler\Task\Task;

class ComposerTask extends AbstractTask
{


    /**
     * @param array $options
     */
    public function create(array $config) {
        $this->output->writeln('Installing '.$config['options']['distribution']);
        $this->execute('composer create-project '. (!isset($config['options']['params'])? '' : implode(' ', $config['options']['params'])) .' '. $config['options']['distribution'].' '.$config['options']['tempPath'] );
        $this->execute('shopt -s dotglob && mv '. $config['options']['tempPath'] .'/* ./');
        $this->execute('rm -Rf '. $config['options']['tempPath']);
    }


    /**
     * @param array $config
     * @return array
     */
    public function add(array $config) {
        $this->output->writeln('Add package(s) :'.$config['options']['package']);
        $this->execute('composer require '. (!isset($config['options']['params'])? '' : implode(' ', $config['options']['params'])) .' '. $config['options']['package'] );
        return ['test' => $config['options']['package']];
    }


    /**
     * @param array $options
     */
    public function remove(array $options) {
        $this->output->writeln('Remove '.$options['package']);
        $this->execute('composer remove '.$options['package']);
    }

}
