<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * KStringInflectorTest
 *
 * @author      Mathias Verraes <mathias@joomlatools.org>
 * @package     Koowa_Tests
 */
class KStringInflectorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Provides data for the test
     */
    public static function provideNames()
    {
        // classified, separator, split, exploded, camelized, underscored
        return array(
            array(
                'PrefixBaseSuffix',
                'base',
                array('prefix'=>'prefix', 'base'=>'base', 'suffix'=>'suffix'),
                array('prefix', 'base', 'suffix'),
                'PrefixBaseSuffix',
                'prefix_base_suffix'
                ),
            array(
                'BaseSuffix',
                'base',
                array('prefix'=>'', 'base'=>'base', 'suffix'=>'suffix'),
                array('base', 'suffix'),
                'BaseSuffix',
                'base_suffix'
                ),
            array(
                'PrefixBase',
                'base',
                array('prefix'=>'prefix', 'base'=>'base', 'suffix'=>''),
                array('prefix', 'base'),
                'PrefixBase',
                'prefix_base'
                ),
            array(
                'Base',
                'base',
                array('prefix'=>'', 'base'=>'base', 'suffix'=>''),
                array('base'),
                'Base',
                'base'
                ),
            array(
                'PrefixDoubleBaseSuffix',
                'doublebase',
                array('prefix'=>'prefix', 'base'=>'doublebase', 'suffix'=>'suffix'),
                array('prefix', 'double', 'base', 'suffix'),
                'PrefixDoubleBaseSuffix',
                'prefix_double_base_suffix'
                )
        );
    }

    /**
     * Provides data for the test
     */
    public static function providePlurals()
    {
        // singular, plural
        return array(
            array('person',     'people'),
            array('item',       'items'),
            array('aircraft',   'aircraft'),
            array('cannon',     'cannon'),
            array('deer',       'deer'),
            array('quiz',       'quizzes'),
            array('child',      'children'),
            array('foot',       'feet'),
            array('suffix',     'suffices'),
            array('dish',       'dishes'),
            array('tomato',     'tomatoes'),
            array('hero',       'heroes'),
            array('cherry',     'cherries'),
            array('monkey',     'monkeys'),
            array('calf',       'calves'),
            array('knife',      'knives'),
            array('moose',      'moose'),
            array('swine',      'swine'),
            array('woman',      'women'),
            array('alumna',     'alumnae'),
            array('vertex',     'vertices'),
            array('crisis',     'crises'),
            array('addendum',   'addenda'),
            array('genus',      'genera')
        );
    }

    /**
     * @dataProvider provideNames
     */
    public function testUnderscoredToCamelize($classified, $separator, $split, $exploded, $camelized, $underscored)
    {
    	$this->assertEquals(KStringInflector::camelize($underscored), $camelized);
    }

    /**
     * @dataProvider provideNames
     */
    public function testCamelizeToUnderscored($classified, $separator, $split, $exploded, $camelized, $underscored)
    {
        $this->assertEquals(KStringInflector::underscore($camelized), $underscored);
    }

    /**
     * @dataProvider provideNames
     */
    public function testExplode($classified, $separator, $split, $exploded)
    {
        $this->assertEquals( KStringInflector::explode($classified), $exploded);
    }

    public function testgetPart()
    {
        $this->markTestIncomplete('Not implemented');
    }

    public function testHumanize()
    {
        $this->markTestIncomplete('Not implemented');
    }

    /**
     * @dataProvider providePlurals
     */
    public function testIsPlural($singular, $plural)
    {
        $this->assertTrue(KStringInflector::isPlural($plural));
    }

    /**
     * @dataProvider providePlurals
     */
    public function testIsSingular($singular, $plural)
    {
        $this->assertTrue(KStringInflector::isSingular($singular));
    }

    /**
     * @dataProvider providePlurals
     */
    public function testPluralize($singular, $plural)
    {
        $this->assertEquals(KStringInflector::pluralize($singular), $plural);
    }

    /**
     * @dataProvider providePlurals
     */
    public function testSingularize($singular, $plural)
    {
        $this->assertEquals(KStringInflector::singularize($plural), $singular);
    }

    /**
     * @dataProvider provideNames
     */
    public function testSplit($classified, $separator, $split, $exploded)
    {
        $this->assertEquals( KStringInflector::split($separator, $classified), $split);
    }

    public function testUnderscore()
    {
        $this->markTestIncomplete('Not implemented');
    }
}


