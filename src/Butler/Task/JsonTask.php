<?php
namespace Butler\Task;

use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class JsonTask extends AbstractTask
{
    /**
     * @var \Butler\Helper\YamlHelper
     */
    protected $json;

    /**
     * @var \Butler\Helper\FilesystemHelper
     */
    protected $fileSystem;

    /**
     * JsonTask constructor.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param HelperSet $helperSet
     */
    public function __construct(InputInterface $input, OutputInterface $output, HelperSet $helperSet)
    {
        parent::__construct($input, $output, $helperSet);
        $this->json = $this->helperSet->get('json'); // init filesystem helper
        $this->fileSystem = $this->helperSet->get('filesystem'); // init filesystem helper
    }


    /**
     * create new json file
     *
     * @param array $config
     *
     * create:
     *   class: \Butler\Task\JsonTask
     *   task: create
     *   options:
     *     filename: MyFile.json
     *     data:
     *       'array1':
     *         'data1': 'demo data 1'
     *         'data2': 'demo data 2'
     *       'array2': 'demo data'
     */
    public function create(array $config)
    {
        if (!$this->fileSystem->exists($this->fileSystem->getPath($config['options']['filename']))) {
            try {
                $this->fileSystem->dumpFile($this->fileSystem->getPath($config['options']['filename']), json_encode($config['options']['data'], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            } catch (Exception $e) {
                $this->output->writeln('<error><options=bold;bg=red>  ERR </></error> <fg=red>"json:create" ' . $e->getMessage() .'</>');
            }
        } else {
            $this->output->writeln( '<error><options=bold;bg=red>  ERR </></error> <fg=red>"json:create" File "' . $config['options']['filename'] . '" already exist!</>');
        }
    }


    /**
     * update existing json file
     *
     * @param array $config
     * usage:
     * update:
     *   class: \Butler\Task\JsonTask
     *   task: update
     *   options:
     *     filename: MyFile.json
     *     data:
     *       'array1':
     *         'data1': 'demo data 1'
     *         'data3': 'demo data 3'
     *       'array2': 'demo data'
     */
    public function update(array $config)
    {
        if (!$this->fileSystem->exists($this->fileSystem->getPath($config['options']['filename']))) {
            $this->output->writeln( 'File "' . $config['options']['filename'] . '" does not exist but will be created!');
            $this->create($config);
            return;
        }
        $data = json_decode(file_get_contents($this->fileSystem->getPath($config['options']['filename'])), true);
        $config['options']['data'] = $this->json::arrayMergeDistinct($data, $config['options']['data']);
        $this->fileSystem->dumpFile($this->fileSystem->getPath($config['options']['filename']), json_encode($config['options']['data'], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
}
