<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
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
