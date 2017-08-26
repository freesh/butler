<?php
namespace Butler\Task;


class ComposerTask extends AbstractTask
{


    /**
     * @param array $config
     */
    public function create(array $config) {
        $this->execute('composer create-project '. (!isset($config['options']['params'])? '' : implode(' ', $config['options']['params'])) .' '. $config['options']['distribution'].' '.$config['options']['tempPath'] );
        $this->execute('shopt -s dotglob && mv '. $config['options']['tempPath'] .'/* ./');
        $this->execute('rm -Rf '. $config['options']['tempPath']);
    }


    /**
     * @param array $config
     */
    public function add(array $config) {
        $this->execute('composer require '. (!isset($config['options']['params'])? '' : implode(' ', $config['options']['params'])) .' '. $config['options']['package'] );
    }


    /**
     * @param array $config
     */
    public function remove(array $config) {
        $this->execute('composer remove '.$config['package']);
    }

}
