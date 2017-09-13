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
    public function getName() {
        return 'filesystem';
    }
}