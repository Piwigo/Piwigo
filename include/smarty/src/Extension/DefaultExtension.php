<?php

namespace Smarty\Extension;

use Smarty\Exception;

class DefaultExtension extends Base {

	private $modifiers = [];

	private $functionHandlers = [];

	private $blockHandlers = [];

	public function getModifierCompiler(string $modifier): ?\Smarty\Compile\Modifier\ModifierCompilerInterface {

		if (isset($this->modifiers[$modifier])) {
			return $this->modifiers[$modifier];
		}

		switch ($modifier) {
			case 'cat': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\CatModifierCompiler(); break;
			case 'count_characters': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\CountCharactersModifierCompiler(); break;
			case 'count_paragraphs': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\CountParagraphsModifierCompiler(); break;
			case 'count_sentences': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\CountSentencesModifierCompiler(); break;
			case 'count_words': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\CountWordsModifierCompiler(); break;
			case 'default': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\DefaultModifierCompiler(); break;
			case 'empty': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\EmptyModifierCompiler(); break;
			case 'escape': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\EscapeModifierCompiler(); break;
			case 'from_charset': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\FromCharsetModifierCompiler(); break;
			case 'indent': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\IndentModifierCompiler(); break;
			case 'is_array': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\IsArrayModifierCompiler(); break;
			case 'isset': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\IssetModifierCompiler(); break;
			case 'json_encode': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\JsonEncodeModifierCompiler(); break;
			case 'lower': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\LowerModifierCompiler(); break;
			case 'nl2br': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\Nl2brModifierCompiler(); break;
			case 'noprint': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\NoPrintModifierCompiler(); break;
			case 'raw': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\RawModifierCompiler(); break;
			case 'round': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\RoundModifierCompiler(); break;
			case 'str_repeat': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\StrRepeatModifierCompiler(); break;
			case 'string_format': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\StringFormatModifierCompiler(); break;
			case 'strip': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\StripModifierCompiler(); break;
			case 'strip_tags': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\StripTagsModifierCompiler(); break;
			case 'strlen': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\StrlenModifierCompiler(); break;
			case 'substr': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\SubstrModifierCompiler(); break;
			case 'to_charset': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\ToCharsetModifierCompiler(); break;
			case 'unescape': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\UnescapeModifierCompiler(); break;
			case 'upper': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\UpperModifierCompiler(); break;
			case 'wordwrap': $this->modifiers[$modifier] = new \Smarty\Compile\Modifier\WordWrapModifierCompiler(); break;
		}

		return $this->modifiers[$modifier] ?? null;
	}

	public function getModifierCallback(string $modifierName) {
		switch ($modifierName) {
			case 'capitalize': return [$this, 'smarty_modifier_capitalize'];
			case 'count': return [$this, 'smarty_modifier_count'];
			case 'date_format': return [$this, 'smarty_modifier_date_format'];
			case 'debug_print_var': return [$this, 'smarty_modifier_debug_print_var'];
			case 'escape': return [$this, 'smarty_modifier_escape'];
			case 'explode': return [$this, 'smarty_modifier_explode'];
			case 'implode': return [$this, 'smarty_modifier_implode'];
			case 'in_array': return [$this, 'smarty_modifier_in_array'];
			case 'join': return [$this, 'smarty_modifier_join'];
			case 'mb_wordwrap': return [$this, 'smarty_modifier_mb_wordwrap'];
			case 'number_format': return [$this, 'smarty_modifier_number_format'];
			case 'regex_replace': return [$this, 'smarty_modifier_regex_replace'];
			case 'replace': return [$this, 'smarty_modifier_replace'];
			case 'spacify': return [$this, 'smarty_modifier_spacify'];
			case 'split': return [$this, 'smarty_modifier_split'];
			case 'truncate': return [$this, 'smarty_modifier_truncate'];
		}
		return null;
	}

	public function getFunctionHandler(string $functionName): ?\Smarty\FunctionHandler\FunctionHandlerInterface {

		if (isset($this->functionHandlers[$functionName])) {
			return $this->functionHandlers[$functionName];
		}

		switch ($functionName) {
			case 'count': $this->functionHandlers[$functionName] = new \Smarty\FunctionHandler\Count(); break;
			case 'counter': $this->functionHandlers[$functionName] = new \Smarty\FunctionHandler\Counter(); break;
			case 'cycle': $this->functionHandlers[$functionName] = new \Smarty\FunctionHandler\Cycle(); break;
			case 'fetch': $this->functionHandlers[$functionName] = new \Smarty\FunctionHandler\Fetch(); break;
			case 'html_checkboxes': $this->functionHandlers[$functionName] = new \Smarty\FunctionHandler\HtmlCheckboxes(); break;
			case 'html_image': $this->functionHandlers[$functionName] = new \Smarty\FunctionHandler\HtmlImage(); break;
			case 'html_options': $this->functionHandlers[$functionName] = new \Smarty\FunctionHandler\HtmlOptions(); break;
			case 'html_radios': $this->functionHandlers[$functionName] = new \Smarty\FunctionHandler\HtmlRadios(); break;
			case 'html_select_date': $this->functionHandlers[$functionName] = new \Smarty\FunctionHandler\HtmlSelectDate(); break;
			case 'html_select_time': $this->functionHandlers[$functionName] = new \Smarty\FunctionHandler\HtmlSelectTime(); break;
			case 'html_table': $this->functionHandlers[$functionName] = new \Smarty\FunctionHandler\HtmlTable(); break;
			case 'mailto': $this->functionHandlers[$functionName] = new \Smarty\FunctionHandler\Mailto(); break;
			case 'math': $this->functionHandlers[$functionName] = new \Smarty\FunctionHandler\Math(); break;
		}

		return $this->functionHandlers[$functionName] ?? null;
	}

	public function getBlockHandler(string $blockTagName): ?\Smarty\BlockHandler\BlockHandlerInterface {

		switch ($blockTagName) {
			case 'textformat': $this->blockHandlers[$blockTagName] = new \Smarty\BlockHandler\TextFormat(); break;
		}

		return $this->blockHandlers[$blockTagName] ?? null;
	}

	/**
	 * Smarty spacify modifier plugin
	 * Type:     modifier
	 * Name:     spacify
	 * Purpose:  add spaces between characters in a string
	 *
	 * @author Monte Ohrt <monte at ohrt dot com>
	 *
	 * @param string $string       input string
	 * @param string $spacify_char string to insert between characters.
	 *
	 * @return string
	 */
	public function smarty_modifier_spacify($string, $spacify_char = ' ')
	{
		// well… what about charsets besides latin and UTF-8?
		return implode($spacify_char, preg_split('//' . \Smarty\Smarty::$_UTF8_MODIFIER, $string, -1, PREG_SPLIT_NO_EMPTY));
	}

	/**
	 * Smarty capitalize modifier plugin
	 * Type:     modifier
	 * Name:     capitalize
	 * Purpose:  capitalize words in the string
	 * {@internal {$string|capitalize:true:true} is the fastest option for MBString enabled systems }}
	 *
	 * @param string  $string    string to capitalize
	 * @param boolean $uc_digits also capitalize "x123" to "X123"
	 * @param boolean $lc_rest   capitalize first letters, lowercase all following letters "aAa" to "Aaa"
	 *
	 * @return string capitalized string
	 * @author Monte Ohrt <monte at ohrt dot com>
	 * @author Rodney Rehm
	 */
	public function smarty_modifier_capitalize($string, $uc_digits = false, $lc_rest = false)
	{
		$string = (string) $string;

		if ($lc_rest) {
			// uppercase (including hyphenated words)
			$upper_string = mb_convert_case($string, MB_CASE_TITLE, \Smarty\Smarty::$_CHARSET);
		} else {
			// uppercase word breaks
			$upper_string = preg_replace_callback(
				"!(^|[^\p{L}'])([\p{Ll}])!S" . \Smarty\Smarty::$_UTF8_MODIFIER,
				function ($matches)	{
					return stripslashes($matches[1]) .
						mb_convert_case(stripslashes($matches[2]), MB_CASE_UPPER, \Smarty\Smarty::$_CHARSET);
				},
				$string
			);
		}
		// check uc_digits case
		if (!$uc_digits) {
			if (preg_match_all(
				"!\b([\p{L}]*[\p{N}]+[\p{L}]*)\b!" . \Smarty\Smarty::$_UTF8_MODIFIER,
				$string,
				$matches,
				PREG_OFFSET_CAPTURE
			)
			) {
				foreach ($matches[ 1 ] as $match) {
					$upper_string =
						substr_replace(
							$upper_string,
							mb_strtolower($match[ 0 ], \Smarty\Smarty::$_CHARSET),
							$match[ 1 ],
							strlen($match[ 0 ])
						);
				}
			}
		}
		$upper_string =
			preg_replace_callback(
				"!((^|\s)['\"])(\w)!" . \Smarty\Smarty::$_UTF8_MODIFIER,
				function ($matches) {
					return stripslashes(
						$matches[ 1 ]) . mb_convert_case(stripslashes($matches[ 3 ]),
						MB_CASE_UPPER,
						\Smarty\Smarty::$_CHARSET
					);
				},
				$upper_string
			);
		return $upper_string;
	}

	/**
	 * Smarty count modifier plugin
	 * Type:     modifier
	 * Name:     count
	 * Purpose:  counts all elements in an array or in a Countable object
	 * Input:
	 *          - Countable|array: array or object to count
	 *          - mode: int defaults to 0 for normal count mode, if set to 1 counts recursive
	 *
	 * @param mixed $arrayOrObject  input array/object
	 * @param int $mode       count mode
	 *
	 * @return int
	 */
	public function smarty_modifier_count($arrayOrObject, $mode = 0) {
		/*
		 * @see https://www.php.net/count
		 * > Prior to PHP 8.0.0, if the parameter was neither an array nor an object that implements the Countable interface,
		 * > 1 would be returned, unless value was null, in which case 0 would be returned.
		 */

		if ($arrayOrObject instanceof \Countable || is_array($arrayOrObject)) {
			return count($arrayOrObject, (int) $mode);
		} elseif ($arrayOrObject === null) {
			return 0;
		}
		return 1;
	}

	/**
	 * Smarty date_format modifier plugin
	 * Type:     modifier
	 * Name:     date_format
	 * Purpose:  format datestamps via strftime
	 * Input:
	 *          - string: input date string
	 *          - format: strftime format for output
	 *          - default_date: default date if $string is empty
	 *
	 * @author Monte Ohrt <monte at ohrt dot com>
	 *
	 * @param string $string       input date string
	 * @param string $format       strftime format for output
	 * @param string $default_date default date if $string is empty
	 * @param string $formatter    either 'strftime' or 'auto'
	 *
	 * @return string |void
	 * @uses   smarty_make_timestamp()
	 */
	public function smarty_modifier_date_format($string, $format = null, $default_date = '', $formatter = 'auto')
	{
		if ($format === null) {
			$format = \Smarty\Smarty::$_DATE_FORMAT;
		}

		if (!empty($string) && $string !== '0000-00-00' && $string !== '0000-00-00 00:00:00') {
			$timestamp = smarty_make_timestamp($string);
		} elseif (!empty($default_date)) {
			$timestamp = smarty_make_timestamp($default_date);
		} else {
			return;
		}
		if ($formatter === 'strftime' || ($formatter === 'auto' && strpos($format, '%') !== false)) {
			if (\Smarty\Smarty::$_IS_WINDOWS) {
				$_win_from = array(
					'%D',
					'%h',
					'%n',
					'%r',
					'%R',
					'%t',
					'%T'
				);
				$_win_to = array(
					'%m/%d/%y',
					'%b',
					"\n",
					'%I:%M:%S %p',
					'%H:%M',
					"\t",
					'%H:%M:%S'
				);
				if (strpos($format, '%e') !== false) {
					$_win_from[] = '%e';
					$_win_to[] = sprintf('%\' 2d', date('j', $timestamp));
				}
				if (strpos($format, '%l') !== false) {
					$_win_from[] = '%l';
					$_win_to[] = sprintf('%\' 2d', date('h', $timestamp));
				}
				$format = str_replace($_win_from, $_win_to, $format);
			}
			// @ to suppress deprecation errors when running in PHP8.1 or higher.
			return @strftime($format, $timestamp);
		} else {
			return date($format, $timestamp);
		}
	}

	/**
	 * Smarty debug_print_var modifier plugin
	 * Type:     modifier
	 * Name:     debug_print_var
	 * Purpose:  formats variable contents for display in the console
	 *
	 * @author Monte Ohrt <monte at ohrt dot com>
	 *
	 * @param array|object $var     variable to be formatted
	 * @param int          $max     maximum recursion depth if $var is an array or object
	 * @param int          $length  maximum string length if $var is a string
	 * @param int          $depth   actual recursion depth
	 * @param array        $objects processed objects in actual depth to prevent recursive object processing
	 *
	 * @return string
	 */
	public function smarty_modifier_debug_print_var($var, $max = 10, $length = 40, $depth = 0, $objects = array())
	{
		$_replace = array("\n" => '\n', "\r" => '\r', "\t" => '\t');
		switch (gettype($var)) {
			case 'array':
				$results = '<b>Array (' . count($var) . ')</b>';
				if ($depth === $max) {
					break;
				}
				foreach ($var as $curr_key => $curr_val) {
					$results .= '<br>' . str_repeat('&nbsp;', $depth * 2) . '<b>' . htmlspecialchars(strtr($curr_key, $_replace)) .
						'</b> =&gt; ' .
						$this->smarty_modifier_debug_print_var($curr_val, $max, $length, ++$depth, $objects);
					$depth--;
				}
				break;
			case 'object':
				$object_vars = get_object_vars($var);
				$results = '<b>' . get_class($var) . ' Object (' . count($object_vars) . ')</b>';
				if (in_array($var, $objects)) {
					$results .= ' called recursive';
					break;
				}
				if ($depth === $max) {
					break;
				}
				$objects[] = $var;
				foreach ($object_vars as $curr_key => $curr_val) {
					$results .= '<br>' . str_repeat('&nbsp;', $depth * 2) . '<b> -&gt;' . htmlspecialchars(strtr($curr_key, $_replace)) .
						'</b> = ' . $this->smarty_modifier_debug_print_var($curr_val, $max, $length, ++$depth, $objects);
					$depth--;
				}
				break;
			case 'boolean':
			case 'NULL':
			case 'resource':
				if (true === $var) {
					$results = 'true';
				} elseif (false === $var) {
					$results = 'false';
				} elseif (null === $var) {
					$results = 'null';
				} else {
					$results = htmlspecialchars((string)$var);
				}
				$results = '<i>' . $results . '</i>';
				break;
			case 'integer':
			case 'float':
				$results = htmlspecialchars((string)$var);
				break;
			case 'string':
				$results = strtr($var, $_replace);
				if (mb_strlen($var, \Smarty\Smarty::$_CHARSET) > $length) {
					$results = mb_substr($var, 0, $length - 3, \Smarty\Smarty::$_CHARSET) . '...';
				}
				$results = htmlspecialchars('"' . $results . '"', ENT_QUOTES, \Smarty\Smarty::$_CHARSET);
				break;
			case 'unknown type':
			default:
				$results = strtr((string)$var, $_replace);
				if (mb_strlen($results, \Smarty\Smarty::$_CHARSET) > $length) {
					$results = mb_substr($results, 0, $length - 3, \Smarty\Smarty::$_CHARSET) . '...';
				}
				$results = htmlspecialchars($results, ENT_QUOTES, \Smarty\Smarty::$_CHARSET);
		}
		return $results;
	}

	/**
	 * Smarty escape modifier plugin
	 * Type:     modifier
	 * Name:     escape
	 * Purpose:  escape string for output
	 *
	 * @author Monte Ohrt <monte at ohrt dot com>
	 *
	 * @param string  $string        input string
	 * @param string  $esc_type      escape type
	 * @param string  $char_set      character set, used for htmlspecialchars() or htmlentities()
	 * @param boolean $double_encode encode already encoded entitites again, used for htmlspecialchars() or htmlentities()
	 *
	 * @return string escaped input string
	 */
	public function smarty_modifier_escape($string, $esc_type = 'html', $char_set = null, $double_encode = true)
	{
		if (!$char_set) {
			$char_set = \Smarty\Smarty::$_CHARSET;
		}

		$string = (string)$string;

		switch ($esc_type) {
			case 'html':
				return htmlspecialchars($string, ENT_QUOTES, $char_set, $double_encode);
			// no break
			case 'htmlall':
				$string = mb_convert_encoding($string, 'UTF-8', $char_set);
				return htmlentities($string, ENT_QUOTES, 'UTF-8', $double_encode);
			// no break
			case 'url':
				return rawurlencode($string);
			case 'urlpathinfo':
				return str_replace('%2F', '/', rawurlencode($string));
			case 'quotes':
				// escape unescaped single quotes
				return preg_replace("%(?<!\\\\)'%", "\\'", $string);
			case 'hex':
				// escape every byte into hex
				// Note that the UTF-8 encoded character ä will be represented as %c3%a4
				$return = '';
				$_length = strlen($string);
				for ($x = 0; $x < $_length; $x++) {
					$return .= '%' . bin2hex($string[ $x ]);
				}
				return $return;
			case 'hexentity':
				$return = '';
				foreach ($this->mb_to_unicode($string, \Smarty\Smarty::$_CHARSET) as $unicode) {
					$return .= '&#x' . strtoupper(dechex($unicode)) . ';';
				}
				return $return;
			case 'decentity':
				$return = '';
				foreach ($this->mb_to_unicode($string, \Smarty\Smarty::$_CHARSET) as $unicode) {
					$return .= '&#' . $unicode . ';';
				}
				return $return;
			case 'javascript':
				// escape quotes and backslashes, newlines, etc.
				return strtr(
					$string,
					array(
						'\\' => '\\\\',
						"'"  => "\\'",
						'"'  => '\\"',
						"\r" => '\\r',
						"\n" => '\\n',
						'</' => '<\/',
						// see https://html.spec.whatwg.org/multipage/scripting.html#restrictions-for-contents-of-script-elements
						'<!--' => '<\!--',
						'<s'   => '<\s',
						'<S'   => '<\S',
						"`" => "\\\\`",
						"\${" => "\\\\\\$\\{"
					)
				);
			case 'mail':
				return smarty_mb_str_replace(
					array(
						'@',
						'.'
					),
					array(
						' [AT] ',
						' [DOT] '
					),
					$string
				);
			case 'nonstd':
				// escape non-standard chars, such as ms document quotes
				$return = '';
				foreach ($this->mb_to_unicode($string, \Smarty\Smarty::$_CHARSET) as $unicode) {
					if ($unicode >= 126) {
						$return .= '&#' . $unicode . ';';
					} else {
						$return .= chr($unicode);
					}
				}
				return $return;
			default:
				trigger_error("escape: unsupported type: $esc_type - returning unmodified string", E_USER_NOTICE);
				return $string;
		}
	}


	/**
	 * convert characters to their decimal unicode equivalents
	 *
	 * @link   http://www.ibm.com/developerworks/library/os-php-unicode/index.html#listing3 for inspiration
	 *
	 * @param string $string   characters to calculate unicode of
	 * @param string $encoding encoding of $string
	 *
	 * @return array sequence of unicodes
	 * @author Rodney Rehm
	 */
	private function mb_to_unicode($string, $encoding = null) {
		if ($encoding) {
			$expanded = mb_convert_encoding($string, 'UTF-32BE', $encoding);
		} else {
			$expanded = mb_convert_encoding($string, 'UTF-32BE');
		}
		return unpack('N*', $expanded);
	}

	/**
	 * Smarty explode modifier plugin
	 * Type:     modifier
	 * Name:     explode
	 * Purpose:  split a string by a string
	 *
	 * @param string   $separator
	 * @param string   $string
	 * @param int|null $limit
	 *
	 * @return array
	 */
	public function smarty_modifier_explode($separator, $string, ?int $limit = null)
	{
		trigger_error("Using explode is deprecated. " .
			"Use split, using the array first, separator second.", E_USER_DEPRECATED);
		// provide $string default to prevent deprecation errors in PHP >=8.1
		return explode($separator, $string ?? '', $limit ?? PHP_INT_MAX);
	}

	/**
	 * Smarty split modifier plugin
	 * Type:     modifier
	 * Name:     split
	 * Purpose:  split a string by a string
	 *
	 * @param string $string
	 * @param string   $separator
	 * @param int|null $limit
	 *
	 * @return array
	 */
	public function smarty_modifier_split($string, $separator, ?int $limit = null)
	{
		// provide $string default to prevent deprecation errors in PHP >=8.1
		return explode($separator, $string ?? '', $limit ?? PHP_INT_MAX);
	}

	/**
	 * Smarty implode modifier plugin
	 * Type:     modifier
	 * Name:     implode
	 * Purpose:  join an array of values into a single string
	 *
	 * @param array   $values
	 * @param string   $separator
	 *
	 * @return string
	 */
	public function smarty_modifier_implode($values, $separator = '')
	{

		trigger_error("Using implode is deprecated. " .
			"Use join using the array first, separator second.", E_USER_DEPRECATED);

		if (is_array($separator)) {
			return implode((string) ($values ?? ''), (array) $separator);
		}
		return implode((string) ($separator ?? ''), (array) $values);
	}

	/**
	 * Smarty in_array modifier plugin
	 * Type:     modifier
	 * Name:     in_array
	 * Purpose:  test if value is contained in an array
	 *
	 * @param mixed   $needle
	 * @param array   $array
	 * @param bool   $strict
	 *
	 * @return bool
	 */
	public function smarty_modifier_in_array($needle, $array, $strict = false)
	{
		return in_array($needle, (array) $array, (bool) $strict);
	}

	/**
	 * Smarty join modifier plugin
	 * Type:     modifier
	 * Name:     join
	 * Purpose:  join an array of values into a single string
	 *
	 * @param array   $values
	 * @param string   $separator
	 *
	 * @return string
	 */
	public function smarty_modifier_join($values, $separator = '')
	{
		if (is_array($separator)) {
			trigger_error("Using join with the separator first is deprecated. " .
				"Call join using the array first, separator second.", E_USER_DEPRECATED);
			return implode((string) ($values ?? ''), (array) $separator);
		}
		return implode((string) ($separator ?? ''), (array) $values);
	}

	/**
	 * Smarty wordwrap modifier plugin
	 * Type:     modifier
	 * Name:     mb_wordwrap
	 * Purpose:  Wrap a string to a given number of characters
	 *
	 * @link   https://php.net/manual/en/function.wordwrap.php for similarity
	 *
	 * @param string  $str   the string to wrap
	 * @param int     $width the width of the output
	 * @param string  $break the character used to break the line
	 * @param boolean $cut   ignored parameter, just for the sake of
	 *
	 * @return string  wrapped string
	 * @author Rodney Rehm
	 */
	public function smarty_modifier_mb_wordwrap($str, $width = 75, $break = "\n", $cut = false)
	{
		return smarty_mb_wordwrap($str, $width, $break, $cut);
	}

	/**
	 * Smarty number_format modifier plugin
	 * Type:     modifier
	 * Name:     number_format
	 * Purpose:  Format a number with grouped thousands
	 *
	 * @param float|null  $num
	 * @param int         $decimals
	 * @param string|null $decimal_separator
	 * @param string|null $thousands_separator
	 *
	 * @return string
	 */
	public function smarty_modifier_number_format(?float $num, int $decimals = 0, ?string $decimal_separator = ".", ?string $thousands_separator = ",")
	{
		// provide $num default to prevent deprecation errors in PHP >=8.1
		return number_format($num ?? 0.0, $decimals, $decimal_separator, $thousands_separator);
	}

	/**
	 * Smarty regex_replace modifier plugin
	 * Type:     modifier
	 * Name:     regex_replace
	 * Purpose:  regular expression search/replace
	 *
	 * @author Monte Ohrt <monte at ohrt dot com>
	 *
	 * @param string       $string  input string
	 * @param string|array $search  regular expression(s) to search for
	 * @param string|array $replace string(s) that should be replaced
	 * @param int          $limit   the maximum number of replacements
	 *
	 * @return string
	 */
	public function smarty_modifier_regex_replace($string, $search, $replace, $limit = -1)
	{
		if (is_array($search)) {
			foreach ($search as $idx => $s) {
				$search[ $idx ] = $this->regex_replace_check($s);
			}
		} else {
			$search = $this->regex_replace_check($search);
		}
		return preg_replace($search, $replace, $string, $limit);
	}

	/**
	 * @param  string $search string(s) that should be replaced
	 *
	 * @return string
	 * @ignore
	 */
	private function regex_replace_check($search)
	{
		// null-byte injection detection
		// anything behind the first null-byte is ignored
		if (($pos = strpos($search, "\0")) !== false) {
			$search = substr($search, 0, $pos);
		}
		// remove eval-modifier from $search
		if (preg_match('!([a-zA-Z\s]+)$!s', $search, $match) && (strpos($match[ 1 ], 'e') !== false)) {
			$search = substr($search, 0, -strlen($match[ 1 ])) . preg_replace('![e\s]+!', '', $match[ 1 ]);
		}
		return $search;
	}

	/**
	 * Smarty replace modifier plugin
	 * Type:     modifier
	 * Name:     replace
	 * Purpose:  simple search/replace
	 *
	 * @author Monte Ohrt <monte at ohrt dot com>
	 * @author Uwe Tews
	 *
	 * @param string $string  input string
	 * @param string $search  text to search for
	 * @param string $replace replacement text
	 *
	 * @return string
	 */
	public function smarty_modifier_replace($string, $search, $replace)
	{
		return smarty_mb_str_replace($search, $replace, $string);
	}

	/**
	 * Smarty truncate modifier plugin
	 * Type:     modifier
	 * Name:     truncate
	 * Purpose:  Truncate a string to a certain length if necessary,
	 *               optionally splitting in the middle of a word, and
	 *               appending the $etc string or inserting $etc into the middle.
	 *
	 * @author Monte Ohrt <monte at ohrt dot com>
	 *
	 * @param string  $string      input string
	 * @param integer $length      length of truncated text
	 * @param string  $etc         end string
	 * @param boolean $break_words truncate at word boundary
	 * @param boolean $middle      truncate in the middle of text
	 *
	 * @return string truncated string
	 */
	public function smarty_modifier_truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false)
	{
		if ($length === 0 || $string === null) {
			return '';
		}
		if (mb_strlen($string, \Smarty\Smarty::$_CHARSET) > $length) {
			$length -= min($length, mb_strlen($etc, \Smarty\Smarty::$_CHARSET));
			if (!$break_words && !$middle) {
				$string = preg_replace(
					'/\s+?(\S+)?$/' . \Smarty\Smarty::$_UTF8_MODIFIER,
					'',
					mb_substr($string, 0, $length + 1, \Smarty\Smarty::$_CHARSET)
				);
			}
			if (!$middle) {
				return mb_substr($string, 0, $length, \Smarty\Smarty::$_CHARSET) . $etc;
			}
			return mb_substr($string, 0, intval($length / 2), \Smarty\Smarty::$_CHARSET) . $etc .
				mb_substr($string, -intval($length / 2), $length, \Smarty\Smarty::$_CHARSET);
		}
		return $string;
	}

}
