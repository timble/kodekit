<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Date Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Helper
 */
class TemplateHelperDate extends TemplateHelperAbstract
{
    /**
     * Returns formatted date according to current local and adds time offset.
     *
     * @param  array  $config An optional array with configuration options.
     * @return string Formatted date.
     */
    public function format($config = array())
    {
        $translator = $this->getObject('translator');

        $config = new ObjectConfig($config);
        $config->append(array(
            'date'     => 'now',
            'timezone' => date_default_timezone_get(),
            'format'   => $translator->translate('DATE_FORMAT_LC1'),
            'default'  => ''
        ));

        $return = $config->default;

        if(!in_array($config->date, array('0000-00-00 00:00:00', '0000-00-00')))
        {
            try
            {
                $date = $this->getObject('lib:date', array('date' => $config->date, 'timezone' => 'UTC'));
                $date->setTimezone(new \DateTimeZone($config->timezone));

                $return = $date->format($config->format);
            }
            catch(Exception $e) {}
        }

        return $return;
    }

    /**
     * Returns human readable date.
     *
     * @param  array $config An optional array with configuration options.
     * @return string  Formatted date.
     */
    public function humanize($config = array())
    {
        $config = new ObjectConfig($config);
        $config->append(array(
            'date'      => 'now',
            'timezone'  => date_default_timezone_get(),
            'default'   => $this->getObject('translator')->translate('Never'),
            'period'    => 'second',

        ));

        $result = $config->default;

        if(!in_array($config->date, array('0000-00-00 00:00:00', '0000-00-00')))
        {
            try
            {
                $date = $this->getObject('date', array('date' => $config->date, 'timezone' => 'UTC'));
                $date->setTimezone(new \DateTimeZone($config->timezone));

                $result = $date->humanize($config->period);
            }
            catch(Exception $e) {}
        }
        return $result;
    }
}
