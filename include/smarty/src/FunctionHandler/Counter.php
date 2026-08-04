<?php
namespace Smarty\FunctionHandler;

use Smarty\Template;

/**
 * Smarty {counter} function plugin
 * Type:     function
 * Name:     counter
 * Purpose:  print out a counter value
 *
 * @param array                    $params   parameters
 * @param Template $template template object
 *
 * @return string|null
 *
 * @author Monte Ohrt <monte at ohrt dot com>
 */
class Counter extends Base {

	private $counters = [];

	public function handle($params, Template $template) {
		$name = (isset($params['name'])) ? $params['name'] : 'default';
		if (!isset($this->counters[$name])) {
			$this->counters[$name] = ['start' => 1, 'skip' => 1, 'direction' => 'up', 'count' => 1];
		}
		$counter =& $this->counters[$name];
		if (isset($params['start'])) {
			$counter['start'] = $counter['count'] = (int)$params['start'];
		}
		if (!empty($params['assign'])) {
			$counter['assign'] = $params['assign'];
		}
		if (isset($counter['assign'])) {
			$template->assign($counter['assign'], $counter['count']);
		}
		if (isset($params['print'])) {
			$print = (bool)$params['print'];
		} else {
			$print = empty($counter['assign']);
		}
		if ($print) {
			$retval = $counter['count'];
		} else {
			$retval = null;
		}
		if (isset($params['skip'])) {
			$counter['skip'] = $params['skip'];
		}
		if (isset($params['direction'])) {
			$counter['direction'] = $params['direction'];
		}
		if ($counter['direction'] === 'down') {
			$counter['count'] -= $counter['skip'];
		} else {
			$counter['count'] += $counter['skip'];
		}
		return $retval;
	}
}