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

    /**
     * Change the owner of an array of files or directories.
     *
     * @param string|iterable $files     A filename, an array of files, or a \Traversable instance to change owner
     * @param string          $user      The new owner user name
     * @param bool            $recursive Whether change the owner recursively or not
     *
     * @throws IOException When the change fail
     */
    public function chown($files, $user, $recursive = false)
    {
        $this->toIterable($files);
        array_walk($files,[$this, 'getPathWrapper']);
        parent::chown($files, $user, $recursive);
    }

    /**
     * Copies a file.
     *
     * If the target file is older than the origin file, it's always overwritten.
     * If the target file is newer, it is overwritten only when the
     * $overwriteNewerFiles option is set to true.
     *
     * @param string $originFile          The original filename
     * @param string $targetFile          The target filename
     * @param bool   $overwriteNewerFiles If true, target files newer than origin files are overwritten
     *
     * @throws FileNotFoundException When originFile doesn't exist
     * @throws IOException           When copy fails
     */
    public function copy($originFile, $targetFile, $overwriteNewerFiles = false)
    {
        parent::copy($this->getPath($originFile), $this->getPath($targetFile), $overwriteNewerFiles);
    }

    /**
     * Atomically dumps content into a file.
     *
     * @param string $filename The file to be written to
     * @param string $content  The data to write into the file
     *
     * @throws IOException if the file cannot be written to
     */
    public function dumpFile($filename, $content)
    {
        parent::dumpFile($this->getPath($filename), $content);
    }

    /**
     * Checks the existence of files or directories.
     *
     * @param string|iterable $files A filename, an array of files, or a \Traversable instance to check
     *
     * @return bool true if the file exists, false otherwise
     */
    public function exists($files)
    {
        $this->toIterable($files);
        array_walk($files,[$this, 'getPathWrapper']);
        return parent::exists($files);
    }

    /**
     * Creates a hard link, or several hard links to a file.
     *
     * @param string          $originFile  The original file
     * @param string|iterable $targetFiles The target file(s)
     *
     * @throws FileNotFoundException When original file is missing or not a file
     * @throws IOException           When link fails, including if link already exists
     */
    public function hardlink($originFile, $targetFiles)
    {
        $this->toIterable($targetFiles);
        array_walk($targetFiles,[$this, 'getPathWrapper']);
        parent::hardlink($this->getPath($originFile), $targetFiles);
    }

    /**
     * Returns whether the file path is an absolute path.
     *
     * @param string $file A file path
     *
     * @return bool
     */
    public function isAbsolutePath($file)
    {
        return parent::isAbsolutePath($file);
    }

    /**
     * Given an existing path, convert it to a path relative to a given starting path.
     *
     * @param string $endPath   Absolute path of target
     * @param string $startPath Absolute path where traversal begins
     *
     * @return string Path of target relative to starting path
     */
    public function makePathRelative($endPath, $startPath)
    {
        return parent::makePathRelative($this->getPath($endPath), $this->getPath($startPath));
    }

    /**
     * Mirrors a directory to another.
     *
     * @param string       $originDir The origin directory
     * @param string       $targetDir The target directory
     * @param \Traversable $iterator  A Traversable instance
     * @param array        $options   An array of boolean options
     *                                Valid options are:
     *                                - $options['override'] Whether to override an existing file on copy or not (see copy())
     *                                - $options['copy_on_windows'] Whether to copy files instead of links on Windows (see symlink())
     *                                - $options['delete'] Whether to delete files that are not in the source directory (defaults to false)
     *
     * @throws IOException When file type is unknown
     */
    public function mirror($originDir, $targetDir, \Traversable $iterator = null, $options = array())
    {
        parent::mirror($this->makePathAbsolute($originDir), $this->makePathAbsolute($targetDir), $iterator, $options);
    }

    /**
     * Moves a directory or file to another location.
     *
     * @param string       $origin The origin file or directory
     * @param string       $target The target file or directory
     * @param \Traversable $iterator  A Traversable instance
     * @param array        $options   An array of boolean options
     *                                Valid options are:
     *                                - $options['override'] Whether to override an existing file on copy or not (see copy())
     *                                - $options['copy_on_windows'] Whether to copy files instead of links on Windows (see symlink())
     *                                - $options['delete'] Whether to delete files that are not in the source directory (defaults to false)
     *
     * @throws IOException When origin type is unknown or does not exist
     */
    public function move($origin, $target, \Traversable $iterator = null, $options = array())
    {
        if (is_dir($this->getPath($origin))) {
            $this->mirror($origin, $target, $iterator, $options);
        } else {
            $this->copy($origin, $target, (isset($options['override']) ? $options['override'] : array()));
        }
        if ($this->exists($target)) {
            $this->remove($origin);
            return true;
        }
        return false;
    }

    /**
     * Creates a directory recursively.
     *
     * @param string|iterable $dirs The directory path
     * @param int             $mode The directory mode
     *
     * @throws IOException On any directory creation failure
     */
    public function mkdir($dirs, $mode = 0777)
    {
        $this->toIterable($dirs);
        parent::mkdir($this->getPath($dirs), $mode);
    }

    /**
     * Resolves links in paths.
     *
     * With $canonicalize = false (default)
     *      - if $path does not exist or is not a link, returns null
     *      - if $path is a link, returns the next direct target of the link without considering the existence of the target
     *
     * With $canonicalize = true
     *      - if $path does not exist, returns null
     *      - if $path exists, returns its absolute fully resolved final version
     *
     * @param string $path         A filesystem path
     * @param bool   $canonicalize Whether or not to return a canonicalized path
     *
     * @return string|null
     */
    public function readlink($path, $canonicalize = false)
    {
        return parent::readlink($this->getPath($path), $canonicalize);
    }


    /**
     * Removes files or directories.
     *
     * @param string|iterable $files A filename, an array of files, or a \Traversable instance to remove
     *
     * @throws IOException When removal fails
     */
    public function remove($files)
    {
        parent::remove($this->getPath($files));
    }


    /**
     * Renames a file or a directory.
     *
     * @param string $origin    The origin filename or directory
     * @param string $target    The new filename or directory
     * @param bool   $overwrite Whether to overwrite the target if it already exists
     *
     * @throws IOException When target file or directory already exists
     * @throws IOException When origin cannot be renamed
     */
    public function rename($origin, $target, $overwrite = false)
    {
        parent::rename($this->getPath($origin), $this->getPath($target), $overwrite);
    }

    /**
     * Creates a symbolic link or copy a directory.
     *
     * @param string $origin     The origin directory path
     * @param string $target     The symbolic link name
     * @param bool   $copyOnWindows Whether to copy files if on Windows
     *
     * @throws IOException When symlink fails
     */
    public function symlink($origin, $target, $copyOnWindows = false)
    {
        parent::symlink($origin, $this->getPath($target), $copyOnWindows);
    }

    /**
     * Creates a temporary file with support for custom stream wrappers.
     *
     * @param string $dir    The directory where the temporary filename will be created
     * @param string $prefix The prefix of the generated temporary filename
     *                       Note: Windows uses only the first three characters of prefix
     *
     * @return string The new temporary filename (with path), or throw an exception on failure
     */
    public function tempnam($dir, $prefix)
    {
        parent::tempnam($dir, $prefix);
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
        if(is_array($path) || $path instanceof \Traversable) {
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
