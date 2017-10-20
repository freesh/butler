<?php

namespace Butler\Project;

class NeosBaseProject extends AbstractProject
{

    /**
     * create tasks
     */
    public function createTasks() {

        # get vendor and project name
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


        # import neos demo site
        $this->addTask([
            'key' => 'import neos demo',
            'class' => '\\Butler\\Task\\NeosTask',
            'task' => 'siteImport',
            'options' => [
                'context' => 'Development', // optional | String default: Development
                'package' => 'Neos.Demo'
            ],
        ]);


        # kickstart a new site package
        $this->addTask([
            'key' => 'kickstart site',
            'class' => '\\Butler\\Task\\NeosTask',
            'task' => 'kickstartSite',
            'options' => [
                'context' => 'Development', // optional | String default: Development
                'package-key' => '{projectvendor}.Site',
                'site-name' => '{projectname}'
            ],
        ]);


        # create a new site
        $this->addTask([
            'key' => 'create site',
            'class' => '\\Butler\\Task\\NeosTask',
            'task' => 'siteCreate',
            'options' => [
                'context' => 'Development', // optional | String default: Development
                'package-key' => '{projectvendor}.Site',
                'site-name' => '{projectname}'
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


        # Stop Docker ...
        $this->addTask([
            'key' => 'The End :)',
            'class' => '\\Butler\\Task\\InputTask',
            'task' => 'writelines',
            'options' => [
                'command' => 'Finished',
                '....'
            ],
        ]);


        return 'tasks created :))';
    }

}

# init composer project
#$this->composerTask = new ComposerTask();



#$this->composerTask->create('neos/neos-base-distribution');
#$this->task('composer create-project --no-dev neos/neos-base-distribution '.$PATH_TEMP);



/*



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
