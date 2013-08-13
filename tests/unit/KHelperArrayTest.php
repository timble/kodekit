<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * KHelperArrayTest
 *
 * @author      Mathias Verraes <mathias@joomlatools.org>
 * @package     Koowa_Tests
 */
class KHelperArrayTest extends PHPUnit_Framework_TestCase
{
    public static function provideForSettype()
    {
        $expected1  = array(1, 2, array(3, 4));
        $array1     = array('1', '2.5', array('3', '4'));
        $type1      = 'int';
        $recursive1 = true;
                
        // $expected, $array, $type, $recursive
        return array(
            array($expected1, $array1, $type1, $recursive1)                
        );
    }
    
    /**
     * @dataProvider provideForSettype
     */
    public function testSettype($expected, $array, $type, $recursive)
    {
    	$this->assertEquals(
    	    $expected,
    	    KHelperArray::settype($array, $type, $recursive)    	    
        );
    }
    
    public static function provideForGetColumn()
    {
        $expected1 = array('This', 'is', 'a', 'test');
        $array1    = array(
            _KHelperArrayTest::getInstance()->set('This'),
            _KHelperArrayTest::getInstance()->set('is'),
            _KHelperArrayTest::getInstance()->set('a'),
            _KHelperArrayTest::getInstance()->set('test')
        );
        $key1     = 'text';
        
        // $expected, $array, $key
        return array(
            array($expected1, $array1, $key1)
        );
    }
    
    /**
     * @dataProvider provideForGetColumn
     */
    public function testGetColumn($expected, $array, $key)
    {
        $this->assertEquals(
            $expected,
            KHelperArray::getColumn($array, $key)
        );
    }

}

/**
 * Helper class for the test
 *
 */
class _KHelperArrayTest
{
    public $id;
    public $text;
    public function __construct()
    {
        static $i = 0;
        $this->id = $i++;
    }
    public function getInstance()
    {
        return new _KHelperArrayTest;
    }
    public function set($text)
    {
        $this->text = $text;
        return $this;
    }
    
}


