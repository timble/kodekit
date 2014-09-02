<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Feeds information from JDocument into the template to be used in page rendering
 *
 * @author  Ercan Özkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Koowa\Template\Filter
 */
class ComKoowaTemplateFilterDocument extends KTemplateFilterAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority' => self::PRIORITY_HIGH
        ));

        parent::_initialize($config);
    }

    public function filter(&$text)
    {
        if($this->getTemplate()->getParameters()->layout == 'koowa')
        {
            $head = JFactory::getDocument()->getHeadData();
            $mime = JFactory::getDocument()->getMimeEncoding();

            ob_start();

            echo '<title>'.$head['title'].'</title>';

            // Generate stylesheet links
            foreach ($head['styleSheets'] as $source => $attributes)
            {
                $attributes['type'] = $attributes['mime'];
                unset($attributes['mime']);

                echo sprintf('<ktml:style src="%s" %s />', $source, $this->buildAttributes($attributes));
            }

            // Generate stylesheet declarations
            foreach ($head['style'] as $type => $content)
            {
                // This is for full XHTML support.
                if ($mime != 'text/html') {
                    $content = "<![CDATA[\n".$content."\n]]>";
                }

                echo sprintf('<style type="%s">%s</style>', $type, $content);
            }

            // Generate script file links
            foreach ($head['scripts'] as $path => $attributes)
            {
                $attributes['type'] = $attributes['mime'];
                unset($attributes['mime']);

                echo sprintf('<ktml:script src="%s" %s />', $path, $this->buildAttributes($attributes));
            }

            // Generate script declarations
            foreach ($head['script'] as $type => $content)
            {
                // This is for full XHTML support.
                if ($mime != 'text/html') {
                    $content = "<![CDATA[\n".$content."\n]]>";
                }

                echo sprintf('<script type="%s">%s</script>', $type, $content);
            }

            foreach ($head['custom'] as $custom) {
                echo $custom."\n";
            }


            // Generate script language declarations.
            if (count(JText::script()))
            {
                echo '<script type="text/javascript">';
                echo '(function() {';
                echo 'var strings = ' . json_encode(JText::script()) . ';';
                echo 'if (typeof Joomla == \'undefined\') {';
                echo 'Joomla = {};';
                echo 'Joomla.JText = strings;';
                echo '}';
                echo 'else {';
                echo 'Joomla.JText.load(strings);';
                echo '}';
                echo '})();';
                echo '</script>';
            }

            $head = ob_get_clean();

            $text = $head.$text;
        }
    }
}