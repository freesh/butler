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
    protected $fileSystem;

    /**
     * DockerTask constructor.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param HelperSet $helperSet
     */
    public function __construct(InputInterface $input, OutputInterface $output, HelperSet $helperSet)
    {
        parent::__construct($input, $output, $helperSet);
        $this->fileSystem = $this->helperSet->get('filesystem'); // init filesystem helper
    }


    /**
     * Create a docker-compose.yaml with config
     *
     * @param array $config
     */
    public function settings(array $config)
    {
        // load existing settings if exists and merge with new settings
        if ($this->fileSystem->exists($config['options']['filename'])) {
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
        $this->fileSystem->dumpFile(
            $config['options']['filename'],
            $this->yaml::dump($config['options']['settings'], 20, 2)
        );
    }

    /**
     * creating a dockerfile
     *
     * @param array $config
     */
    public function dockerfile(array $config)
    {
        $lines = $this->parseDockerCmd($config['options']['cmd']);
        $this->fileSystem->dumpFile($config['options']['path'].'Dockerfile', $lines);
    }


    /**
     * Parse multidimensional array to multi line string
     *
     * @param array $config
     * @return string
     */
    private function parseDockerCmd(array $config)
    {
        $lines = '';
        foreach ($config as $key => $cmd) {
            if (is_array($cmd)) {
                if (is_string($key) && !empty($cmd[0])) {
                    $count = 0;
                    foreach ($cmd as $cmdItem) {
                        if ($count == 0) {
                            $lines .= $key . ' ' . $cmdItem . " \ \n";
                        } else {
                            $lines .= '    && ' . $cmdItem . " \ \n";
                        }
                        $count++;
                    }
                } elseif (is_int($key)) {
                    $lines .= $this->parseDockerCmd($cmd);
                }
            } else {
                $lines .= $key.' '.$cmd." \n";
            }
        }
        return $lines;
    }
}
