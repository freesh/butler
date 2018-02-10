<?php
namespace Butler\Helper;

use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Filesystem\Filesystem;

class FilesystemHelper extends Filesystem implements HelperInterface
{
    protected $helperSet = null;

    /**
     * Sets the helper set associated with this helper.
     *
     * @param HelperSet $helperSet A HelperSet instance
     */
    public function setHelperSet(HelperSet $helperSet = null)
    {
        $this->helperSet = $helperSet;
    }

    /**
     * Gets the helper set associated with this helper.
     *
     * @return HelperSet|null
     */
    public function getHelperSet()
    {
        return $this->helperSet;
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'filesystem';
    }

    public function appendToFile($filename, $content)
    {
        parent::appendToFile($this->getPath($filename), $content);
    }

    /**
     * Change the group of an array of files or directories.
     *
     * @param string|iterable $files     A filename, an array of files, or a \Traversable instance to change group
     * @param string          $group     The group name
     * @param bool            $recursive Whether change the group recursively or not
     *
     * @throws IOException When the change fail
     */
    public function chgrp($files, $group, $recursive = false)
    {
        $this->toIterable($files);
        array_walk($files,[$this, 'getPathWrapper']);
        parent::chgrp($files, $group, $recursive);
    }

    /**
     * Change mode for an array of files or directories.
     *
     * @param string|iterable $files     A filename, an array of files, or a \Traversable instance to change mode
     * @param int             $mode      The new mode (octal)
     * @param int             $umask     The mode mask (octal)
     * @param bool            $recursive Whether change the mod recursively or not
     *
     * @throws IOException When the change fail
     */
    public function chmod($files, $mode, $umask = 0000, $recursive = false)
    {
        $this->toIterable($files);
        array_walk($files,[$this, 'getPathWrapper']);
        parent::chmod($files, $mode, $umask, $recursive);
    }





    /*
     * Wrapper function of $this->getPath for usage in array_map()
     *
     * @param $path string
     */
    private function getPathWrapper(&$path)
    {
        $path = $this->getPath($path);
    }

    /**
     * Checks if cli is executed from phar archive.
     * If yes and $path is not absolute, the 'command working directory' will be used and extended by $path and
     * the absolute path will be returned.
     * We need absolute path for file operations out of the .phar archive
     *
     * @param $path string|array
     * @return string|array
     */
    public function getPath($path)
    {
        if(($path = $this->isNotRootDir($path)) === false) {
            return null;
        }
        if(is_array($path)) {
            array_walk($path, function(&$val){
                $val = $this->convertUserPath($val);
                if(!$this->isAbsolutePath($val) && !empty($pharPath = \Phar::running(false))) {
                    $val = $this->makePathAbsolute($val);
                }
            });
        } else {
            $path = $this->convertUserPath($path);
            if (!$this->isAbsolutePath($path) && !empty($pharPath = \Phar::running(false))) {
                $path = $this->makePathAbsolute( $path);
            }
        }
        return $path;
    }

    /**
     * converts a path to absolute path
     * ToDo: create a task
     *
     * @param $path
     * @return string
     */
    public function makePathAbsolute($path)
    {
        return getcwd().'/'.ltrim ( $path, "./" );
    }

    /**
     * Replace ~ in $path with the absolute user path
     *
     * @param $path
     * @return mixed
     */
    public function convertUserPath($path)
    {
        if (function_exists('posix_getuid') && strpos($path, '~') !== false) {
            $userInfo = posix_getpwuid(posix_getuid());
            return str_replace('~', $userInfo['dir'], $path);
        }
        return $path;
    }

    /**
     * This checks if the given path is the root path. Opeartions on "/" should not allowed.
     * Iterates over strings and arrays. If some item with a root path is found, it will be removed.
     *
     * @param $path string|array
     * @return string|array|bool
     */
    private function isNotRootDir($path)
    {
        if(is_array($path)) {
            if (($key = array_search("/", $path)) !== false) {
                $this->output->writeln('<error><options=bold;bg=red>  ERR </></error> <fg=red>"filesystem" Operations on "' . $path[$key] . '" are not allowed!</>');
                unset($path[$key]);
            }
        } else {
            if ($path == "/") {
                $this->output->writeln('<error><options=bold;bg=red>  ERR </></error> <fg=red>"filesystem" Operations on "' . $path . '" are not allowed!</>');
                return false;
            }
        }
        return $path;
    }

    /**
     * @param mixed $files
     *
     * @return array|\Traversable
     */
    private function toIterable(&$files)
    {
        $files = is_array($files) || $files instanceof \Traversable ? $files : array($files);
        return $files;
    }
}
