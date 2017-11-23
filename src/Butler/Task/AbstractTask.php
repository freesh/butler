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

use Symfony\Component\Process\ProcessBuilder;

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
    public function __construct(InputInterface $input, OutputInterface $output, HelperSet $helperSet)
    {
        $this->input = $input;
        $this->output = $output;
        $this->helperSet = $helperSet;
    }

    /**
     * @param string $question
     * @param string $default
     * @param string $type question | confirmation | choice
     * @param array $choices array with choices if $type == choice
     * @return mixed
     */
    protected function setQuestion($question = '', $default = '', $type = 'question', $choices = array())
    {
        $helper = $this->getHelper('question');
        switch ($type) {
            case 'confirmation':
                if ($default == '') {
                    $default = false;
                }
                $question = new ConfirmationQuestion($question, $default);
                break;
            case 'choice':
                if (!$default) {
                    $default = null;
                }
                $question = new ChoiceQuestion($question, $choices, $default);
                break;
            case 'question':
            default:
                $question = new Question($question, $default);
        }
        $bundle = $helper->ask($this->input, $this->output, $question);
        return $bundle;
    }

    /**
     * @param string $command
     * @return string
     */
    protected function execute($command)
    {
        $processHelper = $this->getHelper('process');
        $process = new Process($command);
        $process->setTimeout(600);
        $processHelper->run($this->output, $process);
        // executes after the command finishes
        if (!$process->isSuccessful()) {
            $this->output->writeln('<error><options=bold;bg=red>  ERR </></error> <fg=red>"' . $command . '" is too drunk to work. Please run butler command with -v, -vv, or -vvv for more information.</>');
            if ($this->output->isVerbose()) {
                $this->output->writeln('<fg=black;bg=white>'.$process->getErrorOutput().'</>');
            }
            #throw new ProcessFailedException($process);
        } else {
            if ($this->output->isVerbose()) {
                $this->output->writeln('<options=bold;bg=green>  OK  </> <comment>"' . $command . '</comment>');
            }
        }
        #return $process->getOutput();
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
