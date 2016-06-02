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
    protected $_includes = array();

    /**
     * Constructor
     *
     * @param   ObjectConfig $config Configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        $this->_version = $config->version;
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
            'behaviors'  => array('localizable', 'routable'),
            'mimetype'   => 'application/vnd.api+json',
            'version'    => '1.0',
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
        //Get the data
        $data = $this->_createDocument($context->entity);

        // Root should be JSON object, not array
        $data = new \ArrayObject($data);

        // Encode <, >, ', &, and " for RFC4627-compliant JSON, which may also be embedded into HTML.
        $content = json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

        if (json_last_error() > 0) {
            throw new \DomainException(sprintf('Cannot encode data to JSON string - %s', json_last_error_msg()));
        }

        return $content;
    }

    /**
     * Returns the JSON data
     *
     * It converts relative URLs in the content to relative before returning the result
     *
     * @see http://jsonapi.org/format/#document-structure
     *
     * @param ModelEntityInterface  $entity
     * @return array
     */
    protected function _createDocument(ModelEntityInterface $entity)
    {
        $document = array(
            'jsonapi' => array('version' => $this->_version),
            'links'   => array('self' => $this->getUrl()->toString()),
            'data'    => array()
        );

        //Resource(s)
        if ($this->isCollection())
        {
            //Data
            foreach ($entity as $data) {
                $document['data'][] = $this->_createResource($data);
            }

            //Pagination
            $model = $this->getModel();

            if($model->isPaginatable())
            {
                $url       = $this->getUrl();
                $paginator = $model->getPaginator();

                $total  = (int) $paginator->count;
                $limit  = (int) $paginator->limit;
                $offset = (int) $paginator->offset;

                $document['meta'] = array(
                    'offset'   => $offset,
                    'limit'    => $limit,
                    'total'    => $total
                );

                if ($limit && $total - ($limit + $offset) > 0) {
                    $document['links']['next'] = (string) $url->setQuery(array('offset' => $limit + $offset), true)->toString();
                }

                if ($limit && $offset && $offset >= $limit) {
                    $document['links']['prev'] = (string) $url->setQuery(array('offset' => max($offset - $limit, 0)), true)->toString();
                }
            }
        }
        else $document['data'] = $this->_createResource($entity);

        //Include(s)
        if ($this->_includes) {
            $document['included'] = array_values($this->_includes);
        }

        return $document;
    }

    /**
     * Creates a resource object specified by JSON API
     *
     * @link http://jsonapi.org/format/#document-resource-objects
     *
     * @param ModelEntityInterface  $entity
     * @return array
     */
    protected function _createResource(ModelEntityInterface $entity)
    {
        //Data
        $data = array(
            'type'       => $this->_getEntityType($entity),
            'id'         => $this->_getEntityId($entity),
            'attributes' => $this->_getEntityAttributes($entity)
        );

        //Links
        if($links = $this->_getEntityLinks($entity)) {
            $data['links'] = $links;
        }

        //Relationships
        if ( $relationships = $this->_getEntityRelationships($entity)) {
            $data['relationships'] = $relationships;
        }

        return $data;
    }

    /**
     * Creates a resource object and returns a resource identifier object specified by JSON API
     *
     * @link   http://jsonapi.org/format/#document-resource-identifier-objects
     *
     * @param ModelEntityInterface $entity
     * @return array
     */
    protected function _includeResource(ModelEntityInterface $entity)
    {
        $cache = $entity->getIdentifier()->name.'-'.$entity->getHandle();

        if (!isset($this->_includes[$cache])) {
            $this->_includes[$cache] = $this->_createResource($entity);
        }

        $resource = $this->_includes[$cache];

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
     * @link   http://jsonapi.org/format/#document-resource-identifier-objects
     *
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
     * Get the entity id
     *
     * @param ModelEntityInterface  $entity
     * @return int
     */
    protected function _getEntityId(ModelEntityInterface $entity)
    {
        return $entity->{$entity->getIdentityKey()};
    }

    /**
     * Get the entity links
     *
     * @param ModelEntityInterface  $entity
     * @return string
     */
    protected function _getEntityType(ModelEntityInterface $entity)
    {
        return StringInflector::pluralize($entity->getIdentifier()->name);
    }

    /**
     * Get the entity attributes
     *
     * @param ModelEntityInterface  $entity
     * @return array
     */
    protected function _getEntityAttributes(ModelEntityInterface $entity)
    {
        $attributes = $entity->toArray();

        //Remove the identity key from the attributes
        $key = $entity->getIdentityKey();
        if(isset($attributes[$key])) {
            unset($attributes[$key]);
        }

        return $attributes;
    }

    /**
     * Get the entity links
     *
     * @param ModelEntityInterface  $entity
     * @return array
     */
    protected function _getEntityLinks(ModelEntityInterface $entity)
    {
        $package = $this->getIdentifier()->package;
        $view    = $entity->getIdentifier()->name;

        $self = $this->getRoute(sprintf('component=%s&view=%s&id=%s&format=json', $package, $view, $entity->id));

        return array('self' => (string) $self);
    }

    /**
     * Get the entity relationships
     *
     * @param ModelEntityInterface  $entity
     * @return array
     */
    protected function _getEntityRelationships(ModelEntityInterface $entity)
    {
        return array();
    }
}
