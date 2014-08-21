<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Csv View
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\View
 */
class KViewCsv extends KViewAbstract
{
    /**
     * Character used for quoting
     *
     * @var string
     */
    public $quote = '"';

    /**
     * Character used for separating fields
     *
     * @var string
     */
    public $separator = ',';

    /**
     * End of line
     *
     * @var string
     */
    public $eol = "\n";

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
            'version'     => '1.0',
            'disposition' => 'inline',
            'quote'       => '"',
            'separator'   => ',',
            'eol'         => "\n"
        ))->append(array(
            'mimetype'    => 'text/csv; version='.$config->version,
        ));

        parent::_initialize($config);
    }

    /**
     * Return the views output
     *
     * @param KViewContext  $context A view context object
     * @return string   The output of the view
     */
    protected function _actionRender(KViewContext $context)
    {
        $rows    = '';
        $columns = array();
        $entities  = $this->getModel()->fetch();

        // Get the columns
        foreach($entities as $entity)
        {
            $data    = $entity->toArray();
            $columns = array_merge($columns + array_flip(array_keys($data)));
        }

        // Empty the column values
        foreach($columns as $key => $value) {
            $columns[$key] = '';
        }

        //Create the rows
        foreach($entities as $entity)
        {
            $data = $entity->toArray();
            $data = array_merge($columns, $data);

            $rows .= $this->_arrayToString(array_values($data)).$this->eol;
        }

        // Create the header
        $header = $this->_arrayToString(array_keys($columns)).$this->eol;

        // Set the content
        $this->setContent($header.$rows);

        return parent::_actionRender($context);
    }

    /**
     * Render
     *
     * @param   array   $data Value
     * @return  boolean
     */
    protected function _arrayToString($data)
    {
        $fields = array();
        foreach($data as $value)
        {
            //Implode array's
            if(is_array($value)) {
                $value = implode(',', $value);
            }

             // Escape the quote character within the field (e.g. " becomes "")
            if ($this->_quoteValue($value))
            {
                $quoted_value = str_replace($this->quote, $this->quote.$this->quote, $value);
                $fields[] 	  = $this->quote . $quoted_value . $this->quote;
            }
            else $fields[] = $value;
        }

        return  implode($this->separator, $fields);
    }

    /**
     * Check if the value should be quoted
     *
     * @param   string  $value Value
     * @return  boolean
     */
    protected function _quoteValue($value)
    {
        if(is_numeric($value)) {
            return false;
        }

        if(strpos($value, $this->separator) !== false) { // Separator is present in field
            return true;
        }

        if(strpos($value, $this->quote) !== false) { // Quote character is present in field
            return true;
        }

        if (strpos($value, "\n") !== false || strpos($value, "\r") !== false ) { // Newline is present in field
            return true;
        }

        if(substr($value, 0, 1) == " " || substr($value, -1) == " ") {  // Space found at beginning or end of field value
            return true;
        }

        return false;
    }
}
