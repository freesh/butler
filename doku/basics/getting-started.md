# Getting started

## Init Projects

**1. Install butler**

- See: [Install](install.md)

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
