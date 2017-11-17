<?php
namespace Butler\Task;

use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GithubTask extends AbstractTask
{

    /**
     * @var \Github\Client
     */
    private $client = null;

    public function __construct(InputInterface $input, OutputInterface $output, HelperSet $helperSet)
    {
        parent::__construct($input, $output, $helperSet);

    }

    /**
     * @param array $config
     * task config:
     *
     */
    public function auth(array $config) {

        try {
            if ($this->client === null) {
                $this->client = new \Github\Client();
                $this->client->authenticate($config['options']['token'], null, \Github\Client::AUTH_HTTP_TOKEN);
            }
        } catch ( Exception $e) {
            $this->output->writeln('<error><options=bold;bg=red>  ERR </></error> <fg=red>"github:auth" ' . $e->getMessage() .'</>');
        }
    }

    /**
     * @param array $config
     * task config:
     *
     */
    public function repositoryCreate(array $config) {

        try {
            // Create repo
            $repo = $this->client->api('repo')->create(
                $config['options']['name'],
                (isset($config['options']['description']) ? $config['options']['description'] : ''),
                (isset($config['options']['homepage']) ? $config['options']['homepage'] : ''),
                (isset($config['options']['public']) ? $config['options']['public'] : true)
            );

            return ['github' => $repo];

        } catch ( \Github\Exception\ValidationFailedException $e ) {
                $this->output->writeln('<error><options=bold;bg=red>  ERR </></error> <fg=red>"github:repositoryCreate" The Repository "' . $config['options']['name'] . '" already exist.</>');
        } catch ( \Github\Exception\RuntimeException $e ) {
                $this->output->writeln('<error><options=bold;bg=red>  ERR </></error> <fg=red>"github:repositoryCreate" ' . $e->getMessage() .'</>');
        }
    }


    /**
     * @param array $config
     * task config:
     *
     */
    public function repositoryRemove(array $config) {

        try {
            // Remove repo
            $this->client->api('repo')->remove($config['options']['user'], $config['options']['name']); // Get the deletion token
        } catch (Exception $e) {
            $this->output->writeln('<error><options=bold;bg=red>  ERR </></error> <fg=red>"github:repositoryDelete" ' . $e->getMessage() .'</>');
        }

    }

}
