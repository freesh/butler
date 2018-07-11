# Variables

You can use variables within your configuration. There are three ways to define them:

- Defined in task config.
- Defined as environment variables
- Returned by task. Variables can be returned by tasks. See documentation of this specific task.

Debug: If you build your own project config, it is often usefull to debug the rendering of your project config if variables are used. See [debug](./debug.md) section for more informations about that.

Note: Variables can be used as array tree with point notation. This is interesting if variable sets are returned from tasks to your runtime configuration.

#### Example:

Ask the user some interesting questions with the question task of the inputTask Driver:
```yaml
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
```yaml
'touch file':
  class: \Butler\Task\FilesystemTask
  task: touch
  options:
    files:
      - '{projectvendor}-{projectname}.txt'
      - '{level1.sub1.myvar}.txt'
      - '{level1.sub2.myvar}.txt'
      - '{ENVVARIABLE}.txt'
```
The first task will ask the user for vendor and name and the second task creates a file named by the answers.
Some Tasks return data to the runtime config. This can also used. 

### Use environment variables

```bash
projectname="myproject" butler project:run neos
```

If the environment variable is **not** overwriten within the yaml file, butler will look in your environment for this variable and use this value.
