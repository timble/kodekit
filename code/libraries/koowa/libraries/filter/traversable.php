<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright   Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/nooku/nooku-framework for the canonical source repository
 */

/**
 * Filter Traversable Interface
 *
 * This interface signals KFilterAbstract::getInstance() to decorate the Filter with a KFilterIterator. The iterator
 * will traverse the data if it's traversable and filter each value separately.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Filter
 * @see KFilterAbstract::getInstance()
 */
interface KFilterTraversable {}