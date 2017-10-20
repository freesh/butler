<?php

namespace Butler\Project;

class DockerProject extends AbstractProject
{

    /**
     * create tasks
     */
    public function createTasks() {


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
                    ['RUN' => 'docker-php-ext-install pdo pdo_mysql gd'],
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

        return 'tasks created :))';
    }
}

