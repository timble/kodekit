<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Directory Object Bootstrapper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Bootstrapper
 */
class ComKoowaObjectBootstrapperDirectory extends KObjectBootstrapperDirectory
{
    /**
     * Get the components from a directory
     *
     * @param array $directory
     * @return array
     */
    public function getComponents(array $directory)
    {
        $components = array();

        foreach($directory as $path)
        {
            foreach (new \DirectoryIterator($path) as $dir)
            {
                //Only get the component directory names
                if ($dir->isDot() || !$dir->isDir() || !preg_match('/^[a-zA-Z]+/', $dir->getBasename())) {
                    continue;
                }

                if(file_exists($dir->getPathname().'/bootstrapper.php')) {
                    $components[] = substr($dir, 4);
                }
            }
        }

        return array_unique($components);
    }
}
