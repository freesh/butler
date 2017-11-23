# Butler
Butler is a small taskrunner for creating and initialising web projects.
You can define tasks for composer, git, docker and and and...

The future goal of this project is to create tasks for initializing the complete project stack.
For example:

- Ask project name and vendor
- Init project on github/gitlab
- Init project on your projectmanagement tool
- Init dev, stage, and live server (ssh on your hoster, or with api on aws, digitalocean and other services)
- Init CI tool (gitlabci, usw...)
- Init project distribution
- Init and start docker (or vagrant)
- Init configuration
- Call setup routines for project distribution
- Setup deployment with deployer
- Push Project to git
- Deploy Project to dev server
- Create your awesome project. =)



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

**Create neos base Project:**

- Go to your empty Projectfolder
- Execute butler command

Parameter "neos-base" stands for NeosBase project and is configures in NeosBaseProject.php/yaml

```butler project:run neos-base```

Execute just some specific tasks: --task or -t

```butler project:run neos-base --task="my task1 key" --task="my task4 key"```

Execute with special path for butler files (default: ~/Butler)

```butler project:run neos-base --task="./Build/Butler"```

**Help:**

```butler``` or ```butler --help```


## Create Project

1. Create a new project class in ```src/Butler/Project/``` extending ```AbstractProject```and create the public "createTask" function.
2. Use ```$this->addTask()``` to add your tasks. (See available tasks in ```src/Butler/Task/```)
3. Use your task: ```$ butler project:create my-new-site``

Example:

```
<?php

namespace Butler\Project;

class MyNewSiteProject extends AbstractProject
{

    /**
     * create tasks
     */
    public function createTasks() {

        $this->addTask([
            'key' => 'project-data',
            'class' => '\\Butler\\Task\\InputTask',
            'task' => 'question',
            'options' => [
                'projectname' => 'What is the name of your project?',
                'projectvendor' => 'What is the vendor of your project?'
            ],
        ]);

        $this->addTask([
            'key' => 'create',
            'class' => '\\Butler\\Task\\ComposerTask',
            'task' => 'create',
            'options' => [
                'distribution' => 'neos/neos-base-distribution',
                'tempPath' => 'temp',
                'params' => [
                    '--no-dev'
                ]
            ],
        ]);

        $this->addTask([
            'key' => 'require',
            'class' => '\\Butler\\Task\\ComposerTask',
            'task' => 'add',
            'options' => [
                'package' => 'packagefactory/atomicfusion packagefactory/atomicfusion-afx:~3.0.0 sitegeist/monocle'
            ],
        ]);

        $this->addTask([
            'key' => 'require-dev',
            'class' => '\\Butler\\Task\\ComposerTask',
            'task' => 'add',
            'options' => [
                'package' => 'sitegeist/magicwand:dev-master sitegeist/neosguidelines',
                'params' => [
                    '--dev'
                ]
            ],
        ]);
    }
}
```

### Modify and use project config

Ask the user some interesting questions with the question task of the inputTask Driver:
```
        $this->addTask([
            'key' => 'project-data',
            'class' => '\\Butler\\Task\\InputTask',
            'task' => 'question',
            'options' => [
                'projectname' => 'What is the name of your project?',
                'projectvendor' => 'What is the vendor of your project?',
                'level1.sub1.myvar' => 'first tree in level1',
                'level1.sub2.myvar' => 'different tree in level1'
            ],
        ]);
 ```

The option keys "projectname" and "projectvendor" will be stored in a project configuration.
This config variables can be used in task configuration like this:
```
        $this->addTask([
            'key' => 'touch file',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'touch',
            'options' => [
                'files' => [
                    '{projectvendor}-{projectname}.txt',
                    '{level1.sub1.myvar}.txt',
                    '{level1.sub2.myvar}.txt'
                ]
            ]
        ]);
```
The first task will ask the user for vendor and name and the second task creates a file named by the answers.


### Using conditions in task configuration

The execution of every task can skipped by condition:

```
        $this->addTask([
            'key' => 'set project data',
            'class' => '\\Butler\\Task\\InputTask',
            'task' => 'question',
            'options' => [
                'projectname' => 'What is the name of your Project?',
                'projectvendor' => 'What is the vendor name of your Project?'
            ],
        ]);

        $this->addTask([
            'key' => 'touch projectvendor',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'touch',
            'options' => [
                'files' => '{projectvendor}-{projectname}.txt',
            ],
            'condition' => 'projectname != projectvendor'
        ]);
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

### Debug "Task-" and "Runtime-Config"

``` 
        $this->addTask([
            'key' => 'github create repository',
            'class' => '\\Butler\\Task\\GithubTask',
            'task' => 'repositoryCreate',
            'options' => [
                ...
            ],
            'debug' => true, // bool | optional default: false
            'debug-depth' => '3', // int | optional (default: -1)
            'debug-path' => 'project.github', // string | optional (project.my.option)
            'debug-type' => 'export' // string | optional [export|print] (default: print) 
        ]);
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


## Create new Task Driver

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