<?php
namespace Butler\Task;


class NeosTask extends AbstractTask
{


    /**
     * @param array $options
     */
    public function dummy(array $config) {
        $this->output->writeln('Neos dummy task');
    }

}
