<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Json View
 *
 * Adheres to the JSON API standard v1.0
 *
 * @see     http://jsonapi.org/
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Kodekit\Library\View
 */
class ViewJson extends ViewAbstract
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
     * @param   ObjectConfig $config Configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        $this->_version = $config->version;

        $this->_text_fields = ObjectConfig::unbox($config->text_fields);
        $this->_fields      = ObjectConfig::unbox($config->fields);

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
     * @param   ObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'behaviors'   => array('localizable', 'routable'),
            'mimetype'    => 'application/vnd.api+json',
            'version'     => '1.0',
            'fields'      => array(),
            'text_fields' => array('description'), // Links are converted to absolute ones in these fields
        ));

        parent::_initialize($config);
    }

    /**
     * Render and return the views output
     *
     * If the view 'content'  is empty the output will be generated based on the model data, if it set it will
     * be returned instead.
     *
     * @param ViewContext  $context A view context object
     * @throws \DomainException Object could not be encoded to valid JSON.
     * @return string A RFC4627-compliant JSON string, which may also be embedded into HTML.
     */
    protected function _actionRender(ViewContext $context)
    {
        //Get the content
        $content = $context->content;

        if (!is_string($content))
        {
            // Root should be JSON object, not array
            if (is_array($content) && count($content) === 0) {
                $content = new \ArrayObject();
            }

            // Encode <, >, ', &, and " for RFC4627-compliant JSON, which may also be embedded into HTML.
            $content = json_encode($content, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

            if (json_last_error() > 0) {
                throw new \DomainException(sprintf('Cannot encode data to JSON string - %s', json_last_error_msg()));
            }
        }

        return $content;
    }

    /**
     * Returns the JSON data
     *
     * It converts relative URLs in the content to relative before returning the result
     *
     * @return array
     */
    protected function _fetchData(ViewContext $context)
    {
        $output = new \ArrayObject(array(
            'jsonapi' => array('version' => $this->_version),
            'links'   => array('self' => $this->getUrl()->toString()),
            'data'    => array()
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

        $context->content = $output;
    }

    /**
     * Creates a resource object specified by JSON API
     *
     * @see   http://jsonapi.org/format/#document-resource-objects
     *
     * @param ModelEntityInterface  $entity   Document row
     * @param array $config Resource configuration.
     * @return array The array with data to be encoded to json
     */
    protected function _createResource(ModelEntityInterface $entity, array $config = array())
    {
        $config = array_merge(array(
            'links'         => true,
            'relationships' => true
        ), $config);

        $data = array(
            'type' => $this->_callCustomMethod($entity, 'type') ?: StringInflector::pluralize($entity->getIdentifier()->name),
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
     * @param ModelEntityInterface $entity
     * @return array
     */
    protected function _includeResource(ModelEntityInterface $entity)
    {
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
     * @param ModelEntityInterface $entities
     * @return array
     */
    protected function _includeCollection(ModelEntityInterface $entities)
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
     * @param ModelEntityInterface $entity
     * @param string $method
     * @return mixed Method results or false if the method not exists
     */
    protected function _callCustomMethod(ModelEntityInterface $entity, $method)
    {
        $result = false;
        $name   = StringInflector::singularize($entity->getIdentifier()->name);
        $method = '_get'.ucfirst($name).ucfirst($method);

        if ($method !== '_getEntity'.ucfirst($method) && method_exists($this, $method)) {
            $result = $this->$method($entity);
        }

        return $result;
    }

    /**
     * Provides a default entity link
     *
     * It can be overridden by creating a _getFooLinks method where foo is the entity type
     *
     * @param ModelEntityInterface  $entity
     * @return string
     */
    protected function _getEntityRoute(ModelEntityInterface $entity)
    {
        $package = $this->getIdentifier()->package;
        $view    = $entity->getIdentifier()->name;

        return $this->getRoute(sprintf('component=%s&view=%s&slug=%s&format=json', $package, $view, $entity->slug));
    }

    /**
     * Converts links in the content from relative to absolute
     *
     * @param ViewContextInterface $context
     */
    protected function _convertRelativeLinks(ViewContextInterface $context)
    {
        if (is_array($context->content) || $context->content instanceof \Traversable) {
            $this->_processLinks($context->content);
        }
    }

    /**
     * Converts links in the content array from relative to absolute
     *
     * @param \Traversable|array $array
     */
    protected function _processLinks(&$array)
    {
        $base = $this->getUrl()->toString(HttpUrl::AUTHORITY);

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

