# Debug

## Debug Task output

```butler project:run neos-base -v```

```bash butler project:run neos-base -vv```

```butler project:run neos-base -vvv```

## Debug "Task-" and "Runtime-Config"

```yaml
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
