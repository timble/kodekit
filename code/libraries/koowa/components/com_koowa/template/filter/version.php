<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Url Template Filter
 *
 * Filter allows to create url schemes that are replaced on compile and render.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa\Template\Filter
 */
class ComKoowaTemplateFilterVersion extends KTemplateFilterAbstract
{
    /**
     * A component => version map
     *
     * @var array
     */
    protected static $_versions = array();

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority' => KTemplateFilterInterface::PRIORITY_HIGH
        ));

        parent::_initialize($config);
    }


    /**
     * Returns the version information of a component
     *
     * @param $component
     * @return string|null
     */
    protected function _getVersion($component)
    {
        if (!isset(self::$_versions[$component]))
        {
            try
            {
                if ($component === 'koowa') {
                    $version = Koowa::VERSION;
                }
                else $version = $this->getObject('com://admin/' . $component.'.version')->getVersion();
            }
            catch (Exception $e) {
                $version = null;
            }

            self::$_versions[$component] = $version;
        }

        return self::$_versions[$component];
    }

    /**
     * Adds version suffixes to stylesheets and scripts
     *
     * {@inheritdoc}
     */
    public function filter(&$text)
    {
        $pattern = '~
            <ktml:(?:script|style) # match ktml:script and ktml:style tags
            [^(?:src=)]+           # anything before src=
            src="                  # match the link
              ((?:media://|assets://)           # starts with media:// or assets://
              (?:koowa/)?            # may or may not be in koowa/ folder
              (?:com_([^/]+)/|js/|css/|scss/) # either has package name (com_foo) or in framework
              [^"]+)"                # match the rest of the link
             (.*)/>
        ~siUx';

        if(preg_match_all($pattern, $text, $matches, PREG_SET_ORDER))
        {
            foreach ($matches as $match)
            {
                $version = $this->_getVersion(!empty($match[2]) ? $match[2] : 'koowa');

                if ($version)
                {
                    $url     = $match[1];
                    $version = substr(md5($version), 0, 8);
                    $suffix  = strpos($url, '?') === false ? '?'.$version : '&'.$version;

                    $text    = str_replace($url, $url.$suffix, $text);
                }
            }
        }
    }
}