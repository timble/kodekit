// <?php !! This fools phpdocumentor into parsing this file
/**
 * @version		$Id$
 * @category    Koowa
 * @package     Koowa_Media
 * @copyright	Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org
 */

// needed for Table Column ordering
function KTableOrdering( order, dir, task ) 
{
	var form = document.adminForm;

	form.filter_order.value 	= order;
	form.filter_direction.value	= dir;
	submitform( task );
}

