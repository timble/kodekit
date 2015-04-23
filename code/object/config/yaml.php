<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Object Config Yaml
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Object\Config
 */
class KObjectConfigYaml extends KObjectConfigFormat
{
    /**
     * YAML encoder callback
     *
     * @var callable
     */
    protected static $_encoder;

    /**
     * YAML decoder callback
     *
     * @var callable
     */
    protected static $_decoder;

    /**
     * Constructor.
     *
     * @param   array|KObjectConfig An associative array of configuration options or a KObjectConfig instance.
     */
    public function __construct( $options = array() )
    {
        parent::__construct($options);

        if(!self::$_encoder)
        {
            if (function_exists('yaml_emit')) {
                $this->setEncoder('yaml_emit');
            } elseif(class_exists('Symfony\Component\Yaml\Yaml')) {
                $this->setEncoder('Symfony\Component\Yaml\Yaml::dump');
            }
        }

        if(!self::$_decoder)
        {
            if (function_exists('yaml_parse')) {
                $this->setDecoder('yaml_parse');
            } elseif(class_exists('Symfony\Component\Yaml\Yaml')) {
                $this->setDecoder('Symfony\Component\Yaml\Yaml::parse');
            }
        }
    }

    /**
     * Get callback for encoding YAML
     *
     * @return callable
     */
    public static function getEncoder()
    {
        return self::$_encoder;
    }

    /**
     * Set callback for encoding YAML
     *
     * @param  callable $encoder the encoder to set
     * @throws InvalidArgumentException
     * @return void
     */
    public static function setEncoder($encoder)
    {
        if (!is_callable($encoder)) {
            throw new InvalidArgumentException('Invalid parameter to setEncoder(). Must be callable');
        }

        self::$_encoder = $encoder;
    }

    /**
     * Get callback for decoding YAML
     *
     * @return callable
     */
    public static function getDecoder()
    {
        return self::$_decoder;
    }

    /**
     * Set callback for decoding YAML
     *
     * @param  callable $decoder the decoder to set
     * @throws InvalidArgumentException
     * @return void
     */
    public static function setDecoder($decoder)
    {
        if (!is_callable($decoder)) {
            throw new InvalidArgumentException('Invalid parameter to setDecoder(). Must be callable');
        }

        self::$_decoder = $decoder;
    }

    /**
     * Read from a YAML string and create a config object
     *
     * @param  string $string
     * @param  bool    $object  If TRUE return a ConfigObject, if FALSE return an array. Default TRUE.
     * @throws DomainException
     * @throws RuntimeException
     * @return KObjectConfigYaml|array
     */
    public function fromString($string, $object = true)
    {
        $data = array();

        if ($decoder = $this->getDecoder())
        {
            $data = array();

            if(!empty($string))
            {
                $data = call_user_func($decoder, $string);

                if($data === false) {
                    throw new DomainException('Cannot parse YAML string');
                }
            }
        }
        else throw new RuntimeException("No Yaml decoder specified");

        return $object ? $this->merge($data) : $data;
    }

    /**
     * Write a config object to a YAML string.
     *
     * @return string|false     Returns a YAML encoded string on success. False on failure.
     */
    public function toString()
    {
        $result = false;

        if ($encoder = $this->getEncoder())
        {
            $data   = $this->toArray();
            $result = call_user_func($encoder, $data);
        }
        else throw new RuntimeException("No Yaml encoder specified");

        return $result;
    }
}