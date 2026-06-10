<?php
namespace Smarty\FunctionHandler;

use Smarty\Template;

/**
 * Smarty {html_checkboxes} function plugin
 * File:       HtmlCheckboxes.php
 * Type:       function
 * Name:       html_checkboxes
 * Date:       24.Feb.2003
 * Purpose:    Prints out a list of checkbox input types
 * Examples:
 *
 * {html_checkboxes values=$ids output=$names}
 * {html_checkboxes values=$ids name='box' separator='<br>' output=$names}
 * {html_checkboxes values=$ids checked=$checked separator='<br>' output=$names}
 *
 * Params:
 *
 * - name       (optional) - string default "checkbox"
 * - values     (required) - array
 * - options    (optional) - associative array
 * - checked    (optional) - array default not set
 * - separator  (optional) - ie <br> or &nbsp;
 * - output     (optional) - the output next to each checkbox
 * - assign     (optional) - assign the output as an array to this variable
 * - escape     (optional) - escape the content (not value), defaults to true
 *
 * @author  Christopher Kvarme <christopher.kvarme@flashjab.com>
 * @author  credits to Monte Ohrt <monte at ohrt dot com>
 * @version 1.0
 *
 * @param array                    $params   parameters
 * @param Template $template template object
 *
 * @return string
 * @uses    smarty_function_escape_special_chars()
 * @throws \Smarty\Exception
 */
class HtmlCheckboxes extends HtmlBase {

	public function handle($params, Template $template) {
		$name = 'checkbox';
		$values = null;
		$options = null;
		$selected = [];
		$separator = '';
		$escape = true;
		$labels = true;
		$label_ids = false;
		$output = null;
		$extra = '';
		foreach ($params as $_key => $_val) {
			switch ($_key) {
				case 'name':
				case 'separator':
					$$_key = (string)$_val;
					break;
				case 'escape':
				case 'labels':
				case 'label_ids':
					$$_key = (bool)$_val;
					break;
				case 'options':
					$$_key = (array)$_val;
					break;
				case 'values':
				case 'output':
					$$_key = array_values((array)$_val);
					break;
				case 'checked':
				case 'selected':
					if (is_array($_val)) {
						$selected = [];
						foreach ($_val as $_sel) {
							if (is_object($_sel)) {
								if (method_exists($_sel, '__toString')) {
									$_sel = smarty_function_escape_special_chars((string)$_sel->__toString());
								} else {
									trigger_error(
										'html_checkboxes: selected attribute contains an object of class \'' .
										get_class($_sel) . '\' without __toString() method',
										E_USER_NOTICE
									);
									continue;
								}
							} else {
								$_sel = smarty_function_escape_special_chars((string)$_sel);
							}
							$selected[$_sel] = true;
						}
					} elseif (is_object($_val)) {
						if (method_exists($_val, '__toString')) {
							$selected = smarty_function_escape_special_chars((string)$_val->__toString());
						} else {
							trigger_error(
								'html_checkboxes: selected attribute is an object of class \'' . get_class($_val) .
								'\' without __toString() method',
								E_USER_NOTICE
							);
						}
					} else {
						$selected = smarty_function_escape_special_chars((string)$_val);
					}
					break;
				case 'checkboxes':
					trigger_error(
						'html_checkboxes: the use of the "checkboxes" attribute is deprecated, use "options" instead',
						E_USER_WARNING
					);
					$options = (array)$_val;
					break;
				case 'strict':
				case 'assign':
					break;
				case 'disabled':
				case 'readonly':
					if (!empty($params['strict'])) {
						if (!is_scalar($_val)) {
							trigger_error(
								"html_options: {$_key} attribute must be a scalar, only boolean true or string '{$_key}' will actually add the attribute",
								E_USER_NOTICE
							);
						}
						if ($_val === true || $_val === $_key) {
							$extra .= ' ' . $_key . '="' . smarty_function_escape_special_chars($_key) . '"';
						}
						break;
					}
				// omit break; to fall through!
				// no break
				default:
					if (!is_array($_val)) {
						$extra .= ' ' . $_key . '="' . smarty_function_escape_special_chars($_val) . '"';
					} else {
						trigger_error("html_checkboxes: extra attribute '{$_key}' cannot be an array", E_USER_NOTICE);
					}
					break;
			}
		}
		if (!isset($options) && !isset($values)) {
			return '';
		} /* raise error here? */
		$_html_result = [];
		if (isset($options)) {
			foreach ($options as $_key => $_val) {
				$_html_result[] =
					$this->getHtmlForInput(
						'checkbox',
						$name,
						$_key,
						$_val,
						true,
						$selected,
						$extra,
						$separator,
						$labels,
						$label_ids,
						$escape
					);
			}
		} else {
			foreach ($values as $_i => $_key) {
				$_val = isset($output[$_i]) ? $output[$_i] : '';
				$_html_result[] =
					$this->getHtmlForInput(
						'checkbox',
						$name,
						$_key,
						$_val,
						true,
						$selected,
						$extra,
						$separator,
						$labels,
						$label_ids,
						$escape
					);
			}
		}
		if (!empty($params['assign'])) {
			$template->assign($params['assign'], $_html_result);
		} else {
			return implode("\n", $_html_result);
		}
	}

}
