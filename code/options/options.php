<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Options Singleton
 *
 * @author  Israel Canasa <https://github.com/raeldc>
 * @package Koowa\Library\Options
 */
class KOptions extends KObjectConfig implements KOptionsInterface
{
    /**
     * The options identifier
     *
     * @var string
     */
    private $__identifier;

    /**
     * The provider that created the Options
     *
     * @var KOptionsProviderInterface
     */
    private $__provider;

    /**
     * Constructor.
     *
     * @param  array $options An associative array of configuration options
     * @param  bool $readonly  TRUE to not allow modifications of the config data. Default FALSE.
     */
    public function __construct($options = array(), $readonly = false)
    {
        if(isset($options['identifier'])) {
            $this->__identifier = $options['identifier'];
        }
        else throw new KExceptionError('KOptions must have an identifier');

        if(isset($options['provider']) && $options['provider'] instanceof KOptionsProviderInterface) {
            $this->__provider = $options['provider'];
        }
        else throw new KExceptionError('Options Provider is required when instantiating KOptions');

        if(isset($options['data'])) {
            $data = (array) $options['data'];
        }

        parent::__construct($data, $readonly);
    }

    /**
     * Gets the options identifier.
     *
     * @return	string
     */
    public function getIdentifier()
    {
        return $this->__identifier;
    }

    /**
     * Gets the Provider that instantiated this Options
     *
     * @return	string
     */
    public function getProvider()
    {
        return $this->__provider;
    }

    /**
     * Store the Options using the Provider
     *
     * @return	boolean Whether store was successful
     */
    public function store()
    {
        return $this->getProvider()
                    ->store($this->getIdentifier(), $this);
    }
}
