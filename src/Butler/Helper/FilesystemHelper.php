<?php
namespace Butler\Helper;

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class FilesystemHelper extends Helper
{
    /**
     * @param $filename
     * @return bool|\Exception|IOExceptionInterface
     */
    public function createFile($filename) {
        $fs = new Filesystem();
        try {
            $fs->mkdir('./symfonyTest/'.mt_rand());
        } catch (IOExceptionInterface $e) {
            return $e;
        }
        return true;
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName() {
        return 'filesystem';
    }
}