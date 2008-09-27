<?php
/**
* @version      $Id$
* @category		Koowa
* @package      Koowa
* @copyright    Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
* @license      GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
* @link     	http://www.koowa.org
*/
defined('_JEXEC') or die('Restricted access');
?>

Legend:

 * -> Security Fix
 # -> Bug Fix
 $ -> Language fix or change
 + -> Addition
 ^ -> Change
 - -> Removed
 ! -> Note
 
2008-09-26 Johan Janssens
 ^ KDatabaseQuery refactoring, added support for :
 	- DISTINCT
 	- SELECT COUNT(*)
 	- JOINS
 + Added KDatabase::qouteName function to improve quoting 

2008-09-26 Mathias Verraes
 + KDatabaseRowsetAbstract extends KObjectArray
 + Added KObjectArray 
 
2008-09-25 Johan Janssens
 + Added KFilterHtml and KFilterText
 
2008-09-24 Johan Janssens
 ^ Implemented KDatabaseTable::fetchAll and KDatabaseTable::fetchRow in KModelAbstract.
 ^ General cleanup in KModel query building helper functions
 + Added KDatabaseRowset::findRow method
 
2008-09-23 Johan Janssens
 ^ Added OPERATION constants to KDatabase, the database events now pass the constants
 ^ Replaced slug filtering in KModel::getDefaultStates by cmd filter. Slug filter has
   issues with new routing without id information.
 + Added event triggers to select database operations. Allows for easy implemenation
   of a logger and or query cache.
 
2008-09-17 Johan Janssens
 + Add ability to Koowa to load it's own plugins from 'koowa' group
 + Added example koowa plugin to list all events and added basic docblock information
 ^ Renamed database and application events
 
2008-09-17 Johan Janssens
 ^ Changed Koowa::import to follow KFactory conventions. You can now import files from
   either site or admin.
 + Added KRouter package, router now implements a command chain and to allow sending of
   events.

2008-09-17 Mathias Verraes
 + Added KInflector::addWord(), removed the feature from singualrize and pluralize 
 
2008-09-16 Mathias Verraes
 + Added KFilterDigit
 
2008-09-14 Johan Janssens
 ^ Moved KHelperClass to KMixinClass
 
2008-09-13 Mathias Verraes
 + Automatically added tokens in forms  can now be overriden using @token(bool $reuse)
 ^ Tokens can be reused from the previous request
 
2008-09-11 Johan Janssens
 ^ Refactored KViewHelper, added format specifier and moved current helpers into
   html subdirectory.
 ^ Renamed Koowa::getMediaURL to Koowa::getURL
 
2008-09-10 Johan Janssens
 + Added KDocument package
 + Added toString method to JHelperArray
 ^ Improved loader to be able to look for files in directories with the same name
 
2008-09-08 Johan Janssens
 + Added KHelperString class to easily handle multi-byte strings 
  
2008-09-03 Johan Janssens
 + Added KFactory::tmp method to create an object witout storing it in the factory 
   container
 ^ Reworked KRequest::get to also accept filter names as strings.
 ^ Renamed KRequest to KInput

2008-08-27 Johan Janssens
 ^ Completely refactored the factory package, implemented support for factory adapters
 + Added koow, joomla and component specific factory adapters
 - Removed KViewAbstract::getFileName, now handled by the component factory adapter
 ^ Fixed docblocks, added @uses to package blocks and @throws to function blocks
 ^ Renamed KPatternClass to KHelperClass and moved to helper package

2008-08-25 Mathias Verraes
 + Added KFilterAscii, KFilterArray*
 
2008-08-24 Johan Janssens
 - Removed KObject::getError, setError and getErrors
 ^ Replaced all calls to JError::raiseError by throwing KExceptions
 ! Need to have a look at how to deal with JError::raiseNotice and raiseWarning  

2008-08-24 Mathias Verraes
 + Added KFilter and KRequest

2008-08-22 Mathias Verraes
 + Added changelog, license and readme 