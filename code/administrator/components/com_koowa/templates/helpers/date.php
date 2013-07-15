<?php
/**
 * @version     $Id$
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Date Helper
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComKoowaTemplateHelperDate extends KTemplateHelperDate
{
    /**
     * Returns formated date according to current local and adds time offset
     *
     * @param   string  A date in ISO 8601 format or a unix time stamp
     * @param   string  format optional format for strftime
     * @returns string  formated date
     * @see     strftime
     */
    public function format($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'gmt_offset' => JFactory::getApplication()->getCfg('offset')*3600, // Only used in Joomla 1.5
            'timezone'   => true, // Used in 1.6+
            'format'     => 'DATE_FORMAT_LC1'
        ));

        $config->format = $this->translate($config->format);

        // Joomla 1.6+ uses formats for date() while 1.5 uses strftime() format
        if (version_compare(JVERSION, '1.6', '<')) {
            return parent::format($config);
        } else {
            return JHtml::_('date', $config->date, $config->format, $config->timezone);
        }
    }

    /**
     * Returns human readable date.
     *
     * @param  array   An optional array with configuration options.
     * @return string  Formatted date.
     */
    public function humanize($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'gmt_offset' => 0
        ));

        return parent::humanize($config);
    }
}