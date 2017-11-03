<?php
namespace Butler\Task;

use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use phpseclib\Net\SFTP;
use phpseclib\Crypt\RSA;

class SftpTask extends AbstractTask
{

    /**
     * @var \phpseclib\Net\SFTP
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

                // connect
                $this->client = new SFTP(
                    $config['options']['host'],
                    (!isset($config['options']['port']) ? '22' : $config['options']['port']),
                    (!isset($config['options']['timeout']) ? null : $config['options']['timeout'])
                );

                // if username is empty
                if(!isset($config['options']['username'])) {
                    $config['options']['username'] = $this->setQuestion('<options=bold;bg=cyan>  ASK </> <fg=cyan>SSH User: </> ', null);
                }

                // auth
                switch ((!isset($config['options']['auth_method']) ? 'default' : $config['options']['auth_method'])) {
                    case 'rsa':

                        // load private key
                        $key = new RSA();
                        $key->setPassword($config['options']['rsa_private_password']);
                        $key->loadKey(file_get_contents(
                            $this->getRsaPrivateKey(
                                (!isset($config['options']['rsa_private_file']) ? '~/.ssh/id_rsa' : $config['options']['rsa_private_file'])
                            )
                        ));

                        // login
                        if (!$this->client->login(
                            $config['options']['username'],
                            $key
                        )) {
                            throw new \Exception('Cannot login into your server with rsa !');
                        }

                        break;

                    case 'userauth':
                    default:

                        // if password is empty
                        if(!isset($config['options']['password'])) {
                            $config['options']['password'] = $this->setQuestion('<options=bold;bg=cyan>  ASK </> <fg=cyan>SSH Password: </> ', null);
                        }

                        if (!$this->client->login(
                            $config['options']['username'],
                            (!isset($config['options']['password']) ? null : $config['options']['password'])
                        )) {
                            throw new \Exception('Cannot login into your server with username password !');
                        }
                }
            }

        } catch (Exception $e) {
            #$this->output->writeln('<error><options=bold;bg=red>  ERR </></error> <fg=red>"github:auth" is too drunk to work. Please run butler command with -v, -vv, or -vvv for more information.</>');
            #if($this->output->isVerbose()) $this->output->writeln('<fg=black;bg=white>'.$e->getMessage().'</>');

            echo 'Sftp Exception: ',  $e->getMessage(), "\n";
        }

    }

    /**
     * @param array $config
     * task config:
     *
     */
    public function list(array $config) {

        try {
            $list = $this->client->nlist((isset($config['options']['path']) ? $config['options']['path'] : '.'));
            #$list = $this->client->rawlist((isset($config['options']['path']) ? $config['options']['path'] : '.'));
            asort($list);
            foreach ($list as $id => $item) {
                if (!is_array($item)) {
                    $this->output->writeln($item);
                } else {
                    $this->output->writeln($id);
                    foreach ($item as $key => $value) {
                        $this->output->write(''.$key.':'.$value);
                    }
                    $this->output->writeln('');
                }
            }
        } catch (Exception $e) {
            #$this->output->writeln('<error><options=bold;bg=red>  ERR </></error> <fg=red>"github:repositoryCreate" is too drunk to work. Please run butler command with -v, -vv, or -vvv for more information.</>');
            #if($this->output->isVerbose()) $this->output->writeln('<fg=black;bg=white>'.$e->getMessage().'</>');

            echo 'SFTP Exception: ',  $e->getMessage(), "\n";
        }

    }


    /**
     * replace ~ with absolute user path
     * @param $path
     * @return mixed
     */
    private function getRsaPrivateKey($path)
    {

        // replace ~ with user path
        if (function_exists('posix_getuid') && strpos($path, '~') !== false) {
            $userinfo = posix_getpwuid(posix_getuid());
            $path = str_replace('~', $userinfo['dir'], $path);
        }

        // if file does not exist: ask for new path. :)
        if (!file_exists($path)) {
            $path = $this->getRsaPrivateKey(
                $this->setQuestion('<options=bold;bg=cyan>  ASK </> <fg=cyan>RSA private key file not found! Please add again, or try with absolute path: </> ', null)
            );
        }

        return $path;
    }
}
