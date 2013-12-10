<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Message Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Template
 */
class ComKoowaTemplateHelperMessage extends KTemplateHelperAbstract
{
    /**
     * Get the locked information
     *
     * @param  array|KObjectConfig $config An optional configuration array.
     * @throws UnexpectedValueException
     * @return string The locked by "name" "date" message
     */
    public function lock($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'row' => null
        ));

        if (!($config->row instanceof KDatabaseRowInterface)) {
            throw new UnexpectedValueException('$config->row should be a KDatabaseRowInterface instance');
        }

        $row = $config->row;
        $message = '';

        if($row->isLockable() && $row->locked())
        {
            $user = JFactory::getUser($row->locked_by);
            $date = $this->getObject('com:koowa.template.helper.date')->humanize(array('date' => $row->locked_on));

            $message = $this->getObject('translator')->translate(
                'Locked by {name} {date}', array('name' => $user->get('name'), 'date' => $date)
            );
        }

        return $message;
    }
}
