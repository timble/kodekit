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
 * @package     Nooku_Components
 * @subpackage  Default
 */
class ComDefaultTemplateHelperDate extends KTemplateHelperDate
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
            'gmt_offset' => self::getOffset()
        ));
        
        // Joomla 1.6+ uses different date formats so DATE_FORMAT_LC1 is no longer usable
        if (version_compare(JVERSION, '1.6', '<')) {
            $config->append(array(
                'format' => $this->translate('DATE_FORMAT_LC1')
            ));
        }

        return parent::format($config);
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
    
    /**
     * Returns the offset as seconds based on the current timezone
     */
    public static function getOffset()
    {
        $offset = version_compare(JVERSION, '3.0', 'ge')
            ? JFactory::getConfig()->get('offset') 
            : JFactory::getConfig()->getValue('config.offset');
        $seconds = 0;
        
        if (version_compare(JVERSION, '1.6', '<')) {
            $seconds = $offset * 3600;
        } else {
            $timezone = new DateTimeZone($offset);
            $seconds  = $timezone->getOffset(new DateTime);
        }
        
        return $seconds;
    }
}
