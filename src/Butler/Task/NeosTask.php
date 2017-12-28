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
    protected $fileSystem;

    /**
     * NeosTask constructor.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param HelperSet $helperSet
     */
    public function __construct(InputInterface $input, OutputInterface $output, HelperSet $helperSet)
    {
        parent::__construct($input, $output, $helperSet);
        $this->yaml = $this->helperSet->get('yaml'); // init filesystem helper
        $this->fileSystem = $this->helperSet->get('filesystem'); // init filesystem helper
    }


    /**
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
     * @param array $config
     */
    public function doctrineMigrate(array $config)
    {
        $context = 'Development';
        // set context to Production
        if (isset($config['options']['context'])) {
            $context = $config['options']['context'];
        }
        // execute command
        $this->execute('export FLOW_CONTEXT='. $context .' && ./flow doctrine:migrate');
    }


    /**
     * @param array $config
     */
    public function createUser(array $config)
    {
        $context = 'Development';
        // set context to Production
        if (isset($config['options']['context'])) {
            $context = $config['options']['context'];
        }
        if (!isset($config['options']['user'])) {
            $config['options']['user'] = $this->setQuestion('<options=bold;bg=cyan>  ASK </> <fg=cyan>Username: </> ', null);
        }
        if (!isset($config['options']['password'])) {
            $config['options']['password'] = $this->setQuestion('<options=bold;bg=cyan>  ASK </> <fg=cyan>Password: </> ', null);
        }
        // execute command
        $this->execute('export FLOW_CONTEXT='. $context .' && ./flow user:create '. $config['options']['user'] .' '. $config['options']['password'] .' '. $config['options']['username'] . (!isset($config['options']['roles']) ? '' : ' --roles ' . implode(' ', $config['options']['roles'])));
    }


    /**
     * Import a site from a package
     *
     * @param array $config
     */
    public function siteImport(array $config)
    {
        $context = 'Development';
        if (isset($config['options']['context'])) {
            $context = $config['options']['context'];
        }
        $this->execute('export FLOW_CONTEXT='. $context .' && ./flow site:import --package-key '. $config['options']['package']);
    }


    /**
     * Create a site
     *
     * @param array $config
     */
    public function siteCreate(array $config)
    {
        $context = 'Development';
        if (isset($config['options']['context'])) {
            $context = $config['options']['context'];
        }
        $this->execute(
            'export FLOW_CONTEXT='. $context .' && '
            .'./flow site:create "' . ucfirst(
                (isset($config['options']['site-name'])? $config['options']['site-name'] : $this->setQuestion('<options=bold;bg=cyan>  ASK </> <fg=cyan>Please add the site name: </> ', null))
            ) .'" '
            . ucwords(
                (isset($config['options']['package-key'])? $config['options']['package-key'] : $this->setQuestion('<options=bold;bg=cyan>  ASK </> <fg=cyan>Please add the package key: </> ', null)),
                '.'
            )
        );
    }


    /**
     * kickstart a site package
     *
     * @param array $config
     * @return array
     */
    public function kickstartSite(array $config)
    {
        $context = 'Development';
        if (isset($config['options']['context'])) {
            $context = $config['options']['context'];
        }
        if (!isset($config['options']['package-key'])) {
            $config['options']['package-key'] = $this->setQuestion('<options=bold;bg=cyan>  ASK </> <fg=cyan>Please add the package key: </> ', null);
        }
        if (!isset($config['options']['site-name'])) {
            $config['options']['site-name'] = $this->setQuestion('<options=bold;bg=cyan>  ASK </> <fg=cyan>Please add the site name: </> ', null);
        }
        $config['options']['site-name'] = ucfirst($config['options']['site-name']);
        $config['options']['package-key'] = ucwords($config['options']['package-key'],'.');
        $this->execute(
            'export FLOW_CONTEXT='. $context .' && ' .'./flow kickstart:site ' .'--package-key ' . $config['options']['package-key'] .' --site-name "' . $config['options']['site-name'] .'"'
        );
        return [
            'neos' => [
                'site-package' => [
                    'key' => $config['options']['package-key'],
                    'name' => $config['options']['site-name'],
                    'composer-name' => $this->getJsonData(
                        'Packages/Sites/'.$config['options']['package-key'].'/composer.json',
                        'name'
                    )
                ]
            ]
        ];
    }


    /**
     * Load Json Data from a json file
     * ToDo: Put in global helper
     * @param $file
     * @param $path
     * @return array|mixed|void
     */
    private function getJsonData($file, $path) {
        if (!$this->fileSystem->exists($file)) {
            $this->output->writeln( 'File "' . $file . '" does not exist!');
            return false;
        }
        $data = json_decode(file_get_contents($file), true);
        return $this->arrayPathValue($path,$data);
    }

    /**
     * get the value of a array by key path example: this.is.my.path
     * ToDo: Put this in a global helper
     * @param $path
     * @param array $data
     * @return array|mixed
     */
    private function arrayPathValue($path, array $data)
    {
        $paths = explode(".", $path);
        // iterate over path and data array
        foreach ($paths as $seg) {
            isset($data[$seg]) ? $data = $data[$seg] : null;
        }
        return $data;
    }
}
