<?php
namespace Butler\Task;

class CommandTask extends AbstractTask
{
    /**
     * @param array $config
     * @return void
     */
    public function command(array $config)
    {
        $this->execute($config['options']['command']);
    }

    /**
     * @param array $config
     * @return void
     */
    public function commands(array $config)
    {
        foreach ($config['options']['commands'] as $command) {
            $this->execute($command);
        }
    }
}
