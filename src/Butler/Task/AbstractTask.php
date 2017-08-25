<?php
namespace Butler\Task;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\HelperSet;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\ChoiceQuestion;



abstract class AbstractTask
{
    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var HelperSet
     */
    protected $helperSet;

    /**
     * @var array
     */
    protected $config = [];


    /**
     * AbstractTask constructor
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output, HelperSet $helperSet )
    {
        $this->input = $input;
        $this->output = $output;
        $this->helperSet = $helperSet;
    }

    /**
     * @param string $question
     * @param string $default
     */
    protected function setQuestion($question = '', $default = '') {

        $helper = $this->getHelper('question');
        $question = new Question($question, $default);

        $bundle = $helper->ask($this->input, $this->output, $question);

        return $bundle;
    }

    /**
     * @param string $command
     * @return string
     */
    protected function execute($command) {
        $process = new Process($command);
        $process->setTimeout(600);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    /**
     * @param $name
     * @return \Symfony\Component\Console\Helper\HelperInterface
     */
    protected function getHelper($name)
    {
        return $this->helperSet->get($name);
    }
}