<?php
namespace Butler\Task;

use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NeosTask extends AbstractTask
{
    /**
     * @var \Butler\Helper\YamlHelper
     */
    protected $yaml;

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
        $this->yaml = $this->helperSet->get('yaml'); // init filesystem helper
        $this->fs = $this->helperSet->get('filesystem'); // init filesystem helper
    }


    /**
     * @param array $config
     */
    public function settings(array $config) {

        // load existing settings if exists and merge with new settings
        if ( $this->fs->exists($config['options']['filename']))
        {
            try {
                // parse yaml from file
                $settings = $this->yaml::parse(file_get_contents($config['options']['filename']));
                // merge distinct existing and new settings
                $config['options']['settings'] = $this->yaml::arrayMergeDistinct(
                    $settings,
                    $config['options']['settings']
                );
            } catch ( \Symfony\Component\Yaml\Exception\ParseException $e) {
                // ToDo: refactor exeptionhandling
                printf("Unable to parse the YAML string: %s", $e->getMessage());
            }
        }

        // dump settings to yaml and in the file
        $this->fs->dumpFile(
            $config['options']['filename'],
            $this->yaml::dump($config['options']['settings'],20,2)
        );
    }

}