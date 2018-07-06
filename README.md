# Butler

Note: This in wip state. ;)

Butler is a php taskrunner for creating and initialising web projects.
You can define tasks for composer, git, github, docker, sftp, file operations and other things ...

**[Dokumentation:](doku/index.md)**

Basics
- [Install](doku/basics/install.md)
- [Getting started](doku/basics/getting-started.md)
- [CLI commands](doku/basics/cli.md)

Project Configuration
- [Create project config](doku/project-config/create-config.md)
- [Variables](doku/project-config/variables.md)
- [Conditions](doku/project-config/conditions.md)
- [Debug](doku/project-config/debug.md)

Development

- [Create own tasks (deprecated: Will change in future)](doku/development/own-tasks.md)



## Getting started

**1. Install butler**

- See: [Install](doku/basics/install.md)

**2. create empty project folder**

```bash
cd myProjectfolder
```

**3. create project**

```bash
$ butler project:run neos-base
```

This will init a complete new neos project on your host system. Cli will ask you some questions about your project like projectname. 

But if you create your own project config you can create the whole infrasructure at the same time. For example setup dev and live server, deployment, github or gitlab project and deploy directly to these servers and repositories.


## Easy task configuration with yaml

_Example configuration:_

```yaml
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

# // ... //

'Neos kickstart site':
  class: \Butler\Task\NeosTask
  task: kickstartSite
  options:
    context: Development
    site-name: '{projectname}'

# // ...
```
