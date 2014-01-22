<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Date Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaTemplateHelperDate extends KTemplateHelperAbstract
{
    /**
     * Returns formatted date according to current local
     *
     * @param  array  $config An optional array with configuration options.
     * @return string Formatted date.
     */
    public function format($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'date'     => 'now',
            'timezone'   => true,
            'format'     => $this->translate('DATE_FORMAT_LC3')
        ));

        return JHtml::_('date', $config->date, $config->format, $config->timezone);
    }

    /**
     * Returns human readable date.
     *
     * @param  array $config An optional array with configuration options.
     * @return string  Formatted date.
     */
    public function humanize($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'date'      => null,
            'timezone'  => date_default_timezone_get(),
            'default'   => $this->translate('Never'),
            'period'    => 'second',

        ));

        $result = $config->default;

        if(!in_array($config->date, array('0000-00-00 00:00:00', '0000-00-00')))
        {
            try
            {
                $date = new ComKoowaDate(array('date' => $config->date, 'timezone' => 'UTC'));
                $date->setTimezone(new DateTimeZone($config->timezone));

                $result = $date->humanize($config->period);
            }
            catch(Exception $e) {}
        }
        return $result;
    }
}
