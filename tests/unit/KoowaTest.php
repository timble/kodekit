<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * KoowaTest
 *
 * @package		Koowa_Tests
 */
class KoowaTest extends PHPUnit_Framework_TestCase
{
    public function testKoowaExists()
    {
        $this->assertTrue(class_exists('Koowa'));
    }
}