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
        $config = new KConfig($config);
        $config->append(array(
            'date'     => 'now',
            'timezone'   => true,
            'format'     => $this->translate('DATE_FORMAT_LC1')
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
        $config = new KConfig($config);
        $config->append(array(
            'date'              => null,
            'gmt_offset'        => 0,
            'smallest_period'   => 'second'
        ));

        $periods    = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year');
        $lengths    = array(60, 60, 24, 7, 4.35, 12, 10);
        $now        = strtotime(gmdate("M d Y H:i:s"));
        $time       = is_numeric($config->date) ? $config->date : strtotime($config->date);

        if($time)
        {
            if($config->gmt_offset != 0) {
                $now =  $now + $config->gmt_offset;
            }

            if($now != $time)
            {
                if($now > $time)
                {
                    $difference = $now - $time;
                    $tense      = 'ago';
                }
                else
                {
                    $difference = $time - $now;
                    $tense      = 'from now';
                }

                for($i = 0; $difference >= $lengths[$i] && $i < 6; $i++) {
                    $difference /= $lengths[$i];
                }

                $difference         = round($difference);
                $period_index       = array_search($config->smallest_period, $periods);
                $omitted_periods    = $periods;
                array_splice($omitted_periods, $period_index);

                if(in_array($periods[$i], $omitted_periods))
                {
                    $difference = 1;
                    $i          = $period_index;
                }

                if($periods[$i] == 'day' && $difference == 1)
                {
                    // Since we got 1 by rounding it down and if it's less than 24 hours it would say x hours ago, this is yesterday
                    return $tense == 'ago' ? $this->translate('Yesterday') : $this->translate('Tomorrow');
                }

                $period = $periods[$i];
                $period_plural = $period.'s';

                // We do not pass $period or $tense as parameters to replace because
                // some languages use different words for them based on the time difference.
                $translator = $this->getTemplate()->getHelper('translator')->getTranslator();
                $result = $translator->choose(array("{number} $period $tense", "{number} $period_plural $tense"), $difference, array(
                    'number' => $difference,
                ));
            }
            else $result = $this->translate('Just now');
        }
        else $result = $this->translate('Never');

        return $result;
    }
}