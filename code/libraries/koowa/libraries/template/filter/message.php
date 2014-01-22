<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

/**
 * Messages Template Filter
 *
 * Filter will render the response flash messages.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class KTemplateFilterMessage extends KTemplateFilterAbstract implements KTemplateFilterRenderer
{
    /**
     * The messages
     *
     * @var array
     */
    protected $_messages;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config An optional ObjectConfig object with configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_messages = $config->messages;
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config  An optional ObjectConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'messages' => $this->getObject('response')->getMessages(),
        ));

        parent::_initialize($config);
    }

    public function render(&$text)
    {
        $messages = '';
        foreach ($this->_messages as $type => $message)
        {
            $messages .= '<div class="alert alert-'.strtolower($type).'">';
            foreach ($message as $line) {
                $messages .= '<div class="alert__text">'.$line.'</div>';
            }
            $messages .= '</div>';
        }

        $text = str_replace('<ktml:messages>', $messages, $text);
    }
}