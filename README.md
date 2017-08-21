# Butler
Butler is a small taskmanager for creating and initialising web projects.
You can define tasks for composer, git, docker and and and...

The future goal of this project is to create tasks for initializing the complete project stack.
For example:
- clone neos distribution
- init and start docker
- init configuration
- migrate database
- create sitepackage and site
- init a new project on github or gitlab
- setup remote server on digitalocean or another hoster with an api or just ssh
- setup deployment with deployer
...


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

