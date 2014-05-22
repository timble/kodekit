<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * KoowaTest
 *
 * @author		Mathias Verraes <mathias@joomlatools.org>
 * @package		Koowa_Tests
 */
class KoowaTest extends PHPUnit_Framework_TestCase
{
	public function testKoowaExists()
	{
		$this->assertTrue(class_exists('Koowa'));
	}
}