<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Json View
 *
 * Adheres to the JSON API standard v1.0
 *
 * @see     http://jsonapi.org/
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Library\View
 */
class KViewJson extends KViewAbstract
{
    /**
     * JSON API version
     *
     * @var string
     */
    protected $_version;

    /**
     * A cache of resource objects to be sent in included property in top level
     *
     * @var array
     */
    protected $_included_resources = array();

    /**
     * A type=>fields map to return in the response. Blank for all.
     *
     * Comes from the fields property in the query string. For example:
     * fields[documents]=foo,bar&fields[categories]=foo,bar,baz would only show foo and bar properties
     * for documents type and foo, bar, and baz for categories type
     *
     * @var array
     */
    protected $_fields = array();

    /**
     * A list of text fields in the row
     *
     * URLs will be converted to fully qualified ones in these fields.
     *
     * @var string
     */
    protected $_text_fields;

    /**
     * Constructor
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_version = $config->version;

        $this->_text_fields = KObjectConfig::unbox($config->text_fields);
        $this->_fields      = KObjectConfig::unbox($config->fields);

        $query = $this->getUrl()->getQuery(true);
        if (!empty($query['fields']) && is_array($query['fields']))
        {
            foreach ($query['fields'] as $type => $list)
            {
                if (!isset($this->_fields[$type])) {
                    $this->_fields[$type] = array();
                }

                $this->_fields[$type] = explode(',', rawurldecode($list));
            }
        }

        $this->addCommandCallback('before.render', '_convertRelativeLinks');
    }

    /**
     * Initializes the config for the object
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
            'fields'      => array(),
            'text_fields' => array('description'), // Links are converted to absolute ones in these fields
        ))->append(array(
            'mimetype' => 'application/vnd.api+json',
        ));

        parent::_initialize($config);
    }

    /**
     * Render and return the views output
     *
     * If the view 'content'  is empty the output will be generated based on the model data, if it set it will
     * be returned instead.
     *
     * @param KViewContext  $context A view context object
     * @return string A RFC4627-compliant JSON string, which may also be embedded into HTML.
     */
    protected function _actionRender(KViewContext $context)
    {
        //Serialise
        if (!is_string($this->_content))
        {
            // Root should be JSON object, not array
            if (is_array($this->_content) && count($this->_content) === 0) {
                $this->_content = new ArrayObject();
            }

            // Encode <, >, ', &, and " for RFC4627-compliant JSON, which may also be embedded into HTML.
            $this->_content = json_encode($this->_content, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        }


        return parent::_actionRender($context);
    }

    /**
     * Force the route to fully qualified and not escaped by default
     *
     * @param   string|array    $route   The query string used to create the route
     * @param   boolean         $fqr     If TRUE create a fully qualified route. Default TRUE.
     * @param   boolean         $escape  If TRUE escapes the route for xml compliance. Default FALSE.
     * @return  KDispatcherRouterRoute The route
     */
    public function getRoute($route = '', $fqr = true, $escape = false)
    {
        return parent::getRoute($route, $fqr, $escape);
    }

    /**
     * Returns the JSON data
     *
     * It converts relative URLs in the content to relative before returning the result
     *
     * @return array
     */
    protected function _fetchData(KViewContext $context)
    {
        $output = new ArrayObject(array(
            'jsonapi' => array(
                'version' => $this->_version,
            ),
            'links' => array(
                'self' => $this->getUrl()->toString()
            ),
            'data' => array()
        ));
        $model  = $this->getModel();
        $url    = $this->getUrl();

        if ($this->isCollection())
        {
            foreach ($model->fetch() as $entity) {
                $output['data'][] = $this->_createResource($entity);
            }

            $total  = $model->count();
            $limit  = (int) $model->getState()->limit;
            $offset = (int) $model->getState()->offset;

            $output['meta'] = array(
                'offset'   => $offset,
                'limit'    => $limit,
                'total'	   => $total
            );

            if ($limit && $total-($limit + $offset) > 0) {
                $output['links']['next'] = $url->setQuery(array('offset' => $limit+$offset), true)->toString();
            }

            if ($limit && $offset && $offset >= $limit) {
                $output['links']['prev'] = $url->setQuery(array('offset' => max($offset-$limit, 0)), true)->toString();
            }
        }
        else $output['data'] = $this->_createResource($model->fetch());

        if ($this->_included_resources) {
            $output['included'] = array_values($this->_included_resources);
        }

        $this->setContent($output);
    }

    /**
     * Creates a resource object specified by JSON API
     *
     * @see   http://jsonapi.org/format/#document-resource-objects
     * @param KModelEntityInterface  $entity   Document row
     * @param array $config Resource configuration.
     * @return array The array with data to be encoded to json
     */
    protected function _createResource(KModelEntityInterface $entity, array $config = array())
    {
        $config = array_merge(array(
            'links'         => true,
            'relationships' => true
        ), $config);

        $entity = method_exists($entity, 'top') ? $entity->top() : $entity;
        $data   = array(
            'type' => $this->_callCustomMethod($entity, 'type') ?: KStringInflector::pluralize($entity->getIdentifier()->name),
            'id'   => $this->_callCustomMethod($entity, 'id') ?: $entity->{$entity->getIdentityKey()},
            'attributes' => $this->_callCustomMethod($entity, 'attributes') ?: $entity->toArray()
        );

        if (isset($this->_fields[$data['type']]))
        {
            $fields = array_flip($this->_fields[$data['type']]);
            $data['attributes'] = array_intersect_key($data['attributes'], $fields);
        }

        if ($config['links'])
        {
            $links = $this->_callCustomMethod($entity, 'links') ?: array('self' => (string)$this->_getEntityRoute($entity));
            if ($links) {
                $data['links'] = $links;
            }
        }

        if ($config['relationships'])
        {
            $relationships = $this->_callCustomMethod($entity, 'relationships');
            if ($relationships) {
                $data['relationships'] = $relationships;
            }
        }

        return $data;
    }

    /**
     * Creates a resource object and returns a resource identifier object specified by JSON API
     *
     * @see   http://jsonapi.org/format/#document-resource-identifier-objects
     * @param KModelEntityInterface $entity
     * @return array
     */
    protected function _includeResource(KModelEntityInterface $entity)
    {
        $entity = method_exists($entity, 'top') ? $entity->top() : $entity;
        $cache  = $entity->getIdentifier()->name.'-'.$entity->getHandle();

        if (!isset($this->_included_resources[$cache])) {
            $this->_included_resources[$cache] = $this->_createResource($entity, array('relationships' => false));
        }

        $resource = $this->_included_resources[$cache];

        return array(
            'data' => array(
                'type' => $resource['type'],
                'id'   => $resource['id']
            )
        );
    }


    /**
     * Creates resource objects and returns an array of resource identifier objects specified by JSON API
     *
     * @see   http://jsonapi.org/format/#document-resource-identifier-objects
     * @param KModelEntityInterface $entities
     * @return array
     */
    protected function _includeCollection(KModelEntityInterface $entities)
    {
        $result = array('data' => array());

        foreach ($entities as $entity)
        {
            $relation = $this->_includeResource($entity);
            $result['data'][] = $relation['data'];
        }

        return $result;
    }

    /**
     * Calls a custom method per entity name to modify resource objects
     *
     * If the entity is of type foo and the method is links, this method will return the results of
     * _getFooLinks method if possible
     *
     * @param KModelEntityInterface $entity
     * @param string $method
     * @return mixed Method results or false if the method not exists
     */
    protected function _callCustomMethod(KModelEntityInterface $entity, $method)
    {
        $name   = KStringInflector::singularize($entity->getIdentifier()->name);
        $method = '_get'.ucfirst($name).ucfirst($method);

        if ($method !== '_getEntity'.ucfirst($method) && method_exists($this, $method)) {
            return $this->$method($entity);
        }
        else return false;
    }

    /**
     * Provides a default entity link
     *
     * It can be overridden by creating a _getFooLinks method where foo is the entity type
     *
     * @param KModelEntityInterface  $entity
     * @return string
     */
    protected function _getEntityRoute(KModelEntityInterface $entity)
    {
        $package = $this->getIdentifier()->package;
        $view    = $entity->getIdentifier()->name;

        return $this->getRoute(sprintf('component=%s&view=%s&slug=%s&format=json', $package, $view, $entity->slug));
    }

    /**
     * Converts links in the content from relative to absolute
     *
     * @param KViewContextInterface $context
     */
    protected function _convertRelativeLinks(KViewContextInterface $context)
    {
        if (is_array($this->_content) || $this->_content instanceof Traversable) {
            $this->_processLinks($this->_content);
        }
    }

    /**
     * Converts links in the content array from relative to absolute
     *
     * @param Traversable|array $array
     */
    protected function _processLinks(&$array)
    {
        $base = $this->getUrl()->toString(KHttpUrl::AUTHORITY);

        foreach ($array as $key => &$value)
        {
            if ($key === 'links')
            {
                foreach ($array['links'] as $k => $v)
                {
                    if (strpos($v, ':/') === false) {
                        $array['links'][$k] = $base.$v;
                    }
                }
            }
            elseif (is_array($value)) {
                $this->_processLinks($value);
            }
            elseif (in_array($key, $this->_text_fields)) {
                $array[$key] = $this->_processText($value);
            }
        }
    }

    /**
     * Convert links in a text from relative to absolute and runs them through router
     *
     * @param string $text The text processed
     * @return string Text with converted links
     */
    protected function _processText($text)
    {
        $matches = array();

        preg_match_all("/(href|src)=\"(?!http|ftp|https|mailto|data)([^\"]*)\"/", $text, $matches, PREG_SET_ORDER);

        foreach ($matches as $match)
        {
            $route = $this->getObject('lib:dispatcher.router.route', array(
                'url'    => $match[2],
                'escape' => false
            ));

            //Add the host and the schema
            $route->scheme = $this->getUrl()->scheme;
            $route->host   = $this->getUrl()->host;

            $text = str_replace($match[0], $match[1].'="'.$route.'"', $text);
        }

        return $text;
    }
}

