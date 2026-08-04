<?php

namespace Smarty\FunctionHandler;

class HtmlBase extends Base {

	/**
	 * @param $inputType
	 * @param      $name
	 * @param      $value
	 * @param      $output
	 * @param $ismultiselect
	 * @param      $selected
	 * @param      $extra
	 * @param      $separator
	 * @param      $labels
	 * @param      $label_ids
	 * @param bool $escape
	 *
	 * @return string
	 */
	protected function getHtmlForInput(
		$inputType,
		$name,
		$value,
		$output,
		$ismultiselect,
		$selected,
		$extra,
		$separator,
		$labels,
		$label_ids,
		$escape = true
	) {

		$_output = '';
		if (is_object($value)) {
			if (method_exists($value, '__toString')) {
				$value = (string)$value->__toString();
			} else {
				trigger_error(
					'value is an object of class \'' . get_class($value) .
					'\' without __toString() method',
					E_USER_NOTICE
				);
				return '';
			}
		} else {
			$value = (string)$value;
		}
		if (is_object($output)) {
			if (method_exists($output, '__toString')) {
				$output = (string)$output->__toString();
			} else {
				trigger_error(
					'output is an object of class \'' . get_class($output) .
					'\' without __toString() method',
					E_USER_NOTICE
				);
				return '';
			}
		} else {
			$output = (string)$output;
		}
		if ($labels) {
			if ($label_ids) {
				$_id = smarty_function_escape_special_chars(
					preg_replace(
						'![^\w\-\.]!' . \Smarty\Smarty::$_UTF8_MODIFIER,
						'_',
						$name . '_' . $value
					)
				);
				$_output .= '<label for="' . $_id . '">';
			} else {
				$_output .= '<label>';
			}
		}
		$name = smarty_function_escape_special_chars($name);
		$value = smarty_function_escape_special_chars($value);
		if ($escape) {
			$output = smarty_function_escape_special_chars($output);
		}
		$_output .= '<input type="' . $inputType . '" name="' . $name;
		if ($ismultiselect) {
			$_output .= '[]';
		}
		$_output .= '" value="' . $value . '"';
		if ($labels && $label_ids) {
			$_output .= ' id="' . $_id . '"';
		}
		if ($ismultiselect && is_array($selected)) {
			if (isset($selected[ $value ])) {
				$_output .= ' checked="checked"';
			}
		} elseif ($value === $selected) {
			$_output .= ' checked="checked"';
		}
		$_output .= $extra . ' />' . $output;
		if ($labels) {
			$_output .= '</label>';
		}
		$_output .= $separator;
		return $_output;
	}

}