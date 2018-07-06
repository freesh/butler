# Create new Task File (deprecated)

1. Create a new task class in ```src/Butler/Task/``` extending ```AbstractTask```.
2. Now you can create public functions with a $config param which is an array. (function name = task name)
3. If a task function return an array, the values are merged in to the project config. Multidimensional Arrays are supported and can be used in following task configs like this: {level1.sub2.myvar}
4. Use $this->execute(); to execute a cli command; Example: ```$this->execute('composer-install');```
5. (tbd) Use $this->writeln(); to write some text on the commandline. Example ```$this->writeln('project installed');```
6. (tbd) Use $this->question(); to send a question input. Example: ```$answer = $this->question('Description for Github project', 'This is a default description');```
7. Use project configuration: ``` $config['project']['mykey'] ``` (tbd)->(usage like $this->getProjectConfig('mykey') )
8. Use task configuration: ```$config['options']['task-option-key']``` (tbd)->(usage like $this->getOption('description') )

Example:

```php
<?php
namespace Butler\Task;


class MyTask extends AbstractTask
{


    /**
     * @param array $options
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
        if (isset($config['options']['package'])) {
            $this->writeln('These packages will installed: ' .$config['options']['package']);
        }

        $additionalpackages = $this->question('Add additional packages: ', '');
        $this->execute('composer require '. (!isset($config['options']['params'])? '' : implode(' ', $config['options']['params'])) . $additionalpackages . ' '. $config['options']['package'] );
    }


    /**
     * @param array $options
     */
    public function remove(array $options) {
        $this->execute('composer remove '.$options['package']);
    }

}

```
