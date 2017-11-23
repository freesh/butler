<?php
namespace Butler\Task;

use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InputTask extends AbstractTask
{


    /**
     * @param array $config
     * @return array
     */
    public function question(array $config) {
        $answers = [];
        foreach ($config['options'] as $key => $question) {
            // print question and get answer
            $response = $this->setQuestion('<options=bold;bg=cyan>  ASK </> <fg=cyan>'.$question.' :</> ', '');
            // write the result to multidimensional array defines by a path string
            $answers = array_merge_recursive($this->pathToArray($key, $response), $answers);
        }
        return $answers;
    }

    /**
     * output multible questions from type: confirmation and return the answer to the given variable
     *
     * @param array $config
     * @return array
     */
    public function confirmation(array $config) {
        $answers = [];
        foreach ($config['options'] as $key => $question) {
            // print question and get answer
            $response = $this->setQuestion('<options=bold;bg=cyan>  ASK </> <fg=cyan>'.$question.':</> (y/n)(default:y): ', true, 'confirmation');
            // write the result to multidimensional array defines by a path string
            $answers = array_merge_recursive($this->pathToArray($key, $response), $answers);
        }
        return $answers;
    }

    /**
     * @param array $config
     * @return void
     */
    public function writelines(array $config) {

        $this->output->writeln($config['options']);

        return;
    }

    /**
     * transform string path and value to multidimensional array
     * @param $path
     * @param $value
     * @return array
     */
    private function pathToArray($path, $value) {

        // get reversed path elements
        $paths = array_reverse(explode(".", $path));

        //add value to last path element
        $array = array($paths[0] => $value);

        // remove last path element
        unset($paths[0]);

        // iterate elements from childs to parent
        foreach ($paths as $arr) {
            $array = array($arr => $array);
        }

        return $array;
    }

}
