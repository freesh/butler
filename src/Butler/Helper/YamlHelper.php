<?php
namespace Butler\Helper;

use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Yaml\Yaml;

class YamlHelper extends Yaml implements HelperInterface
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
        return 'yaml';
    }

    /**
     * ToDo: move to a global utility helper?
     * Merges two arrays recursive and overwrites existing keys
     *
     * @param array $settings1
     * @param array $settings2
     * @return array
     */
    public static function arrayMergeDistinct(array &$settings1, array &$settings2)
    {
        $merged = $settings1;
        foreach ($settings2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = self::arrayMergeDistinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }
        return $merged;
    }
}
