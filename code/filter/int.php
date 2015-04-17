<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Integer Filter
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 */
class KFilterInt extends KFilterAbstract implements KFilterTraversable
{
    /**
     * The maximum value
     *
     * @var integer
     */
    public $max;

    /**
     * The minimum value
     *
     * @var integer
     */
    public $min;

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config An optional ObjectConfig object with configuration options
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'max' => PHP_INT_MAX,
            'min' => ~PHP_INT_MAX,
        ));

        parent::_initialize($config);
    }

    /**
     * Validate a value
     *
     * @param   mixed   $value Value to be validated
     * @return  bool    True when the variable is valid
     */
    public function validate($value)
    {
        $options = array('options' => array(
            'max_range' => $this->max,
            'min_range' => $this->min
        ));

        return empty($value) || (false !== filter_var($value, FILTER_VALIDATE_INT, $options));
    }

    /**
     * Sanitize a value
     *
     * @param   mixed   $value Value to be sanitized
     * @return  int
     */
    public function sanitize($value)
    {
        $value = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);

        if(isset($this->max) && $value > (int) $this->max) {
            $value = (int) $this->max;
        }

        if(isset($this->min) && $value < (int) $this->min) {
            $value = (int) $this->min;
        }

        return $value;
    }

    /**
     * Set the minimum value
     *
     * @param integer   $min
     * @return KFilterInt
     */
    public function min($min)
    {
        $this->min = (int) $min;
        return $this;
    }

    /**
     * Set the maximum value
     *
     * @param integer   $max
     * @return KFilterInt
     */
    public function max($max)
    {
        $this->max = (int) $max;
        return $this;
    }
}

