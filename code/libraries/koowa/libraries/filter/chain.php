<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Filter Chain
 *
 * The filter chain overrides the run method to implement a separate validate and santize method
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterChain extends KCommandChain
{
    /**
     * Run the commands in the chain
     *
     * @param string          $name     The filter name
     * @param KCommandContext $context  The data to be filtered
     * @return  mixed
     */
    final public function run( $name, KCommandContext $context )
    {
        $function = '_'.$name;
        $result =  $this->$function($context);
        return $result;
    }

    /**
     * Validate the data
     *
     * @param   KCommandContext $context  Value to be validated
     * @return  bool True when the data is valid
     */
    final protected function _validate( KCommandContext $context )
    {
        foreach($this as $filter)
        {
            if ( $filter->execute( 'validate', $context ) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Sanitize the data
     *
     * @param   KCommandContext $context  Value to be sanitized
     * @return  mixed
     */
    final protected function _sanitize( KCommandContext $context )
    {
        foreach($this as $filter) {
            $context->data = $filter->execute( 'sanitize', $context );
        }

        return $context->data;
    }
}
