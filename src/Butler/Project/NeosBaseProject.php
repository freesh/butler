<?php

namespace Butler\Project;

class NeosBaseProject extends AbstractProject
{

    /**
     * create tasks
     */
    public function createTasks() {

        /*$this->addTask([
            'key' => 'set project data',
            'class' => '\\Butler\\Task\\InputTask',
            'task' => 'question',
            'options' => [
                'projectname' => 'What is the name of your Project?',
                'projectvendor' => 'What is the vendor name of your Project?'
            ],
        ]);*/

        /*$this->addTask([
            'key' => 'touch projectvendor',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'touch',
            'options' => [
                'files' => '{projectvendor}-{projectname}.txt', // string|array|\Traversable A filename, an array of files, or a \Traversable instance to create
                'time' => null, // (optional) int The touch time as a Unix timestamp
                'atime' => null // (optional) int The access time as a Unix timestamp
            ],
            'condition' => 'projectname != projectvendor'
        ]);*/

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


        $this->addTask([
            'key' => 'Neos dev settings',
            'class' => '\\Butler\\Task\\NeosTask',
            'task' => 'settings',
            'options' => [
                'filename' => 'Configuration/Development/Settings.yaml', // string path to Settings.yaml; Existing file will be merged.
                'settings' => [ // array with setting structure
                    'Neos' => [
                        'Flow' => [
                            'persistence' => [
                                'backendOptions' => [
                                    'dbname' => 'application',
                                    'user' => 'toor',
                                    'password' => 'toor',
                                    'host' => '0.0.0.0',
                                    'port' => 8086
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);


        $this->addTask([
            'key' => 'Neos global settings',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'copy',
            'options' => [
                'originFile' => 'Configuration/Settings.yaml.example',
                'targetFile' => 'Configuration/Settings.yaml'
            ]
        ]);


        // ToDo: create a special yaml task and remove neos/settings task?
        $this->addTask([
            'key' => 'Create docker-compose.yml',
            'class' => '\\Butler\\Task\\NeosTask',
            'task' => 'settings',
            'options' => [
                'filename' => 'docker-compose.yml', // string path to Settings.yaml; Existing file will be merged.
                'settings' => [ // array with setting structure
                    'version' => '2',
                    'services' => [
                        'db' => [
                            'image' => 'mysql:5.7',
                            'volumes' => [
                                "./Data/Docker/Database:/var/lib/mysql"
                            ],
                            'restart' => 'always',
                            'ports' => [
                                '8086:3306'
                            ],
                            'command' => [
                                '--character-set-server=utf8',
                                '--collation-server=utf8_unicode_ci'
                            ],
                            'environment' => [
                                'MYSQL_ROOT_PASSWORD' => 'toor',
                                'MYSQL_DATABASE' => 'application',
                                'MYSQL_USER' => 'toor',
                                'MYSQL_PASSWORD' => 'toor',
                            ],
                        ],
                        'phpmyadmin' => [
                            'image' => 'phpmyadmin/phpmyadmin',
                            'environment' => [
                                'PMA_HOST=db',
                                'PMA_PORT=3306',
                                'PMA_USER=toor',
                                'PMA_PASSWORD=toor'
                            ],
                            'restart' => 'always',
                            'ports' => [
                                '8082:80'
                            ],
                            'volumes' => [
                                '/sessions'
                            ]
                        ],
                    ]
                ]
            ]
        ]);

        // create dockerfile
        $this->addTask([
            'key' => 'init dockerfile',
            'class' => '\\Butler\\Task\\DockerTask',
            'task' => 'dockerfile',
            'options' => [
                'path' => 'Build/Docker/',
                'cmd' => [
                    'FROM' => 'php:7-fpm-alpine',
                    'ENV' => 'IMAGICK_VERSION 3.4.1',
                    'RUN' => 'docker-php-ext-install pdo pdo_mysql gd',
                    #'RUN' => [
                    #    'apk add --no-cache bash imagemagick-dev ssmtp libtool autoconf gcc g++ make',
                    #    'pecl install imagick-$IMAGICK_VERSION',
                    #    'echo "extension=imagick.so" > /usr/local/etc/php/conf.d/ext-imagick.ini',
                    #    'apk del libtool autoconf gcc g++ make'
                    #],
                    #['RUN' => 'docker-php-ext-install pdo pdo_mysql gd'],
                    [
                        'RUN' => [
                            'apk add --no-cache bash imagemagick-dev ssmtp libtool autoconf gcc g++ make',
                            'pecl install imagick-$IMAGICK_VERSION',
                            'echo "extension=imagick.so" > /usr/local/etc/php/conf.d/ext-imagick.ini',
                            'apk del libtool autoconf gcc g++ make'
                        ]
                    ]
                ]
            ],
        ]);


        # update composer.json
        /*$this->addTask([
            'key' => 'update composer.json',
            'class' => '\\Butler\\Task\\JsonTask',
            'task' => 'add',
            'options' => [
                'file' => 'composer.json',
                'data' => [
                    'scripts' => ''
                ]
            ],
        ]);*/

        # Init Docker ...
        $this->addTask([
            'key' => 'docker-compose up',
            'class' => '\\Butler\\Task\\CommandTask',
            'task' => 'command',
            'options' => [
                'command' => 'docker-compose up -d'
            ],
        ]);

        # Init mySQL
        $this->addTask([
            'key' => 'waiting for database',
            'class' => '\\Butler\\Task\\CommandTask',
            'task' => 'command',
            'options' => [
                'command' => 'while ! mysqladmin ping -h0.0.0.0 --port=8086 --silent; do sleep 1 ;done'
            ],
        ]);

        # migrate database
        $this->addTask([
            'key' => 'migrate neos database',
            'class' => '\\Butler\\Task\\NeosTask',
            'task' => 'doctrineMigrate',
            'options' => [
                'context' => 'Development', // optional | String
            ],
        ]);

        # Create Admin [admin:admin]'
        $this->addTask([
            'key' => 'create neos admin',
            'class' => '\\Butler\\Task\\NeosTask',
            'task' => 'createUser',
            'options' => [
                'context' => 'Development', // optional | String default: Development
                'user' => 'admin',
                'password' => 'admin',
                'username' => 'King Loui',
                'roles' => [
                    'Neos.Neos:Administrator'
                ]
            ],
        ]);

        # Stop Docker ...
        $this->addTask([
            'key' => 'docker-compose down',
            'class' => '\\Butler\\Task\\CommandTask',
            'task' => 'command',
            'options' => [
                'command' => 'docker-compose down'
            ],
        ]);
/*


                # import site package
                ##export FLOW_CONTEXT=Development && ./flow site:import --package-key Neos.Demo


                # create site package
                #echo "Create Sitepackage $VENDOR_NAME.Site"
                #$this->task('export FLOW_CONTEXT=Development && ./flow kickstart:site --package-key '.$input->getArgument('vendor').'.Site --site-name '.$input->getArgument('projectname'));

                # create page
                #echo "Create Page $PAGE_NAME"
                #$this->task('export FLOW_CONTEXT=Development && ./flow site:create '.$input->getArgument('projectname').' '.$input->getArgument('vendor').'.Site');
        */

        return 'tasks created :))';
    }

}

# init composer project
#$this->composerTask = new ComposerTask();



#$this->composerTask->create('neos/neos-base-distribution');
#$this->task('composer create-project --no-dev neos/neos-base-distribution '.$PATH_TEMP);



/*
        # move all to root
        $this->task('mv '.$PATH_TEMP.'/* '.$PATH_TEMP.'/.g* '.$PATH_ROOT.'/');



        $this->task('cd '.$PATH_TEMP);
        # import docker config
        $this->task('cp -R ~/Tools/Docker/* ./');

        # copy development Settings.yaml
        $this->task('cp ~/Tools/Neos/Build/Templates/Configuration/Development/Settings.yaml ./Configuration/Development');
        $this->task('mv ./Configuration/Settings.yaml.example ./Configuration/Settings.yaml');

        $this->task('cp ~/Tools/Neos/Build/composer.php ./Build');

        # update composer.json
        $this->task('php ./Build/composer.php');

        # Init Docker ...
        $this->task('docker-compose up -d');

        # Init mySQL
        $this->task('while ! mysqladmin ping -h0.0.0.0 --port=8086 --silent; do sleep 1 ;done');

        # migrate database
        $this->task('export FLOW_CONTEXT=Development && ./flow doctrine:migrate');

        # Create Admin [admin:admin]'
        $this->task('export FLOW_CONTEXT=Development && ./flow user:create admin admin King Loui --roles Neos.Neos:Administrator');

        # import site package
        #export FLOW_CONTEXT=Development && ./flow site:import --package-key Neos.Demo

        # create site package
        #echo "Create Sitepackage $VENDOR_NAME.Site"
        $this->task('export FLOW_CONTEXT=Development && ./flow kickstart:site --package-key '.$input->getArgument('vendor').'.Site --site-name '.$input->getArgument('projectname'));

        # create page
        #echo "Create Page $PAGE_NAME"
        $this->task('export FLOW_CONTEXT=Development && ./flow site:create '.$input->getArgument('projectname').' '.$input->getArgument('vendor').'.Site');

        #####################
        # init atomic fusion
        #####################
        #start_spinner "Add packagefactory/atomicfusion"
        $this->task('composer require packagefactory/atomicfusion');

        # TODO: configure

        ########################
        # init atomic fusion-afx
        ########################
        #start_spinner "Add packagefactory/atomicfusion-afx"
        $this->task('composer require packagefactory/atomicfusion-afx:~3.0.0');

        #TODO: Configure

        #####################
        # init monocle
        #####################
        #start_spinner "Add sitegeist/monocle"
        $this->task('composer require sitegeist/monocle');

        # ToDo: configure

        #####################
        # init magickwand
        #####################
        #start_spinner "Add sitegeist/magicwand"
        $this->task('composer require --dev sitegeist/magicwand:dev-master');

        # ToDo: configure


        ########################
        # init tests and linting
        ########################
        #start_spinner "Add sitegeist/neosguidelines"
        $this->task('composer require --dev sitegeist/neosguidelines');

        # ToDo: configure


        ########################
        # remove unused packages
        ########################
        #start_spinner "Remove neos/demo"
        $this->task('composer remove neos/demo');


        #####################
        # init git repository
        #####################
        #echo "Init git"
        #start_spinner 'Init git'
        $this->task('git init');

        #####################
        # init deployment
        #####################
        # tbd: call deployment.sh

        #####################
        # init README.md
        #####################
        # tbd: call readme.sh
        #start_spinner 'Create README.md'
        $this->task('touch README.md');


        #####################
        # tidy up
        #####################
        # remove initial temp folder
        $this->task('rm -Rf $PATH_TEMP');
        $this->task('docker-compose down');

        # remove unused composer packages

        # flush cache (because of deleted neos packages)
        $this->task('export FLOW_CONTEXT=Development && ./flow flow:cache:flush');

        # start neos server
        #$this->task('export FLOW_CONTEXT=Development && ./flow server:run');
*/

#$output->writeln(sprintf('Created the file %s', $path));
/*$output->writeln([
    'Neos is configured:',
    '===================',
    'To start Server run: ',
    'docker-compose up -d',
    'export FLOW_CONTEXT=Development && ./flow server:run'
]);*/
