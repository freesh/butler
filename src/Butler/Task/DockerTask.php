<?php
namespace Butler\Task;

use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DockerTask extends AbstractTask
{

    /**
     * @var \Butler\Helper\FilesystemHelper
     */
    protected $fs;

    /**
     * FilesystemTask constructor.
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param HelperSet $helperSet
     */
    public function __construct(InputInterface $input, OutputInterface $output, HelperSet $helperSet)
    {
        parent::__construct($input, $output, $helperSet);
        $this->fs = $this->helperSet->get('filesystem'); // init filesystem helper
    }


    /**
     * @param array $config
     */
    public function settings(array $config)
    {

        // load existing settings if exists and merge with new settings
        if ($this->fs->exists($config['options']['filename'])) {
            try {
                // parse yaml from file
                $settings = $this->yaml::parse(file_get_contents($config['options']['filename']));
                // merge distinct existing and new settings
                $config['options']['settings'] = $this->yaml::arrayMergeDistinct(
                    $settings,
                    $config['options']['settings']
                );
            } catch (\Symfony\Component\Yaml\Exception\ParseException $e) {
                // ToDo: refactor exeptionhandling
                printf("Unable to parse the YAML string: %s", $e->getMessage());
            }
        }

        // dump settings to yaml and in the file
        $this->fs->dumpFile(
            $config['options']['filename'],
            $this->yaml::dump($config['options']['settings'], 20, 2)
        );
    }

    /**
     * creating a dockerfile
     * @param array $config
     */
    public function dockerfile(array $config)
    {
        $cmds = $this->parseDockerCmd($config['options']['cmd']);

        $this->fs->dumpFile($config['options']['path'].'Dockerfile', $cmds);
    }


    /**
     * @param array $config
     * @return string
     */
    private function parseDockerCmd(array $config)
    {
        $cmds = '';

        // if $key === string && $value === string | render value as string

        // if $key === string && $value === array int | render values in for each

        // if $key === int and $value === array assoc | recursive function call

        foreach ($config as $key => $cmd) {
            if (is_array($cmd)) {
                /*
                 * 'RUN' => [
                        'apk add --no-cache bash imagemagick-dev ssmtp libtool autoconf gcc g++ make',
                        'pecl install imagick-$IMAGICK_VERSION',
                        'echo "extension=imagick.so" > /usr/local/etc/php/conf.d/ext-imagick.ini',
                        'apk del libtool autoconf gcc g++ make'
                    ],
                 */
                if (is_string($key) && !empty($cmd[0])) { // ['RUN' => 'docker-php-ext-install pdo pdo_mysql gd']
                    $count = 0;
                    foreach ($cmd as $cmdItem) {
                        if ($count == 0) {
                            $cmds .= $key . ' ' . $cmdItem . " \ \n";
                        } else {
                            $cmds .= '    && ' . $cmdItem . " \ \n";
                        }
                        $count++;
                    }
                }

                /*
                 *  ['RUN' => 'docker-php-ext-install pdo pdo_mysql gd'],

                    or:

                    [
                        'RUN' => [
                            'apk add --no-cache bash imagemagick-dev ssmtp libtool autoconf gcc g++ make',
                            'pecl install imagick-$IMAGICK_VERSION',
                            'echo "extension=imagick.so" > /usr/local/etc/php/conf.d/ext-imagick.ini',
                            'apk del libtool autoconf gcc g++ make'
                        ]
                    ]
                 */
                elseif (is_int($key)) {
                    $cmds .= $this->parseDockerCmd($cmd);
                }
            } else { // 'FROM' => 'php:7-fpm-alpine'

                // render
                $cmds .= $key.' '.$cmd." \n";
            }
        }
        return $cmds;
    }
}
