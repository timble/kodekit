<?php
/**
 * @package     Koowa_View
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link     	http://www.nooku.org
 */

/**
 * View JSON Class
 *
 * The JSON view implements supports for JSONP through the models callback
 * state. If a callback is present the output will be padded.
 *
 * @author      Johan Janssens <johan@nooku.org>
 * @package     Koowa_View
 */
class ComKoowaViewJson extends KViewAbstract
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
            'mimetype'	  => 'application/json',
            'padding'	  => '',
            'version'     => '2.0',
            'text_fields' => array('description'), // Links are converted to absolute in these fields
            'item_name'   => KInflector::singularize($this->getName()),
            'list_name'   => KInflector::pluralize($this->getName())
        ));

        parent::_initialize($config);
    }

    /**
     * Return the views output
     *
     * If the view 'output' variable is empty the output will be generated based on the
     * model data, if it set it will be returned instead.
     *
     * If the model contains a callback state, the callback value will be used to apply
     * padding to the JSON output.
     *
     *  @return string 	The output of the view
     */
    public function display()
    {
        if (empty($this->output))
        {
            $this->output = array_merge(array(
                'version' => $this->_version
            ), $this->_getData());

            $this->_processLinks($this->output);
        }

        if (!is_string($this->output)) {
            $this->output = json_encode($this->output);
        }

        //Handle JSONP
        if (!empty($this->_padding)) {
            $this->output = $this->_padding.'('.$this->output.');';
        }

        return parent::display();
    }

    /**
     * Returns the JSON output
     *
     * It converts relative URLs in the output to relative before returning the result
     *
     * @return array
     */
    protected function _getData()
    {
        if (KInflector::isPlural($this->getName())) {
            $result = $this->_renderList($this->getModel()->getList());
        } else {
            $result = $this->_renderItem($this->getModel()->getItem());
        }

        return $result;
    }

    /**
     * Returns the JSON output for lists
     *
     * @param  KDatabaseRowsetInterface $rowset
     * @return array
     */
    protected function _renderList(KDatabaseRowsetInterface $rowset)
    {
        $model   = $this->getModel();
        $key     = $this->_list_name;
        $data    = $this->_getList($rowset);

        $output  = array(
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

        if ($limit && $total-($limit + $offset) > 0) {
            $output['links']['next'] = array(
                'href' => $this->_getListLink($rowset, array('offset' => $limit+$offset)),
                'type' => 'application/json'
            );
        }

        if ($limit && $offset && $offset >= $limit)
        {
            $output['links']['previous'] = array(
                'href' => $this->_getListLink($rowset, array('offset' => max($offset-$limit, 0))),
                'type' => 'application/json'
            );
        }

        return $output;
    }

    /**
     * Get the list link for JSON output
     *
     * @param KDatabaseRowsetInterface  $rowset
     * @param array                     $query Additional query parameters to merge
     * @return string
     */
    protected function _getListLink(KDatabaseRowsetInterface $rowset, array $query = array())
    {
        $url = KRequest::url();

        if ($query) {
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

        foreach ($rowset as $row) {
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
     * Get the item data for JSON output
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
     * Get the item link for JSON output
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
            elseif ($key === 'href') {
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
     *
     * @return string Text with converted links
     *
     * @since   11.1
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

    /**
     * Create a route based on a query string or a row.
     *
     * option, view and layout will automatically be added if they are omitted.
     *
     * In templates, use @route()
     *
     * @param string|KDatabaseRowInterface $route      The query string or a row
     * @param string                       $parameters A query string for extra parameters.
     *
     * @return string The route
     */
    public function createRoute($route = '', $parameters = '')
    {
        $parts = array();

        // Convert parameters to an array
        if (!empty($parameters)) {
            parse_str($parameters, $parts);
        }

        // $parameters elements always take precedence
        parse_str($route, $tmp);
        $parts = array_merge($tmp, $parts);

        if (!isset($parts['option'])) {
            $parts['option'] = 'com_'.$this->getIdentifier()->package;
        }

        if (!isset($parts['view'])) {
            $parts['view'] = $this->getName();
        }

        if (!isset($parts['layout']) && $this->getLayout() !== 'default') {
            $parts['layout'] = $this->getLayout();
        }

        // Add the format information to the URL only if it's not 'html'
        if (!isset($parts['format']) && $this->getIdentifier()->name !== 'html') {
            $parts['format'] = $this->getIdentifier()->name;
        }

        $result = 'index.php?'.http_build_query($parts, '', '&');

        return JRoute::_($result, false);
    }
}
