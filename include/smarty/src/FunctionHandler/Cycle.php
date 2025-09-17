<?php
namespace Smarty\FunctionHandler;

use Smarty\Template;

/**
 * Smarty {cycle} function plugin
 * Type:     function
 * Name:     cycle
 * Date:     May 3, 2002
 * Purpose:  cycle through given values
 * Params:
 *
 * - name      - name of cycle (optional)
 * - values    - comma separated list of values to cycle, or an array of values to cycle
 *               (this can be left out for subsequent calls)
 * - reset     - boolean - resets given var to true
 * - print     - boolean - print var or not. default is true
 * - advance   - boolean - whether to advance the cycle
 * - delimiter - the value delimiter, default is ","
 * - assign    - boolean, assigns to template var instead of printed.
 *
 * Examples:
 *
 * {cycle values="#eeeeee,#d0d0d0d"}
 * {cycle name=row values="one,two,three" reset=true}
 * {cycle name=row}
 *
 * @author  Monte Ohrt <monte at ohrt dot com>
 * @author  credit to Mark Priatel <mpriatel@rogers.com>
 * @author  credit to Gerard <gerard@interfold.com>
 * @author  credit to Jason Sweat <jsweat_php@yahoo.com>
 * @version 1.3
 *
 * @param array                    $params   parameters
 * @param Template $template template object
 *
 * @return string|null
 */
class Cycle extends Base {

	public function handle($params, Template $template) {
		static $cycle_vars;
		$name = (empty($params['name'])) ? 'default' : $params['name'];
		$print = !(isset($params['print'])) || (bool)$params['print'];
		$advance = !(isset($params['advance'])) || (bool)$params['advance'];
		$reset = isset($params['reset']) && (bool)$params['reset'];
		if (!isset($params['values'])) {
			if (!isset($cycle_vars[$name]['values'])) {
				trigger_error('cycle: missing \'values\' parameter');
				return;
			}
		} else {
			if (isset($cycle_vars[$name]['values']) && $cycle_vars[$name]['values'] !== $params['values']) {
				$cycle_vars[$name]['index'] = 0;
			}
			$cycle_vars[$name]['values'] = $params['values'];
		}
		if (isset($params['delimiter'])) {
			$cycle_vars[$name]['delimiter'] = $params['delimiter'];
		} elseif (!isset($cycle_vars[$name]['delimiter'])) {
			$cycle_vars[$name]['delimiter'] = ',';
		}
		if (is_array($cycle_vars[$name]['values'])) {
			$cycle_array = $cycle_vars[$name]['values'];
		} else {
			$cycle_array = explode($cycle_vars[$name]['delimiter'], $cycle_vars[$name]['values']);
		}
		if (!isset($cycle_vars[$name]['index']) || $reset) {
			$cycle_vars[$name]['index'] = 0;
		}
		if (isset($params['assign'])) {
			$print = false;
			$template->assign($params['assign'], $cycle_array[$cycle_vars[$name]['index']]);
		}
		if ($print) {
			$retval = $cycle_array[$cycle_vars[$name]['index']];
		} else {
			$retval = null;
		}
		if ($advance) {
			if ($cycle_vars[$name]['index'] >= count($cycle_array) - 1) {
				$cycle_vars[$name]['index'] = 0;
			} else {
				$cycle_vars[$name]['index']++;
			}
		}
		return $retval;
	}
}