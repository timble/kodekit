<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Automatically converts row fields between KObjectConfig and serialized strings to easily save objects
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Library\Database
 */
class KDatabaseBehaviorSerializable extends KDatabaseBehaviorAbstract
{
    /**
     * Fields to serialize/unserialize
     *
     * @var array
     */
    protected $_fields = array();

    /**
     * Format to serialize into
     *
     * @var string
     */
    protected $_format;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_fields = KObjectConfig::unbox($config->fields);
        $this->_format = $config->format;
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'fields' => array(),
            'format' => 'json',
        ));

        parent::_initialize($config);
    }

    /**
     * Convert row fields to serialized values
     *
     * @return KDatabaseRowInterface
     */
    public function serializeFields()
    {
        $this->_serialize($this->getMixer());
        return $this->getMixer();
    }

    /**
     * Convert row fields to unserialized values
     *
     * @return KDatabaseRowInterface
     */
    public function unserializeFields()
    {
        $this->_unserialize($this->getMixer());
        return $this->getMixer();
    }

    /**
     * Convert a field back into a JSON encoded string
     *
     * @param KDatabaseRowInterface $row
     */
    protected function _serialize(KDatabaseRowInterface $row)
    {
        foreach ($this->_fields as $field)
        {
            $value = $row->$field;
            if (!is_string($value))
            {
                if ($value instanceof KObjectConfigInterface) {
                    $value = $value->toString();
                }
                elseif (is_object($value) || is_array($value))
                {
                    $config = $this->getObject('koowa:object.config.factory')->getFormat($this->_format);
                    foreach ($value as $key => $val) {
                        $config->set($key, $val);
                    }
                    $value = $config->toString();
                }

                // Set the data without changing the modified column information
                if ($value) {
                    $row->setData(array($field => $value), false);
                }
            }
        }
    }

    /**
     * Convert a JSON encoded string back into its value
     *
     * @param KDatabaseRowInterface $row
     */
    protected function _unserialize(KDatabaseRowInterface $row)
    {
        $format = $this->getObject('koowa:object.config.factory')->getFormat($this->_format);

        foreach ($this->_fields as $field)
        {
            try
            {
                // Set the data without changing the modified column information
                if (is_string($row->$field)) {
                    $row->setData(array($field => $format->fromString($row->$field)), false);
                }
            }
            catch (RuntimeException $e) {}
        }
    }

    protected function _afterSelect(KDatabaseContextInterface $context)
    {
        $rowset = $context->data;

        if (is_object($rowset))
        {
            if ($rowset instanceof KDatabaseRowInterface) {
                $rowset = array($rowset);
            }

            foreach ($rowset as $row)
            {
                if (is_object($row)) {
                    $this->_unserialize($row);
                }
            }
        }
    }

    protected function _beforeUpdate(KDatabaseContextInterface $context)
    {
        $this->_serialize($context->data);
    }

    protected function _beforeInsert(KDatabaseContextInterface $context)
    {
        $this->_serialize($context->data);
    }
}
