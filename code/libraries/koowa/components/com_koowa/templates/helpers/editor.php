<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Editor Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaTemplateHelperEditor extends KTemplateHelperAbstract
{
    /**
     * Generates an HTML editor
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function display($config = array())
    {
        $config = new KConfig($config);
        $config->append(array(
            'editor'    => null,
            'name'      => 'description',
            'width'     => '100%',
            'height'    => '500',
            'cols'      => '75',
            'rows'      => '20',
            'buttons'   => true,
            'options'   => array()
        ));

        $editor  = JFactory::getEditor($config->editor);
        $options = KConfig::unbox($config->options);

        $result = $editor->display($config->name, $config->{$config->name}, $config->width, $config->height, $config->cols, $config->rows, KConfig::unbox($config->buttons), $config->name, null, null, $options);
        
        // Some editors like CKEditor return inline JS. 
        $result = str_replace('<script', '<script data-inline', $result);

        return $result;
    }
}
