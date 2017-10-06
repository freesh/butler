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

```butler project:create neos-base Vendor ProjectName```


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
                'projectvendor' => 'What is the vendor of your project?'
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
                'files' => '{projectvendor}-{projectname}.txt'
            ]
        ]);
```
The first task will ask the user for vendor and name and the second task creates a file named by the answers.


## Create new Task Driver

1. Create a new task class in ```src/Butler/Task/``` extending ```AbstractTask```.
2. Now you can create public functions with a $config param which is an array. (function name = task name)
3. If a task function return an array, the values are merged in to the project config.
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