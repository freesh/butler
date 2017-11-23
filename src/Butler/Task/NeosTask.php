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
     */
    public function kickstartSite(array $config)
    {
        $context = 'Development';
        if (isset($config['options']['context'])) {
            $context = $config['options']['context'];
        }
        $this->execute(
            'export FLOW_CONTEXT='. $context .' && '
            .'./flow kickstart:site '
            .'--package-key ' . ucwords(
                (isset($config['options']['package-key'])? $config['options']['package-key'] : $this->setQuestion('<options=bold;bg=cyan>  ASK </> <fg=cyan>Please add the package key: </> ', null)),
                '.'
            )
            .' --site-name "' . ucfirst(
                (isset($config['options']['site-name'])? $config['options']['site-name'] : $this->setQuestion('<options=bold;bg=cyan>  ASK </> <fg=cyan>Please add the site name: </> ', null))
            ) .'"'
        );
    }
}
