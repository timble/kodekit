<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright    Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Pageable Dispatcher Behavior
 *
 * Serves a page in a special template
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Library\Dispatcher
 */
class ComKoowaDispatcherBehaviorPageable extends KControllerBehaviorAbstract
{
    /**
     * Automatically set to true if the format query variable is page
     *
     * @var bool
     */
    protected $_enabled = false;

    /**
     * Render the page using HTML format and don't let Joomla touch it
     *
     * @param KDispatcherContextInterface $context
     * @throws RuntimeException
     */
    protected function _beforeDispatch(KDispatcherContextInterface $context)
    {
        if ($context->request->isGet() && $context->request->getFormat() === 'page') {
            $this->_enabled = true;

            $this->_resetDocument('html');

            $context->request->setFormat('html');
        }
    }

    /**
     * Reset the document object in Joomla after rendering it via JDocument
     *
     * @param KDispatcherContextInterface $context
     */
    protected function _afterDispatch(KDispatcherContextInterface $context)
    {
        if ($this->_enabled)
        {
            $params = array(
                'directory' => dirname(dirname(dirname(__FILE__))) . '/views/page',
                'template'  => 'tmpl',
                'file'      => 'index.html.php',
                'params'    => array()
            );

            $document = JFactory::getDocument();
            $document->parse($params);

            $document->setBuffer($context->result, 'component');

            $context->result = $document->render(false, $params);

            // Revert back to JDocumentRaw to complete the request
            $this->_resetDocument('raw');
        }
    }

    /**
     * Overrides the JDocument instance stored in JFactory to the passed format
     *
     * @param $format
     */
    protected function _resetDocument($format)
    {
        JFactory::getApplication()->input->set('format', $format);

        if (version_compare(JVERSION, '3.0', '<')) {
            JRequest::setVar('format', $format);
        }

        JFactory::$document = null;
        JFactory::getDocument();
    }
}