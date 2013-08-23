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
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Koowa
 */
class ComKoowaDate extends KDate
{
    /**
     * Constructor.
     *
     * @param   array|KObjectConfig $config An associative array of configuration settings or a KObjectConfig instance.
     */
    public function __construct($config = array())
    {
        if (!$config instanceof KObjectConfig) {
            $config = new KObjectConfig($config);
        }

        $this->_initialize($config);

        $this->setTranslator($config->translator);

        parent::__construct($config);
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'translator' => 'com:koowa.translator'
        ));

        parent::_initialize($config);
    }

    /**
     * Returns the date formatted according to given format.
     *
     * @param  string $format The format to use
     * @return string
     */
    public function format($format)
    {
        $format = preg_replace_callback('/(?<!\\\)[DlFM]/', array($this, '_translate'), $format);

        return parent::format($format);
    }

    /**
     * Translates day and month names.
     *
     * @param array $matches Matched elements of preg_replace_callback.
     * @return string
     */
    protected function _translate($matches)
    {
        $replacement = '';
        $translator  = $this->getTranslator();

        switch ($matches[0])
        {
            case 'D':
                $replacement = $translator->translate(strtoupper(parent::format('D')));
                break;

            case 'l':
                $replacement = $translator->translate(strtoupper(parent::format('l')));
                break;

            case 'F':
                $replacement = $translator->translate(strtoupper(parent::format('F')));
                break;

            case 'M':
                $replacement = $translator->translate(strtoupper(parent::format('F').'_SHORT'));
                break;
        }

        $replacement = preg_replace('/^([0-9])/', '\\\\\\\\\1', $replacement);
        $replacement = preg_replace('/([a-z])/i', '\\\\\1', $replacement);

        return $replacement;
    }

    /**
     * Gets the translator object
     *
     * @return  KTranslator
     */
    public function getTranslator()
    {
        return $this->_translator;
    }

    /**
     * Sets the translator object
     *
     * @param string|KTranslator $translator A translator object or identifier
     * @return $this
     *
     * @throws UnexpectedValueException
     */
    public function setTranslator($translator)
    {
        if (!$translator instanceof KTranslator)
        {
            $translator = KObjectManager::getInstance()->getObject($translator);

            if (!$translator instanceof KTranslator) {
                throw new UnexpectedValueException('Passed identifier is not a translator');
            }
        }

        $this->_translator = $translator;

        return $this;
    }
}