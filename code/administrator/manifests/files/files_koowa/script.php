<?php
/**
 * @package     Koowa
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class files_koowaInstallerScript
{
    public function uninstall($installer)
    {
        $xml = $installer->manifest;

        // Joomla does not delete non-empty folders so we need to clear them ourselves
        foreach ($xml->fileset->files->folder as $folder)
        {
            $target = (string) $folder->attributes()->target;

            if (!$target) {
                continue;
            }

            $target = JPATH_ROOT.'/'.$target;

            if (JFolder::exists($target)) {
                JFolder::delete($target);
            }
        }

        // Unset the files element since we handled the delete ourselves
        unset($installer->manifest->fileset->files);
    }
}
