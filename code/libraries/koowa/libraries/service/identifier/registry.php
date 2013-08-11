<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Service Identifier Registry Class
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_Service
 * @subpackage  Identifier
 */
class KServiceIdentifierRegistry extends ArrayObject
{
 	/**
 	 * Cache
 	 *
 	 * @var boolean
 	 */
    protected $_cache = false;

    /**
 	 * Cache Prefix
 	 *
 	 * @var boolean
 	 */
    protected $_cache_prefix = 'koowa-cache-identifier';

	/**
     * Enable class caching
     *
     * @param  boolean	$enabled Enable or disable the cache. Default is TRUE.
     * @return boolean	TRUE if caching is enabled. FALSE otherwise.
     */
	public function enableCache($enabled = true)
	{
	    if($enabled && extension_loaded('apc')) {
            $this->_cache = true;
        } else {
            $this->_cache = false;
        }

        return $this->_cache;
	}

	/**
     * Set the cache prefix
     *
     * @param string $prefix The cache prefix
     * @return void
     */
	public function setCachePrefix($prefix)
	{
	    $this->_cache_prefix = $prefix;
	}

	/**
     * Get the cache prefix
     *
     * @return string	The cache prefix
     */
	public function getCachePrefix()
	{
	    return $this->_cache_prefix;
	}

 	/**
     * Get an item from the array by offset
     *
     * @param   int     $offset   The offset
     * @return  mixed   The item from the array
     */
    public function offsetGet($offset)
    {
        if(!parent::offsetExists($offset))
        {
            if($this->_cache) {
                $result = unserialize(apc_fetch($this->_cache_prefix.'-'.$offset));
            } else {
                $result = false;
            }
        }
        else $result = parent::offsetGet($offset);

        return $result;
    }

    /**
     * Set an item in the array
     *
     * @param   int     $offset The offset of the item
     * @param   mixed   $value  The item's value
     * @return  object  KObjectArray
     */
    public function offsetSet($offset, $value)
    {
        if($this->_cache) {
            apc_store($this->_cache_prefix.'-'.$offset, serialize($value));
        }

        parent::offsetSet($offset, $value);
    }

	/**
     * Check if the offset exists
     *
     * @param   int    $offset The offset
     * @return  bool
     */
    public function offsetExists($offset)
    {
        if(false === $result = parent::offsetExists($offset))
        {
            if($this->_cache) {
                $result = apc_exists($this->_cache_prefix.'-'.$offset);
            }
        }

        return $result;
    }
}
