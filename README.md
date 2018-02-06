# Butler

Note: This in an experimental wip state.

Butler is a php taskrunner for creating and initialising web projects.
You can define tasks for composer, git, docker, sftp, file operations and other things ...

[Installation](#installation)

[Usage](#usage)

[Create a project file](#create-a-project-file)

[Debugging](#debugging)

[Create new Task File](#create-new-task-file-deprecated)

## Installation:

**Clone repository:**

```git clone git@github.com:freesh/butler.git ~/Butler```

**Composer install**

```composer install```

**Make butler file executable:**

```chmod +x ~/Butler/butler```

**Add alias to ~/.bashrc or ~/zshrc:**

```alias butler="php ~/Butler/butler"```


## Usage:

**List comands**

```butler list```

**List project configurations**

```butler project:list```

**Run project:**

```cd emptyProjectfolder```

```butler project:run neos-base```

Execute just some specific tasks from project config: --task or -t

```butler project:run neos-base --task="myTask1Key" --task="my task2 key"```

Execute with special path for butler files (default: ~/Butler)

```butler project:run neos-base --projectPath="./Build/Butler"```

**Help:**

```butler``` or ```butler --help```

**Help for a specific command**

```butler help command:name```

## Create a project file

1. Create a new projectname.yaml file in ```~/Butler/Project/```.
2. Configure your tasks.
3. Use your task: ```$ butler project:run projectname```
4. If your projectfile is located in a subfolder like ```~/Butler/Project/vendor/projectfile.yaml```, you have to use it like that: ```$ butler project:run vendor/projectname```

Example configuration:

```
########################
# Init runtime data
########################
'Set project data':
  class: \Butler\Task\InputTask
  task: question
  options:
    projectname: 'What is the name of your Project?'

########################
# Init project with composer
########################
'Composer create':
  class: \Butler\Task\ComposerTask
  task: create
  options:
    distribution: neos/neos-base-distribution
    tempPath: temp
    params:
      - '--no-dev'

'Neos kickstart site':
  class: \Butler\Task\NeosTask
  task: kickstartSite
  options:
    context: Development
    site-name: '{projectname}'

// ...
```

### Modify and use project runtime config

Ask the user some interesting questions with the question task of the inputTask Driver:
```
'get project-data':
  class: \Butler\Task\InputTask
  task: question
  options:
    projectname: What is the name of your project?
    projectvendor: What is the vendor of your project?
    level1.sub1.myvar: first tree in level1
    level1.sub2.myvar: different tree in level1
 ```

The option keys "projectname" and "projectvendor" will be stored in a project configuration.
This config variables can be used in task configuration like this:
```
'touch file':
  class: \Butler\Task\FilesystemTask
  task: touch
  options:
    files:
      - '{projectvendor}-{projectname}.txt'
      - '{level1.sub1.myvar}.txt'
      - '{level1.sub2.myvar}.txt'
```
The first task will ask the user for vendor and name and the second task creates a file named by the answers.
Some Tasks return data to the runtime config.


### Using conditions in task configuration

The execution of every task can skipped by condition:

```
'set project data:
  class: \Butler\Task\InputTask
  task: question
  options:
    projectname: 'What is the name of your Project?'
    projectvendor: 'What is the vendor name of your Project?'

'touch projectvendor',
  class: \Butler\Task\FilesystemTask
  task: touch
  condition: 'projectname != projectvendor'
  options:
    files:
      - '{projectvendor}-{projectname}.txt'
```
The task "touch projectvendor" is only executed if project config variables for "projectname" and "projectvendor" have NOT the same value.

Comparison Operators: (see: https://symfony.com/doc/current/components/expression_language/syntax.html#comparison-operators)

- ```==``` (equal)
- ```===``` (identical)
- ```!=``` (not equal)
- ```!==``` (not identical)
- ```<``` (less than)
- ```>``` (greater than)
- ```<=``` (less than or equal to)
- ```>=``` (greater than or equal to)
- ```matches``` (regex match)

Logical Operators: (see: https://symfony.com/doc/current/components/expression_language/syntax.html#logical-operators)

- ```not``` or ```!```
- ```and``` or ```&&```
- ```or``` or ```||```

Arithmetic Operators (see: https://symfony.com/doc/current/components/expression_language/syntax.html#arithmetic-operators)

- ```+``` (addition)
- ```-``` (subtraction)
- ```*``` (multiplication)
- ```/``` (division)
- ```%``` (modulus)
- ```**``` (pow)

Bitwise Operators (see: https://symfony.com/doc/current/components/expression_language/syntax.html#bitwise-operators)

- ```&``` (and)
- ```|``` (or)
- ```^``` (xor)

String Operators: (see: https://symfony.com/doc/current/components/expression_language/syntax.html#string-operators)

- ```~``` (concatenation)

Array Operators: (see: https://symfony.com/doc/current/components/expression_language/syntax.html#array-operators)

- ```in``` (contain)
- ```not in``` (does not contain)

Numeric Operators: (see: https://symfony.com/doc/current/components/expression_language/syntax.html#numeric-operators)

- ```..``` (range)

Ternary Operators: (see: https://symfony.com/doc/current/components/expression_language/syntax.html#ternary-operators)

- ```foo ? 'yes' : 'no'```
- ```foo ?: 'no' (equal to foo ? foo : 'no')```
- ```foo ? 'yes' (equal to foo ? 'yes' : '')```

## Debugging

### Debug Task output

```butler project:run neos-base -v```

```butler project:run neos-base -vv```

```butler project:run neos-base -vvv```

### Debug "Task-" and "Runtime-Config"

``` 
'github create repository':
  class: \Butler\Task\GithubTask
  task: repositoryCreate
  options:
    ...
  debug: true // bool | optional default: false
  debug-depth: 3 // int | optional (default: -1)
  debug-path: project.github' // string | optional (project.my.option)
  debug-type: export // string | optional [export|print] (default: print)
```

The option ```debug``` activates debug output for this task. It will output the representation of the task options array and the project runtime array.
You can set the debug option to a specific part ob this array with the ```debug-path``` option.
With the ```debug-type``` option you can switch between 'export' and 'print' view of the output. For more information take a look at the examples below.
The ```debug-depth``` option offers you a way to reduce the array to a specific depth. But note: If ```debug-type``` is set to 'print' all lines with more levels then configured, will be hidden.

Examples:

**debug: 1**

output:

```
Execute Task: github create repository
debug.project.github.level1.level2.level3:  = value
debug.project.github.test:  = value
debug.project.github.level12.level2.level3:  = value
debug.options.name:  = {projectname}
debug.options.description:  =  Test project for {projectname}
debug.options.homepage:  = www.{projectname}
debug.options.public:  = false

```

**debug-path: project.github**

output:

```
Execute Task: github create repository
debug.level1.level2.level3:  = value
debug.test:  = value
debug.level12.level2.level3:  = value
```

**debug-type**

output print (default):

```
...
debug.project.github.test:  = value
debug.project.github.level12.level2.level3:  = value
debug.options.name:  = {projectname}
debug.options.description:  =  Test project for {projectname}
...
```
output export:
```
Execute Task: github create repository
array (
  'project' => 
  array (
    'github' => 
    array (
      'level1' => 
      array (
        'level2' => 
        array (
          'level3' => 'lalala',
        ),
      ),
      'test' => 'lilili',
      'level12' => 
      array (
        'level2' => 
        array (
          'level3' => 'lalala',
        ),
      ),
    ),
  ),
  'options' => 
  array (
    'name' => '{projectname}',
    'description' => ' Test project for {projectname}',
    'homepage' => 'www.{projectname}',
    'public' => false,
  ),
)%
```

**debug-depth: 1**

output export:
```
Execute Task: github create repository
array (
  'project' => 
  array (
  ),
  'options' => 
  array (
    'name' => '{projectname}',
    'description' => ' Test project for {projectname}',
    'homepage' => 'www.{projectname}',
    'public' => false,
    'debug' => true,
  ),
)%
```

output print:
```
Execute Task: github create repository
debug.options.name:  = {projectname}
debug.options.description:  =  Test project for {projectname}
debug.options.homepage:  = www.{projectname}
debug.options.public:  = false

```


## Create new Task File (deprecated)

1. Create a new task class in ```src/Butler/Task/``` extending ```AbstractTask```.
2. Now you can create public functions with a $config param which is an array. (function name = task name)
3. If a task function return an array, the values are merged in to the project config. Multidimensional Arrays are supported and can be used in following task configs like this: {level1.sub2.myvar}
4. Use $this->execute(); to execute a cli command; Example: ```$this->execute('composer-install');```
5. (tbd) Use $this->writeln(); to write some text on the commandline. Example ```$this->writeln('project installed');```
6. (tbd) Use $this->question(); to send a question input. Example: ```$answer = $this->question('Description for Github project', 'This is a default description');```
7. Use project configuration: ``` $config['project']['mykey'] ``` (tbd)->(usage like $this->getProjectConfig('mykey') )
8. Use task configuration: ```$config['options']['task-option-key']``` (tbd)->(usage like $this->getOption('description') )

Example:

```
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