<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Filter Traversable Interface
 *
 * This interface signals FilterAbstract::getInstance() to decorate the Filter with a FilterIterator. The iterator
 * will traverse the data if it's traversable and filter each value separately.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Filter
 * @see FilterAbstract::getInstance()
 */
interface FilterTraversable {}