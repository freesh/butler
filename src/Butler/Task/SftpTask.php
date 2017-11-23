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
     * task options:
     *  'host' => '{ssh-host}', // string
     *  'port' => '22', // int | optional default: 22
     *  'timeout' => '10', // int | optional default: 10
     *  'username' => '{ssh-user}', // string | optional default: input on runtime
     *  'auth_method' => 'rsa', // string | optional ('rsa', 'userpw') default: userpw
     *
     * # if auth_method == userpw
     *  'password' => '{ssh-pwd}', //'' // string | optional default: input on runtime
     *
     * # if auth_method == rsa
     *  'rsa_private_file' => '~/.ssh/id_rsa', // string | optional default: ~/.ssh/id_rsa
     *  'rsa_private_password' => '{rsa_password}', // string | optional default: ~/.ssh/id_rsa
     */
    public function auth(array $config)
    {
        try {
            if ($this->client === null) {

                // connect
                $this->client = new SFTP(
                    $config['options']['host'],
                    (!isset($config['options']['port']) ? '22' : $config['options']['port']),
                    (!isset($config['options']['timeout']) ? null : $config['options']['timeout'])
                );

                // if username is empty
                if (!isset($config['options']['username'])) {
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

                    case 'userpw':
                    default:

                        // if password is empty
                        if (!isset($config['options']['password'])) {
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
            echo 'Sftp Exception: ',  $e->getMessage(), "\n";
        }
    }

    /**
     * @param array $config
     * task options:
     * 'path' => '.' // string | optional default: ./
     */
    public function list(array $config)
    {
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
            echo 'SFTP Exception: ',  $e->getMessage(), "\n";
        }
    }

    /**
     * @param array $config
     * task options:
     * 'dir' => 'dirname' // string or array | required relative or absolute path
     * or
     * 'dir' => [
     *      'dir1',
     *      'dir2',
     *      'dir3'
     * ]
     */
    public function mkdir(array $config)
    {
        try {
            $path = $config['options']['dir'];

            // if dir is string put it in an array
            if (!is_array($path)) {
                $path = array($path);
            }

            // iterate over multible dirs
            foreach ($path as $dir) {
                if (!$this->client->file_exists($dir)) {
                    if (!$this->client->mkdir($dir, -1, true)) {
                        throw new \Exception('Cannot create directory "'.$dir.'"! Please check file permissions');
                    }
                } else {
                    $this->output->writeln('Directory "'.$dir.'" already exist!');
                }
            }
        } catch (Exception $e) {
            echo 'SFTP Exception: ',  $e->getMessage(), "\n";
        }
    }


    /**
     * @param array $config
     * task options:
     * 'target' => 'filename' // string or array | required relative or absolute path
     * or
     * 'target' => [
     *      'dir1',
     *      'file1',
     *      'dir2'
     * ]
     */
    public function delete($config)
    {
        try {
            $path = $config['options']['target'];

            // if target is string put it in an array
            if (!is_array($path)) {
                $path = array($path);
            }

            // iterate over multible targets
            foreach ($path as $target) {
                $this->deleteTarget($target);
            }
        } catch (Exception $e) {
            echo 'SFTP Exception: ',  $e->getMessage(), "\n";
        }
    }


    /**
     * @param array $config
     * task options:
     * 'links' => [ // array | required array with links $key = source $value = target
     *      'link1' => 'target/target1',
     *      'file1' => 'target/file1'
     * ]
     */
    public function symlink($config)
    {
        try {
            // iterate over multible links
            foreach ($config['options']['links'] as $link => $target) {

                // check if file or link does not exist
                if (!$this->client->file_exists($link) && !$this->client->is_link($link)) {
                    // create symlink
                    if (!$this->client->symlink($target, $link)) {
                        throw new \Exception('Cannot create symlink "'.$link.'"! Please check file permissions');
                    }
                } else {
                    // get confirmation to delete existing link, file or folder
                    if ($this->setQuestion(
                        '<options=bold;bg=cyan>  ASK </> <fg=cyan>"'.$link .'" already exist. Will you delete it and create symlink? (y/n): </> ',
                        true,
                        'confirmation'
                    )) {

                        // delete existing link
                        $this->deleteTarget($link);
                        // create symlink
                        if (!$this->client->symlink($target, $link)) {
                            throw new \Exception('Cannot create symlink "'.$link.'"! Please check file permissions');
                        }
                    }
                }
            }
        } catch (Exception $e) {
            echo 'SFTP Exception: ',  $e->getMessage(), "\n";
        }
    }





    /**
     * @param $target
     * @throws \Exception
     */
    private function deleteTarget($target)
    {
        if ($this->client->file_exists($target)) {

            // check if is dir or file
            if (!$this->client->is_link($target) && !$this->isEmptyDir($target)) {

                // get confirmation to delete not empty folder
                if ($this->setQuestion(
                    '<options=bold;bg=cyan>  ASK </> <fg=cyan>"'.$target .'" is a dir and not empty. Delete recursively? (y/n): </> ',
                    true,
                    'confirmation'
                )) {
                    // delete recursive
                    if (!$this->client->delete($target, true)) {
                        throw new \Exception('Cannot delete '.$target.'! Please check file permissions');
                    }
                }
            } else {

                // delete file or dir with just . and .. in it.
                if (!$this->client->delete($target, true)) {
                    throw new \Exception('Cannot delete '.$target.'! Please check file permissions');
                }
            }
        } else {
            //echo 'File "'.$delpath.'" does not exist!';
        }
    }

    /**
     * returns true if $dir ist an empty dir or a file
     * @param $path
     * @return bool
     */
    private function isEmptyDir($dir)
    {

        // if result is an array (dir) or false (file)
        if ($result = $this->client->nlist($dir)) {
            return empty(
            array_diff(
                $result,
                ['.','..']
            )
            );
        }

        // $path looks like a file
        return true;
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
