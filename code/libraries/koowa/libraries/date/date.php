<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Date
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Date
 */
class KDate extends DateTime implements KDateInterface
{
    /**
     * Translator object
     *
     * @var KTranslator
     */
    private $__translator;

    /**
     * Constructor.
     *
     * @param   array|KObjectConfig An associative array of configuration settings or a ObjectConfig instance.
     */
    public function __construct($config = array())
    {
        if (!$config instanceof KObjectConfig) {
            $config = new KObjectConfig($config);
        }

        $this->_initialize($config);

        if (!($config->timezone instanceof DateTimeZone)) {
            $config->timezone = new DateTimeZone($config->timezone);
        }

        $this->__translator = $config->translator;

        parent::__construct($config->date, $config->timezone);
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'date'       => 'now',
            'timezone'   => 'UTC',
            'translator' => 'koowa:translator'
        ));
    }

    /**
     * Returns the date formatted according to given format.
     *
     * @param  string $format The format to use
     * @return string
     */
    public function format($format)
    {
        $format = preg_replace_callback('/(?<!\\\)[DlFM]/', array($this, '_translate'), $format);
        return parent::format($format);
    }

    /**
     * Returns human readable date.
     *
     * @param  string $period The smallest period to use. Default is 'second'.
     * @return string Formatted date.
     */
    public function humanize($period = 'second')
    {
        $translator = $this->getTranslator();

        $periods = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year');
        $lengths = array(60, 60, 24, 7, 4.35, 12, 10);
        $now     = new DateTime();

        if($now != $this)
        {
            // TODO: Use DateTime::getTimeStamp().
            if($now > $this)
            {
                $difference = $now->format('U') - $this->format('U');
                $tense      = 'ago';
            }
            else
            {
                $difference = $this->format('U') - $now->format('U');
                $tense      = 'from now';
            }

            for($i = 0; $difference >= $lengths[$i] && $i < 6; $i++) {
                $difference /= $lengths[$i];
            }

            $difference      = round($difference);
            $period_index    = array_search($period, $periods);
            $omitted_periods = $periods;
            array_splice($omitted_periods, $period_index);

            if(in_array($periods[$i], $omitted_periods))
            {
                $difference = 1;
                $i          = $period_index;
            }

            if($periods[$i] == 'day' && $difference == 1)
            {
                // Since we got 1 by rounding it down and if it's less than 24 hours it would say x hours ago, this
                // is yesterday
                return $tense == 'ago' ? $translator->translate('Yesterday') : $translator->translate('Tomorrow');
            }

            $period        = $periods[$i];
            $period_plural = $period.'s';

            // We do not pass $period or $tense as parameters to replace because some languages use different words
            // for them based on the time difference.
            $result = $translator->choose(
                array("{number} $period $tense", "{number} $period_plural $tense"),
                $difference,
                array('number' => $difference)
            );
        }
        else $result = $translator->translate('Just now');

        return $result;
    }

    /**
     * Gets the translator object
     *
     * @return  KTranslatorInterface
     */
    public function getTranslator()
    {
        if(!$this->__translator instanceof KTranslatorInterface)
        {
            $this->__translator = KObjectManager::getInstance()->getObject($this->__translator);

            if(!$this->__translator instanceof KTranslatorInterface)
            {
                throw new UnexpectedValueException(
                    'Translator: '.get_class($this->__translator).' does not implement KTranslatorInterface'
                );
            }
        }

        return $this->__translator;
    }

    /**
     * Sets the translator object
     *
     * @param  KTranslatorInterface $translator A translator object
     * @return ComKoowaDate
     */
    public function setTranslator(KTranslatorInterface $translator)
    {
        $this->__translator = $translator;
        return $this;
    }

    /**
     * Get a handle for this object
     *
     * This function returns an unique identifier for the object. This id can be used as a hash key for storing objects
     * or for identifying an object
     *
     * @return string A string that is unique
     */
    public function getHandle()
    {
        return spl_object_hash($this);
    }

    /**
     * Translates day and month names.
     *
     * @param array $matches Matched elements of preg_replace_callback.
     * @return string
     */
    protected function _translate($matches)
    {
        $replacement = '';
        $translator  = $this->getTranslator();

        switch ($matches[0])
        {
            case 'D':
                $replacement = $translator->translate(strtoupper(parent::format('D')));
                break;

            case 'l':
                $replacement = $translator->translate(strtoupper(parent::format('l')));
                break;

            case 'F':
                $replacement = $translator->translate(strtoupper(parent::format('F')));
                break;

            case 'M':
                $replacement = $translator->translate(strtoupper(parent::format('F').'_SHORT'));
                break;
        }

        $replacement = preg_replace('/^([0-9])/', '\\\\\\\\\1', $replacement);
        $replacement = preg_replace('/([a-z])/i', '\\\\\1', $replacement);

        return $replacement;
    }
}