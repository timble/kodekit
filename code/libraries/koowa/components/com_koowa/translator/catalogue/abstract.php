<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Translator Catalogue
 *
 * Joomla uses some common keys like JALL, JYES. This catalogue will map them to plain words to allow quick lookups and
 * prevent them for needing to be re-translated for different components.
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Koowa
 */
abstract class ComKoowaTranslatorCatalogueAbstract extends KTranslatorCatalogueAbstract implements ComKoowaTranslatorCatalogueInterface
{
    /**
     * A prefix attached to every generated key
     *
     * @var string
     */
    protected $_prefix;

    /**
     * A list of generated keys
     *
     * @var array
     */
    protected $_keys;


    /**
     * @param KObjectConfig $config
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->setPrefix($config->prefix);
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config An optional KObjectConfig object with configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'prefix'  => 'KLS_',
            'data'    =>  array(
                'all'           => 'JALL',
                'title'         => 'JGLOBAL_TITLE',
                'alias'         => 'JFIELD_ALIAS_LABEL',
                'status'        => 'JSTATUS',
                'category'      => 'JCATEGORY',
                'access'        => 'JGRID_HEADING_ACCESS',
                'date'          => 'JDATE',
                'details'       => 'JDETAILS',
                'description'   => 'JGLOBAL_DESCRIPTION',
                'apply'         => 'JAPPLY',
                'cancel'        => 'JCANCEL',
                'published'     => 'JPUBLISHED',
                'unpublished'   => 'JUNPUBLISHED',
                'options'       => 'JOPTIONS',
                'yes'           => 'JYES',
                'no'            => 'JNO',
                'enabled'       => 'JENABLED',
                'disabled'      => 'JDISABLED',
                'prev'          => 'JPREV',
                'next'          => 'JNEXT',

                'click to sort by this column'  => 'JGLOBAL_CLICK_TO_SORT_THIS_COLUMN',
                'about the calendar'            => 'JLIB_HTML_BEHAVIOR_ABOUT_THE_CALENDAR',
                'go today'                      => 'JLIB_HTML_BEHAVIOR_GO_TODAY',
                'select date'                   => 'JLIB_HTML_BEHAVIOR_SELECT_DATE',
                'drag to move'                  => 'JLIB_HTML_BEHAVIOR_DRAG_TO_MOVE',
                'display %s first'              => 'JLIB_HTML_BEHAVIOR_DISPLAY_S_FIRST',
                'close'                         => 'JLIB_HTML_BEHAVIOR_CLOSE',
                'today'                         => 'JLIB_HTML_BEHAVIOR_TODAY',
                'wk'                            => 'JLIB_HTML_BEHAVIOR_WK',
                'time:'                         => 'JLIB_HTML_BEHAVIOR_TIME',
                'prev. year (hold for menu)'    => 'JLIB_HTML_BEHAVIOR_PREV_YEAR_HOLD_FOR_MENU',
                'prev. month (hold for menu)'   => 'JLIB_HTML_BEHAVIOR_PREV_MONTH_HOLD_FOR_MENU',
                'next month (hold for menu)'    => 'JLIB_HTML_BEHAVIOR_NEXT_MONTH_HOLD_FOR_MENU',
                'next year (hold for menu)'     => 'JLIB_HTML_BEHAVIOR_NEXT_YEAR_HOLD_FOR_MENU',
                '%a, %b %e'                     => 'JLIB_HTML_BEHAVIOR_TT_DATE_FORMAT',
                'start'                         => 'JLIB_HTML_START',
                'end'                           => 'JLIB_HTML_END',
                '(shift-)click or drag to change value' => 'JLIB_HTML_BEHAVIOR_SHIFT_CLICK_OR_DRAG_TO_CHANGE_VALUE',
            )
        ));

        parent::_initialize($config);
    }

    /**
     * Get a string from the catalogue
     *
     * @param string $string
     * @return string
     */
    public function get($string)
    {
        if (!parent::has(strtolower($string)))
        {
            if(!JFactory::getLanguage()->hasKey($string))
            {
                if (substr($string, 0, strlen($this->getPrefix())) === $this->getPrefix()) {
                    $key = $string;
                } else {
                    //Gets a key from the catalogue and prefixes it
                    $key = $this->getPrefix().$this->generateKey($string);
                }

                $translation =  JFactory::getLanguage()->_($key);
            }
            else $translation = JFactory::getLanguage()->_($string);
        }
        else  $translation = JFactory::getLanguage()->_(parent::get(strtolower($string)));

        //Set the translation to prevent it from being re-translated
        $this->set($string, $translation);

        return $translation;
    }

    /**
     * Check if a string exists in the catalogue
     *
     * @param  string $string
     * @return boolean
     */
    public function has($string)
    {
        if (!parent::has(strtolower($string)))
        {
            if(!JFactory::getLanguage()->hasKey($string))
            {
                if (substr($string, 0, strlen($this->getPrefix())) === $this->getPrefix()) {
                    $key = $string;
                } else {
                    //Gets a key from the catalogue and prefixes it
                    $key = $this->getPrefix().$this->generateKey($string);
                }

                $result = JFactory::getLanguage()->hasKey($key);
            }
            else $result = true;
        }
        else $result = true;

        return $result;
    }

    /**
     * Generates a translation key that is safe for INI format
     *
     * @param  string $string
     * @param  int    $limit    Max key length, should be larger then 0. If -1 no limit will be used.
     * @return string
     */
    public function generateKey($string, $limit = 40)
    {
        $key = strtolower($string);

        if(!isset($this->_keys[$string]))
        {
            if ($limit == -1 || strlen($key) <= $limit)
            {
                $key = strip_tags($key);
                $key = preg_replace('#\s+#m', ' ', $key);
                $key = preg_replace('#\{([A-Za-z0-9_\-\.]+)\}#', '$1', $key);
                $key = preg_replace('#(%[^%|^\s|^\b]+)#', 'X', $key);
                $key = preg_replace('#&.*?;#', '', $key);
                $key = preg_replace('#[\s-]+#', '_', $key);
                $key = preg_replace('#[^A-Za-z0-9_]#', '', $key);
                $key = preg_replace('#_+#', '_', $key);
                $key = trim($key, '_');
                $key = trim(strtoupper($key));
            }
            else
            {
                $key = $this->generateKey(substr($key, 0, $limit));
                $key .= '_'.strtoupper(substr(md5($key), 0, 5));
            }
        }
        else $key = $this->_keys[$string];

        return $key;
    }

    /**
     * Return the language key prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }

    /**
     * Set the language key prefix
     *
     * @param string $prefix
     * @return ComKoowaTranslatorCatalogueAbstract
     */
    public function setPrefix($prefix)
    {
        $this->_prefix = $prefix;
        return $this;
    }
}
