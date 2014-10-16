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
class ComKoowaTemplateFilterAsset extends KTemplateFilterAsset
{
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
        $path = rtrim($this->getObject('request')->getSiteUrl()->getPath(), '/');

        $config->append(array(
            'schemes' => array(
                'media://' => $path.'/media/',
                'root://'  => $path.'/',
                'base://'  => rtrim($this->getObject('request')->getBaseUrl()->getPath(), '/').'/',
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Returns the version information of a component
     *
     * @param $component
     * @return bool|string
     */
    protected function _getVersion($component)
    {
        if ($component === 'koowa') {
            return Koowa::VERSION;
        }

        try {
            return $this->getObject('com://admin/' . $component.'.version')->getVersion();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Adds version suffixes to stylesheets and scripts
     *
     * {@inheritdoc}
     */
    public function filter(&$text)
    {
        $pattern = '#<ktml:(?:script|style)(?!\s+data\-inline\s*)\s+src="([^"]+)"(.*)/>#siU';

        if(preg_match_all($pattern, $text, $matches, PREG_SET_ORDER))
        {
            foreach ($matches as $match)
            {
                $url = $match[1];

                if (strpos($url, 'media://') === 0 && preg_match('#media://(?:koowa/)?com_(.*?)/#i', $url, $folder))
                {
                    $version = $this->_getVersion($folder[1]);

                    if ($version)
                    {
                        $version = substr(md5($version), 0, 8);
                        $suffix  = strpos($url, '?') === false ? '?'.$version : '&'.$version;

                        $text    = str_replace($url, $url.$suffix, $text);
                    }
                }
            }
        }

        parent::filter($text);
    }
}