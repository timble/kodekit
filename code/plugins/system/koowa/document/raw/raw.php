<?php
/**
 * @version     $Id$
 * @category	Koowa
 * @package     Koowa_Document
 * @copyright   Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license     GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link        http://www.koowa.org
 */

/**
 * Provides an easy interface to parse and display raw output
 *
 * @author		Johan Janssens <johan@joomlatools.org>
 * @category	Koowa
 * @package		Koowa_Document
 * @subpackage	Raw
 */

class KDocumentRaw extends KDocumentAbstract
{

	/**
	 * Class constructore
	 *
	 * @param	array	$options Associative array of options
	 */
	public function __construct(array $options = array())
	{
		parent::__construct($options);

		// Set the mime encoding
		$this->setMimeEncoding('text/html');
	}

	/**
	 * Render the document.
	 *
	 * @param boolean 	$cache		If true, cache the output
	 * @param array		$params		Associative array of attributes
	 * @return 	The rendered data
	 */
	public function render( $cache = false, array $params = array())
	{
		parent::render();
		return $this->getBuffer();
	}
}
