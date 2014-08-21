<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Yaml Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterYaml extends KFilterAbstract
{
    /**
     * Validate a value
     *
     * @param    mixed  $value Value to be validated
     * @return   bool   True when the variable is valid
     */
    public function validate($value)
    {
        try {
            $config = $this->getObject('object.config.factory')->fromString('yaml', $value);
        } catch(RuntimeException $e) {
            $config = null;
        }

        return is_string($value) && !is_null($config);
    }

    /**
     * Sanitize a value
     *
     * @param   mixed  $value Value to be sanitized
     * @return  KObjectConfig
     */
    public function sanitize($value)
    {
        if(!$value instanceof KObjectConfig)
        {
            if(is_string($value)) {
                $value = $this->getObject('object.config.factory')->fromString('yaml', $value);
            } else {
                $value = $this->getObject('object.config.factory')->createFormat('yaml', $value);
            }
        }

        return $value;
    }
}