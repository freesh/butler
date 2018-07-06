# Create project config

## Create a project file

1. Create a new projectname.yaml file in _~/Butler/Project/_.
2. Configure your tasks.
3. Use your task: ```$ butler project:run projectname```
4. If your projectfile is located in a subfolder like _~/Butler/Project/vendor/projectfile.yaml_, you have to use it like that: ```$ butler project:run vendor/projectname```

Example configuration:

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
