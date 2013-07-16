<?php
/**
 * @version		$Id$
 * @package		Koowa_Template
 * @subpackage	Helper
 * @copyright	Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * Template Behavior Helper
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @package		Koowa_Template
 * @subpackage	Helper
 */
class KTemplateHelperBootstrap extends KTemplateHelperAbstract
{
    /**
     * Array which holds a list of loaded javascript libraries
     *
     * @type array
     */
    protected static $_loaded = array();

    /**
     * Load Bootstrap JavaScript files
     *
     * @param array|KConfig $config
     * @return string
     */
    public function load($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'debug' => null,
            'javascript'  => false,
        ));
        $html   = '';

        if ($config->javascript && !isset(self::$_loaded['bootstrap-javascript'])) {
            $html .= $this->javascript($config);
        }

        if (!isset(self::$_loaded['bootstrap-css']))
        {
            $file  = 'bootstrap'.($config->debug ? '' : '.min').'.css';
            $html .= '<script src="media://lib_koowa/css/'.$file.'" />';

            self::$_loaded['bootstrap-css'] = true;
        }

        return $html;
    }

    /**
     * Load Bootstrap JavaScript files
     *
     * @param array|KConfig $config
     * @return string
     */
    public function javascript($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'debug' => null
        ));
        $html   = '';

        if (!isset(self::$_loaded['bootstrap-javascript']))
        {
            if (!isset(self::$_loaded['jquery'])) {
                $html .= $this->getTemplate()->getHelper('behavior')->jquery($config);
            }

            $file  = 'bootstrap'.($config->debug ? '' : '.min').'.js';
            $html .= '<script src="media://lib_koowa/js/'.$file.'" />';

            self::$_loaded['bootstrap-javascript'] = true;
        }

        return $html;
    }


    /**
     * Wrap the output of the template with a filter
     *
     * @param array $config
     */
    public function wrapper($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'wrapper' => null
        ));

        if ($config->wrapper)
        {
            $this->getTemplate()->addFilter('wrapper');
            $this->getTemplate()->getFilter('wrapper')->setWrapper($config->wrapper);
        }
    }
}