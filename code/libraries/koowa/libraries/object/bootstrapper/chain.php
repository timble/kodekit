<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Object Bootstrapper Chain
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Bootstrapper
 */
class KObjectBootstrapperChain extends KObjectBootstrapperAbstract
{
   /**
     * The bootstrapper queue
     *
     * @var	KObjectQueue
     */
    protected $_queue;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config	An optional ObjectConfig object with configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        //Create the queue
        $this->_queue = $this->getObject('lib:object.queue');
    }

    /**
     * Bootstrap the object manager
     *
     * @return void
     */
    public function bootstrap()
    {
        foreach($this->_queue as $bootstrapper) {
            $bootstrapper->bootstrap();
        }
    }

    /**
     * Add a bootsstrapper to the queue based on priority
     *
     * @param KObjectBootstrapperInterface $bootstrapper A bootstrapper object
     * @param integer	            $priority   The bootstrapper priority, usually between 1 (high priority) and 5 (lowest),
     *                                          default is 3. If no priority is set, the bootstrapper priority will be used
     *                                          instead.
     * @return KObjectBootstrapperChain
     */
    public function addBootstrapper(KObjectBootstrapperInterface $bootstrapper, $priority = null)
    {
        $priority = $priority == null ? $bootstrapper->getPriority() : $priority;
        $this->_queue->enqueue($bootstrapper, $priority);

        return $this;
    }
}