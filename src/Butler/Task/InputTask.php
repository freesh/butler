<?php
namespace Butler\Task;

use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InputTask extends AbstractTask
{


    /**
     * FilesystemTask constructor.
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param HelperSet $helperSet
     */
    /*public function __construct(InputInterface $input, OutputInterface $output, HelperSet $helperSet)
    {
        parent::__construct($input, $output, $helperSet);

        $this->fs = $this->helperSet->get('filesystem'); // init filesystem helper
    }*/


    /**
     * @param array $config
     * @return array
     */
    public function question(array $config) {

        $answers = [];
        foreach ($config['options'] as $key => $question) {
            $response = $this->setQuestion('<options=bold;bg=cyan>  ASK </> <fg=cyan>'.$question.' :</> ', 'AcmeDemo');
            $answers[$key] = $response;
        }

        return $answers;
    }

    /**
     * @param array $config
     * @return array
     */
    public function writelines(array $config) {

        $this->output->writeln($config['options']);

    }

}
