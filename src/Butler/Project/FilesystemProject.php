<?php

namespace Butler\Project;

class FilesystemProject extends AbstractProject
{

    /**
     * create tasks
     */
    public function createTasks() {

        $this->addTask([
            'key' => 'touch',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'touch',
            'options' => [
                'files' => 'test.txt', // string|array|\Traversable A filename, an array of files, or a \Traversable instance to create
                'time' => null, // (optional) int The touch time as a Unix timestamp
                'atime' => null // (optional) int The access time as a Unix timestamp
            ],
        ]);

        $this->addTask([
            'key' => 'copy',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'copy',
            'options' => [
                'originFile' => 'test.txt', // string
                'targetFile' => 'copy.txt', // string
                'overwriteNewerFiles' => false // (optional) bool (default: false)
            ],
        ]);

        /**
         * ToDo: how to use this task? What to retun?
         */
        $this->addTask([
            'key' => 'exists',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'exists',
            'options' => [
                'files' => 'test.txt', // string|array|\Traversable A filename, an array of files, or a \Traversable instance to check
                'condition' => '', // ToDo: exist oe !exist
                'action' => '' // ToDo: what to do if condition is true?
            ],
        ]);

        $this->addTask([
            'key' => 'mkdir',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'mkdir',
            'options' => [
                'dirs' => 'testDir', // string|array|\Traversable $dirs The directory path
                'mode' => 0755 // (optional) int (default: 0777)
            ],
        ]);

        $this->addTask([
            'key' => 'remove',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'remove',
            'options' => [
                'files' => 'testDir', // string|array|\Traversable A filename, an array of files, or a \Traversable instance to remove
            ],
        ]);

        $this->addTask([
            'key' => 'mkdir lala',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'mkdir',
            'options' => [
                'dirs' => 'lala/ohohoh/huhuhu', // string|array|\Traversable $dirs The directory path
                'mode' => 0755 // (optional) int (default: 0777)
            ],
        ]);

        $this->addTask([
            'key' => 'chmod',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'chmod',
            'options' => [
                'files' => 'lala', // string|array|\Traversable A filename, an array of files, or a \Traversable instance to change mode
                'mode' => 0777, // int The new mode (octal)
                'umask' => 0000, // (optional) int The mode mask (octal) (default: 0000)
                'recursive' => true // (optional) bool Whether change the mod recursively or not (default: false)
            ],
        ]);

        $this->addTask([
            'key' => 'chown',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'chown',
            'options' => [
                'files' => 'lala', // string|array|\Traversable A filename, an array of files, or a \Traversable instance to change owner
                'user' => 'freesh', // string The new owner user name
                'recursive' => true // (optional) bool Whether change the owner recursively or not (default: false)
            ],
        ]);

        /**
         * Change the group of an array of files or directories.
         *
         * @param string|array|\Traversable $files     A filename, an array of files, or a \Traversable instance to change group
         * @param string                    $group     The group name
         * @param bool                      $recursive Whether change the group recursively or not
         *
         * @throws IOException When the change fail
         */
        $this->addTask([
            'key' => 'chgrp',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'chgrp',
            'options' => [
                'files' => 'lala', // string|array|\Traversable A filename, an array of files, or a \Traversable instance to change group
                'group' => 'staff', // string The group name
                'recursive' => true // (optional) bool Whether change the group recursively or not (default: false)
            ],
        ]);

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
        $this->addTask([
            'key' => 'rename',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'rename',
            'options' => [
                'origin' => 'lala', // string The origin filename or directory
                'target' => 'staff', // string The new filename or directory
                'overwrite' => true // (optional) bool Whether to overwrite the target if it already exists (default: false)
            ],
        ]);

        /**
         * Creates a symbolic link or copy a directory.
         *
         * @param string $originDir     The origin directory path
         * @param string $targetDir     The symbolic link name
         * @param bool   $copyOnWindows Whether to copy files if on Windows
         *
         * @throws IOException When symlink fails
         */
        $this->addTask([
            'key' => 'symlink',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'symlink',
            'options' => [
                'originDir' => 'staff', // string The origin directory path
                'targetDir' => 'symlink', // string The symbolic link name
                'copyOnWindows' => true // (optional) bool Whether to copy files if on Windows (default: false)
            ],
        ]);

        /**
         * Creates a hard link, or several hard links to a file.
         *
         * @param string          $originFile  The original file
         * @param string|string[] $targetFiles The target file(s)
         *
         * @throws FileNotFoundException When original file is missing or not a file
         * @throws IOException           When link fails, including if link already exists
         */
        $this->addTask([
            'key' => 'hardlink',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'hardlink',
            'options' => [
                'originFile' => 'test.txt', // string The original file
                'targetFiles' => ['hardlink','hardlink2','hardlink3'], // string|array The target file(s)
            ],
        ]);

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
        $this->addTask([
            'key' => 'readlink',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'readlink',
            'options' => [
                'path' => 'symlink', // string The original file
                'canonicalize' => true, // (optional) bool Whether or not to return a canonicalized path (default: false)
            ],
        ]);

        /**
         * Given an existing path, convert it to a path relative to a given starting path.
         *
         * @param string $endPath   Absolute path of target
         * @param string $startPath Absolute path where traversal begins
         *
         * @return string Path of target relative to starting path
         */
        $this->addTask([
            'key' => 'makePathRelative',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'makePathRelative',
            'options' => [
                'endPath' => '/Users/freesh/PhpstormProjects/butler/', // string Absolute path of target
                'startPath' => '/Users/freesh/PhpstormProjects/butler/testfolder', // string Absolute path where traversal begins
            ],
        ]);

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
        $this->addTask([
            'key' => 'mirror',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'mirror',
            'options' => [
                'originDir' => 'staff', // string The origin directory
                'targetDir' => 'lulu', // string The target directory
                'iterator' => null, // (optional) \Traversable A Traversable instance (default: null)
                'options' => [ // (optional) array An array of boolean options (default: array())
                    'override' => false, // (optional) bool Whether to override an existing file on copy or not (see copy()) (default: false)
                    'copy_on_windows' => true, // (optional) bool Whether to copy files instead of links on Windows (see symlink()) (default: false)
                    'delete' => true // (optional) bool Whether to delete files that are not in the source directory (defaults to false)
                ]
            ],
        ]);

        /**
         * Returns whether the file path is an absolute path.
         *
         * @param string $file A file path
         *
         * @return bool
         */
        $this->addTask([
            'key' => 'isAbsolutePath',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'isAbsolutePath',
            'options' => [
                'file' => 'copy.txt', // string A file path
            ],
        ]);

        /**
         * ToDo: should someone use this? remove this task?
         * Creates a temporary file with support for custom stream wrappers.
         *
         * @param string $dir    The directory where the temporary filename will be created
         * @param string $prefix The prefix of the generated temporary filename
         *                       Note: Windows uses only the first three characters of prefix
         *
         * @return string The new temporary filename (with path), or throw an exception on failure
         */
        $this->addTask([
            'key' => 'tempnam',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'tempnam',
            'options' => [
                'dir' => 'temp', // string The directory where the temporary filename will be created
                'prefix' => 'tmp_' // string The prefix of the generated temporary filename (Note: Windows uses only the first three characters of prefix)
            ],
        ]);

        /**
         * Atomically dumps content into a file.
         *
         * @param string $filename The file to be written to
         * @param string $content  The data to write into the file
         *
         * @throws IOException If the file cannot be written to
         */
        $this->addTask([
            'key' => 'dumpFile',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'dumpFile',
            'options' => [
                'filename' => 'test.txt', // string The file to be written to
                'content' => 'dumped content... :)' // string The data to write into the file
            ],
        ]);

        /**
         * Appends content to an existing file.
         *
         * @param string $filename The file to which to append content
         * @param string $content  The content to append
         *
         * @throws IOException If the file is not writable
         */
        $this->addTask([
            'key' => 'appendToFile',
            'class' => '\\Butler\\Task\\FilesystemTask',
            'task' => 'appendToFile',
            'options' => [
                'filename' => 'test.txt', // string The file to which to append content
                'content' => 'appended content... :-)' // string The content to append
            ],
        ]);
    }
}
