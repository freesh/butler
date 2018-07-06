## CLI:

**List comands**

```bash
$ butler list
```

**List project configurations**

```bash
$ butler project:list
```

**Create project:**

```bash
$ cd emptyProjectfolder
$ butler project:run neos-base
```

Execute just some specific tasks from project config: --task or -t

```bash
$ butler project:run neos-base --task="myTask1Key" --task="my task2 key"
```

Execute with special path for butler files (default: ~/Butler)

```bash
$ butler project:run neos-base --projectPath="./Build/Butler"
```

**Help:**

```$ butler``` or ```$ butler --help```

**Help for a specific command**

```bash
$ butler help command:name
```
