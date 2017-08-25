<?php
namespace Butler\Task;

class InputTask extends AbstractTask
{


    /**
     * @param array $config
     * @return array
     */
    public function question(array $config) {

        $answers = [];
        foreach ($config['options'] as $key => $question) {
            $response = $this->setQuestion($question, 'AcmeDemo');
            $answers[$key] = $response;
        }

        return $answers;
    }

}
