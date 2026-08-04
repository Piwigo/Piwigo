<?php

namespace Smarty\Runtime;
use Smarty\Template;

/**
 * Foreach Runtime Methods count(), init(), restore()
 *


 * @author     Uwe Tews
 */
class ForeachRuntime {

	/**
	 * Stack of saved variables
	 *
	 * @var array
	 */
	private $stack = [];

	/**
	 * Init foreach loop
	 *  - save item and key variables, named foreach property data if defined
	 *  - init item and key variables, named foreach property data if required
	 *  - count total if required
	 *
	 * @param \Smarty\Template $tpl
	 * @param mixed $from values to loop over
	 * @param string $item variable name
	 * @param bool $needTotal flag if we need to count values
	 * @param null|string $key variable name
	 * @param null|string $name of named foreach
	 * @param array $properties of named foreach
	 *
	 * @return mixed $from
	 */
	public function init(
		Template $tpl,
		         $from,
		         $item,
		         $needTotal = false,
		         $key = null,
		         $name = null,
		         $properties = []
	) {
		$needTotal = $needTotal || isset($properties['total']);
		$saveVars = [];
		$total = null;
		if (!is_array($from)) {
			if (is_object($from)) {
				if ($needTotal) {
					$total = $this->count($from);
				}
			} else {
				settype($from, 'array');
			}
		}
		if (!isset($total)) {
			$total = empty($from) ? 0 : ($needTotal ? count($from) : 1);
		}
		if ($tpl->hasVariable($item)) {
			$saveVars['item'] = [
				$item,
				$tpl->getVariable($item)->getValue(),
			];
		}
		$tpl->assign($item,null);
		if ($total === 0) {
			$from = null;
		} else {
			if ($key) {
				if ($tpl->hasVariable($key)) {
					$saveVars['key'] = [
						$key,
						clone $tpl->getVariable($key),
					];
				}
				$tpl->assign($key, null);
			}
		}
		if ($needTotal) {
			$tpl->getVariable($item)->total = $total;
		}
		if ($name) {
			$namedVar = "__smarty_foreach_{$name}";
			if ($tpl->hasVariable($namedVar)) {
				$saveVars['named'] = [
					$namedVar,
					clone $tpl->getVariable($namedVar),
				];
			}
			$namedProp = [];
			if (isset($properties['total'])) {
				$namedProp['total'] = $total;
			}
			if (isset($properties['iteration'])) {
				$namedProp['iteration'] = 0;
			}
			if (isset($properties['index'])) {
				$namedProp['index'] = -1;
			}
			if (isset($properties['show'])) {
				$namedProp['show'] = ($total > 0);
			}
			$tpl->assign($namedVar, $namedProp);
		}
		$this->stack[] = $saveVars;
		return $from;
	}

	/**
	 * [util function] counts an array, arrayAccess/traversable or PDOStatement object
	 *
	 * @param mixed $value
	 *
	 * @return int   the count for arrays and objects that implement countable, 1 for other objects that don't, and 0
	 *               for empty elements
	 * @throws \Exception
	 */
	public function count($value): int
	{
		if ($value instanceof \IteratorAggregate) {
			// Note: getIterator() returns a Traversable, not an Iterator
			// thus rewind() and valid() methods may not be present
			return iterator_count($value->getIterator());
		} elseif ($value instanceof \Iterator) {
			return $value instanceof \Generator ? 1 : iterator_count($value);
		} elseif ($value instanceof \Countable) {
			return count($value);
		}
		return count((array) $value);
	}

	/**
	 * Restore saved variables
	 *
	 * will be called by {break n} or {continue n} for the required number of levels
	 *
	 * @param \Smarty\Template $tpl
	 * @param int $levels number of levels
	 */
	public function restore(Template $tpl, $levels = 1) {
		while ($levels) {
			$saveVars = array_pop($this->stack);
			if (!empty($saveVars)) {
				if (isset($saveVars['item'])) {
					$tpl->getVariable($saveVars['item'][0])->setValue($saveVars['item'][1]);
				}
				if (isset($saveVars['key'])) {
					$tpl->setVariable($saveVars['key'][0], $saveVars['key'][1]);
				}
				if (isset($saveVars['named'])) {
					$tpl->setVariable($saveVars['named'][0], $saveVars['named'][1]);
				}
			}
			$levels--;
		}
	}
}
