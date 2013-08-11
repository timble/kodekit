<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Controller Toolbar Command
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Controller
 */
class KControllerToolbarCommand extends KConfig
{
 	/**
     * The command name
     *
     * @var string
     */
    protected $_name;

    /**
     * Constructor.
     *
     * @param	string 			$name The command name
     * @param   array|KConfig 	$config An associative array of configuration settings or a KConfig instance.
     */
    public function __construct( $name, $config = array() )
    {
        parent::__construct($config);

        $this->append(array(
            'icon'       => 'icon-32-'.$name,
            'id'         => $name,
            'label'      => ucfirst($name),
            'disabled'   => false,
            'title'		 => '',
            'attribs'    => array(
                'class'        => array(),
            )
        ));

        //Set the command name
        $this->_name = $name;
    }

    /**
     * Get the command name
     *
     * @return string	The command name
     */
    public function getName()
    {
        return $this->_name;
    }
}
