<?php
/**
* @version      $Id:koowa.php 251 2008-06-14 10:06:53Z mjaz $
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


2008-08-25 Mathias Verraes
 + Added KFilterAscii
 
2008-08-24 Johan Janssens
 - Removed KObject::getError, setError and getErrors
 ^ Replaced all calls to JError::raiseError by throwing KExceptions
 ! Need to have a look at how to deal with JError::raiseNotice and raiseWarning  

2008-08-24 Mathias Verraes
 + Added KFilter and KRequest

2008-08-22 Mathias Verraes
 + Added changelog, license and readme 