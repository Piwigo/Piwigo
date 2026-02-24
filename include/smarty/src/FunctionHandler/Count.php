<?php

namespace Smarty\FunctionHandler;

use Smarty\Exception;
use Smarty\Template;

/**
 * count(Countable|array $value, int $mode = COUNT_NORMAL): int
 * If the optional mode parameter is set to COUNT_RECURSIVE (or 1), count() will recursively count the array.
 * This is particularly useful for counting all the elements of a multidimensional array.
 *
 * Returns the number of elements in value. Prior to PHP 8.0.0, if the parameter was neither an array nor an object that
 * implements the Countable interface, 1 would be returned, unless value was null, in which case 0 would be returned.
 */
class Count extends Base {

	public function handle($params, Template $template) {

		$params = array_values($params ?? []);

		if (count($params) < 1 || count($params) > 2) {
			throw new Exception("Invalid number of arguments for count. count expects 1 or 2 parameters.");
		}

		$value = $params[0];

		if ($value instanceof \Countable) {
			return $value->count();
		}

		$mode = count($params) == 2 ? (int) $params[1] : COUNT_NORMAL;
		return count((array) $value, $mode);
	}

}