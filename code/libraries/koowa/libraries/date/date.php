<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Date Class
 *
 * @package Koowa_Date
 */
class KDate extends DateTime
{
    /**
     * Constructor.
     *
     * @param   array|KConfig An associative array of configuration settings or a ObjectConfig instance.
     */
    public function __construct($config = array())
    {
        if (!$config instanceof KConfig) {
            $config = new KConfig($config);
        }

        $this->_initialize($config);

        if (!($config->timezone instanceof DateTimeZone)) {
            $config->timezone = new DateTimeZone($config->timezone);
        }

        parent::__construct($config->date, $config->timezone);
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KConfig $config Configuration options
     * @return void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'date'       => 'now',
            'timezone'   => 'UTC'
        ));
    }
}