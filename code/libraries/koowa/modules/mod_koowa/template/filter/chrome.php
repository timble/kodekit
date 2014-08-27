<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Chrome Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Module\Koowa\Template\Filter
 */
class ModKoowaTemplateFilterChrome extends KTemplateFilterAbstract
{
    /**
     * Constructor.
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct( KObjectConfig $config = null)
    {
        parent::__construct($config);

        include_once JPATH_THEMES . '/system/html/modules.php';

        $template = JFactory::getApplication()->getTemplate();
        if(file_exists(JPATH_THEMES.'/'.$template.'/html/modules.php')) {
            include_once JPATH_THEMES.'/'.$template.'/html/modules.php';
        }
    }

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
            'priority'   => self::PRIORITY_LOW,
            'template'   => JFactory::getApplication()->getTemplate()
        ));

        parent::_initialize($config);
    }

    /**
     * Render the module chrome
     *
     * @param string $text Block of text to parse
     * @return $this
     */
    public function filter(&$text)
    {
        $data = (object) $this->getTemplate()->getData();

        if (isset($data->styles))
        {
            foreach($data->styles as $style)
            {
                $method = 'modChrome_'.$style;

                // Apply chrome and render module
                if (function_exists($method))
                {
                    $data->module->style   = implode(' ', $data->styles);
                    $data->module->content = $text;

                    ob_start();
                    $method($data->module, $data->module->params, $data->attribs);
                    $data->module->content = ob_get_contents();
                    ob_end_clean();
                }

                $text = $data->module->content;
            }
        }

        return $this;
    }
}
