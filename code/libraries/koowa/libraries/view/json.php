<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Json View
 *
 * The JSON view implements supports for JSONP through the model's callback state. If a callback is present the content
 * will be padded.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\View
 */
class KViewJson extends KViewAbstract
{
    /**
     * The padding for JSONP
     *
     * @var string
     */
    protected $_padding;

    /**
     * JSON API version
     */
    protected $_version;

    /**
     * Constructor
     *
     * @param   KConfig $config Configuration options
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        //Padding can explicitly be turned off by setting to FALSE
        if(empty($config->padding) && $config->padding !== false)
        {
            $state = $this->getModel()->getState();

            if(isset($state->callback) && (strlen($state->callback) > 0)) {
                $config->padding = $state->callback;
            }
        }

        $this->_padding = $config->padding;
        $this->_version = $config->version;

        $this->_item_name = $config->item_name;
        $this->_list_name = $config->list_name;

        $this->_text_fields = KConfig::unbox($config->text_fields);
    }

    /**
     * Initializes the config for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'padding'	  => '',
            'version'     => '1.0',
            'text_fields' => array('description'), // Links are converted to absolute in these fields
            'item_name'   => KStringInflector::singularize($this->getName()),
            'list_name'   => KStringInflector::pluralize($this->getName())
        ))->append(array(
            'mimetype' => 'application/json; version=' . $config->version,
        ));

        parent::_initialize($config);
    }

    /**
     * Return the views content
     *
     * If the view 'content'  is empty the content will be generated based on the model data, if it set it will
     * be returned instead.
     *
     * If the model contains a callback state, the callback value will be used to apply padding to the JSON output.
     *
     *  @return string A RFC4627-compliant JSON string, which may also be embedded into HTML.
     */
    public function display()
    {
        if (empty($this->_content))
        {
            $this->_content = array_merge(array('version' => $this->_version), $this->_getData());
            $this->_processLinks($this->_content);
        }

        //Serialise
        if (!is_string($this->_content))
        {
            // Root should be JSON object, not array
            if (is_array($this->_content) && 0 === count($this->_content)) {
                $this->_content = new \ArrayObject();
            }

            // Encode <, >, ', &, and " for RFC4627-compliant JSON, which may also be embedded into HTML.
            $this->_content = json_encode($this->_content, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        }

        //Handle JSONP
        if (!empty($this->_padding)) {
            $this->_content = $this->_padding.'('.$this->_content.');';
        }

        return parent::display();
    }

    /**
     * Force the route to fully qualified and not escaped by default
     *
     * @param   string  $route   The query string used to create the route
     * @param   boolean $fqr     If TRUE create a fully qualified route. Default TRUE.
     * @param   boolean $escape  If TRUE escapes the route for xml compliance. Default FALSE.
     * @return  string  The route
     */
    public function createRoute($route = '', $fqr = true, $escape = false)
    {
        return parent::createRoute($route, $fqr, $escape);
    }

    /**
     * Returns the JSON data
     *
     * It converts relative URLs in the content to relative before returning the result
     *
     * @return array
     */
    protected function _getData()
    {
        if (KStringInflector::isPlural($this->getName())) {
            $result = $this->_renderList($this->getModel()->getList());
        } else {
            $result = $this->_renderItem($this->getModel()->getItem());
        }

        return $result;
    }

    /**
     * Returns the JSON data for a list
     *
     * @param  KDatabaseRowsetInterface $rowset
     * @return array
     */
    protected function _renderList(KDatabaseRowsetInterface $rowset)
    {
        $model   = $this->getModel();
        $key     = $this->_list_name;
        $data    = $this->_getList($rowset);

        $json  = array(
            'links' => array(
                'self' => array(
                    'href' => $this->_getListLink($rowset),
                    'type' => 'application/json'
                )
            ),
            $key => array(
                'offset'   => (int) $model->offset,
                'limit'    => (int) $model->limit,
                'total'	   => $model->getTotal(),
                'data'     => $data
            )
        );

        $model  = $this->getModel();
        $total  = $model->getTotal();
        $limit  = (int) $model->limit;
        $offset = (int) $model->offset;

        if ($limit && $total-($limit + $offset) > 0)
        {
            $json['links']['next'] = array(
                'href' => $this->_getListLink($rowset, array('offset' => $limit+$offset)),
                'type' => 'application/json'
            );
        }

        if ($limit && $offset && $offset >= $limit)
        {
            $json['links']['previous'] = array(
                'href' => $this->_getListLink($rowset, array('offset' => max($offset-$limit, 0))),
                'type' => 'application/json'
            );
        }

        return $json;
    }

    /**
     * Get the list link
     *
     * @param KDatabaseRowsetInterface  $rowset
     * @param array                     $query Additional query parameters to merge
     * @return string
     */
    protected function _getListLink(KDatabaseRowsetInterface $rowset, array $query = array())
    {
        $url = KRequest::url();

        if ($query)
        {
            $previous = $url->getQuery(true);
            $url->setQuery(array_merge($previous, $query));
        }

        return (string) $url;
    }

    /**
     * Returns the JSON representation of a rowset
     *
     * @param  KDatabaseRowsetInterface $rowset
     * @return array
     */
    protected function _getList(KDatabaseRowsetInterface $rowset)
    {
        $result = array();
        $tmpl   = array(
            'links' => array(
                'self' => array(
                    'href' => null,
                    'type' => 'application/json'
                )
            ),
            'data'  => array()
        );

        foreach ($rowset as $row)
        {
            $clone = $tmpl;
            $clone['links']['self']['href'] = $this->_getItemLink($row);
            $clone['data'] = $this->_getItem($row);

            $result[] = $clone;
        }

        return $result;
    }

    /**
     * Returns an array representing an item
     *
     * @param KDatabaseRowInterface  $row
     *
     * @return array
     */
    protected function _renderItem(KDatabaseRowInterface $row)
    {
        $result = array(
            'links' => array(
                'self' => array(
                    'href' => $this->_getItemLink($row),
                    'type' => 'application/json'
                )
            ),
            'data' => $this->_getItem($row)
        );

        return $result;
    }

    /**
     * Get the item data
     *
     * @param KDatabaseRowInterface  $row   Document row
     * @return array The array with data to be encoded to json
     */
    protected function _getItem(KDatabaseRowInterface $row)
    {
        $method = '_get'.ucfirst($row->getIdentifier()->name);

        if ($method !== '_getItem' && method_exists($this, $method)) {
            return $this->$method($row);
        }

        return $row->toArray();
    }

    /**
     * Get the item link
     *
     * @param KDatabaseRowInterface  $row
     * @return string
     */
    protected function _getItemLink(KDatabaseRowInterface $row)
    {
        $package = $this->getIdentifier()->package;
        $view    = $row->getIdentifier()->name;

        return $this->createRoute(sprintf('option=com_%s&view=%s&slug=%s&format=json', $package, $view, $row->slug));
    }

    /**
     * Converts links in an array from relative to absolute
     *
     * @param array $array Source array
     */
    protected function _processLinks(array &$array)
    {
        $base = KRequest::url()->toString(KHttpUrl::AUTHORITY);

        foreach ($array as $key => &$value)
        {
            if (is_array($value)) {
                $this->_processLinks($value);
            }
            elseif ($key === 'href')
            {
                if (substr($value, 0, 4) !== 'http') {
                    $array[$key] = trim($base, '/').$value;
                }
            }
            elseif (in_array($key, $this->_text_fields)) {
                $array[$key] = $this->_processText($value);
            }
        }
    }

    /**
     * Convert links in a text from relative to absolute and runs them through JRoute
     *
     * @param string $text The text processed
     * @return string Text with converted links
     */
    protected function _processText($text)
    {
        $base    = trim(JURI::base(), '/');
        $matches = array();

        preg_match_all("/(href|src)=\"(?!http|ftp|https|mailto|data)([^\"]*)\"/", $text, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $text = str_replace($match[0], $match[1].'="'.$base.JRoute::_($match[2]).'"', $text);
        }

        return $text;
    }
}
