<?php
/**
 * CssMin - A (simple) css minifier with benefits
 * 
 * --
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING 
 * BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND 
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, 
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * --
 *
 * @package		CssMin
 * @author		Joe Scylla <joe.scylla@gmail.com>
 * @copyright	2008 - 2010 Joe Scylla <joe.scylla@gmail.com>
 * @license		http://opensource.org/licenses/mit-license.php MIT License
 * @version		2.0.1.0064 (2010-09-30)
 */
class CssMin
	{
	/**
	 * State: Is in document
	 * 
	 * @var integer
	 */
	const T_DOCUMENT = 1;
	/**
	 * Token: Comment
	 * 
	 * @var integer
	 */
	const T_COMMENT = 2;
	/**
	 * Token: Generic at-rule
	 * 
	 * @var integer
	 */
	const T_AT_RULE = 3;
	/**
	 * Token: Start of @media block
	 * 
	 * @var integer
	 */
	const T_AT_MEDIA_START = 4;
	/**
	 * State: Is in @media block
	 * 
	 * @var integer
	 */
	const T_AT_MEDIA = 5;
	/**
	 * Token: End of @media block
	 * 
	 * @var integer
	 */
	const T_AT_MEDIA_END = 6;
	/**
	 * Token: Start of @font-face block
	 * 
	 * @var integer
	 */
	const T_AT_FONT_FACE_START = 7;
	/**
	 * State: Is in @font-face block
	 * 
	 * @var integer
	 */
	const T_AT_FONT_FACE = 8;
	/**
	 * Token: @font-face declaration
	 * 
	 * @var integer
	 */
	const T_FONT_FACE_DECLARATION = 9;
	/**
	 * Token: End of @font-face block
	 * 
	 * @var integer
	 */
	const T_AT_FONT_FACE_END = 10;
	/**
	 * Token: Start of @page block
	 * 
	 * @var integer
	 */
	const T_AT_PAGE_START = 11;
	/**
	 * State: Is in @page block
	 * 
	 * @var integer
	 */
	const T_AT_PAGE = 12;
	/**
	 * Token: @page declaration
	 * 
	 * @var integer
	 */
	const T_PAGE_DECLARATION = 13;
	/**
	 * Token: End of @page block
	 * 
	 * @var integer
	 */
	const T_AT_PAGE_END = 14;
	/**
	 * Token: Start of ruleset
	 * 
	 * @var integer
	 */
	const T_RULESET_START = 15;
	/**
	 * Token: Ruleset selectors
	 * 
	 * @var integer
	 */
	const T_SELECTORS = 16;
	/**
	 * Token: Start of declarations
	 * 
	 * @var integer
	 */
	const T_DECLARATIONS_START = 17;
	/**
	 * State: Is in declarations
	 * 
	 * @var integer
	 */
	const T_DECLARATIONS = 18;
	/**
	 * Token: Declaration
	 * 
	 * @var integer
	 */
	const T_DECLARATION = 19;
	/**
	 * Token: End of declarations
	 * 
	 * @var integer
	 */
	const T_DECLARATIONS_END = 20;
	/**
	 * Token: End of ruleset
	 * 
	 * @var integer
	 */
	const T_RULESET_END = 21;
	/**
	 * Token: Start of @variables block
	 * 
	 * @var integer
	 */
	const T_AT_VARIABLES_START = 100;
	/**
	 * State: Is in @variables block
	 * 
	 * @var integer
	 */
	const T_AT_VARIABLES = 101;
	/**
	 * Token: @variables declaration
	 * 
	 * @var integer
	 */
	const T_VARIABLE_DECLARATION = 102;
	/**
	 * Token: End of @variables block
	 * 
	 * @var integer
	 */
	const T_AT_VARIABLES_END = 103;
	/**
	 * State: Is in string
	 * 
	 * @var integer
	 */
	const T_STRING = 254;
	/**
	 * State: Is in url string property
	 * 
	 * @var integer
	 */
	const T_STRING_URL = 255;
	/**
	 * Css transformations table
	 * 
	 * @var array
	 */
	private static $transformations = array
		(
		"border-radius"					=> array("-moz-border-radius", "-webkit-border-radius", "-khtml-border-radius"),
		"border-top-left-radius"		=> array("-moz-border-radius-topleft", "-webkit-border-top-left-radius", "-khtml-top-left-radius"),
		"border-top-right-radius"		=> array("-moz-border-radius-topright", "-webkit-border-top-right-radius", "-khtml-top-right-radius"),
		"border-bottom-right-radius"	=> array("-moz-border-radius-bottomright", "-webkit-border-bottom-right-radius", "-khtml-border-bottom-right-radius"),
		"border-bottom-left-radius"		=> array("-moz-border-radius-bottomleft", "-webkit-border-bottom-left-radius", "-khtml-border-bottom-left-radius"),
		"box-shadow"					=> array("-moz-box-shadow", "-webkit-box-shadow", "-khtml-box-shadow"),
		"opacity"						=> array(array("CssMin", "_tOpacity")),
		"text-shadow"					=> array("-moz-text-shadow", "-webkit-text-shadow", "-khtml-text-shadow"),
		"white-space"					=> array(array("CssMin", "_tWhiteSpacePreWrap"))
		);
	/**
	 * Minifies the Css.
	 * 
	 * @param string $css
	 * @param array $config [optional]
	 * @return string
	 */
	public static function minify($css, $config = array())
		{
		$tokens = self::parse($css);
		$config = array_merge(array
			(
			"remove-empty-blocks"			=> true,
			"remove-empty-rulesets"			=> true,
			"remove-last-semicolons"		=> true,
			"convert-css3-properties"		=> false,
			"convert-color-values"			=> false,
			"compress-color-values"			=> false,
			"compress-unit-values"			=> false,
			"emulate-css3-variables"		=> true,
			), $config);
		// Minification options
 		$sRemoveEmptyBlocks			= $config["remove-empty-blocks"]; 
 		$sRemoveEmptyRulesets		= $config["remove-empty-rulesets"];
 		$sRemoveLastSemicolon		= $config["remove-last-semicolons"];
 		$sConvertCss3Properties 	= $config["convert-css3-properties"];
		$sCompressUnitValues		= $config["compress-unit-values"];
		$sConvertColorValues		= $config["convert-color-values"];
		$sCompressColorValues		= $config["compress-color-values"];
		$sEmulateCcss3Variables		= $config["emulate-css3-variables"];
		$sRemoveTokens				= array(self::T_COMMENT);
		// Remove tokens
		if (!$sEmulateCcss3Variables)
			{
			$sRemoveTokens = array_merge($sRemoveTokens, array(self::T_AT_VARIABLES_START, self::T_VARIABLE_DECLARATION, self::T_AT_VARIABLES_END));
			}
		for($i = 0, $l = count($tokens); $i < $l; $i++)
			{
			if (in_array($tokens[$i][0], $sRemoveTokens))
				{
				unset($tokens[$i]);
				}
			}
		$tokens = array_values($tokens);
		// Remove empty rulesets
		if ($sRemoveEmptyRulesets)
			{
			for($i = 0, $l = count($tokens); $i < $l; $i++)
				{
				// Remove empty rulesets
				if ($tokens[$i][0] == self::T_RULESET_START && $tokens[$i+4][0] == self::T_RULESET_END)
					{
					unset($tokens[$i]); 	// T_RULESET_START
					unset($tokens[++$i]);	// T_SELECTORS
					unset($tokens[++$i]);	// T_DECLARATIONS_START
					unset($tokens[++$i]);	// T_DECLARATIONS_END
					unset($tokens[++$i]);	// T_RULESET_END
					}
				}
			$tokens = array_values($tokens);
			}
		// Remove empty @media, @font-face or @page blocks
		if ($sRemoveEmptyBlocks)
			{
			for($i = 0, $l = count($tokens); $i < $l; $i++)
				{
				// Remove empty @media, @font-face or @page blocks
				if (($tokens[$i][0] == self::T_AT_MEDIA_START && $tokens[$i+1][0] == self::T_AT_MEDIA_END)
					|| ($tokens[$i][0] == self::T_AT_FONT_FACE_START && $tokens[$i+1][0] == self::T_AT_FONT_FACE_END)
					|| ($tokens[$i][0] == self::T_AT_PAGE_START && $tokens[$i+1][0] == self::T_AT_PAGE_END))
					{
					unset($tokens[$i]);		// T_AT_MEDIA_START, T_AT_FONT_FACE_START, T_AT_PAGE_START
					unset($tokens[++$i]);	// T_AT_MEDIA_END, T_AT_FONT_FACE_END, T_AT_PAGE_END
					}
				}
			$tokens = array_values($tokens);
			}
		// CSS Level 3 variables: parse variables
		if ($sEmulateCcss3Variables)
			{
			// Parse variables
			$variables = array();
			for($i = 0, $l = count($tokens); $i < $l; $i++)
				{
				if ($tokens[$i][0] == self::T_VARIABLE_DECLARATION)
					{
					for($i2 = 0, $l2 = count($tokens[$i][3]); $i2 < $l2; $i2++)
						{
						if (!isset($variables[$tokens[$i][3][$i2]]))
							{
							$variables[$tokens[$i][3][$i2]] = array();
							}
						$variables[$tokens[$i][3][$i2]][$tokens[$i][1]] = $tokens[$i][2];
						}
					}
				}
			}
		// Conversion and compression 
		for($i = 0, $l = count($tokens); $i < $l; $i++)
			{
			if ($tokens[$i][0] == self::T_DECLARATION)
				{
				// CSS Level 3 variables
				if ($sEmulateCcss3Variables)
					{
					if (substr($tokens[$i][2], 0, 4) == "var(" && substr($tokens[$i][2], -1, 1) == ")")
						{
						$tokens[$i][3][] = "all";
						$variable = trim(substr($tokens[$i][2], 4, -1));
						for($i2 = 0, $l2 = count($tokens[$i][3]); $i2 < $l2; $i2++)
							{
							if (isset($variables[$tokens[$i][3][$i2]][$variable]))
								{
								$tokens[$i][2] = $variables[$tokens[$i][3][$i2]][$variable];
								break;
								}
							}
						}
					}
				// Compress unit values
				if ($sCompressUnitValues)
					{
					// Compress "0.5px" to ".5px"
					$tokens[$i][2] = preg_replace("/(^| |-)0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/iS", "\${1}.\${2}\${3}", $tokens[$i][2]);
					// Compress "0px" to "0"
					$tokens[$i][2] = preg_replace("/(^| )-?(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/iS", "\${1}0", $tokens[$i][2]);
					// Compress "0 0 0 0" to "0"
					if ($tokens[$i][2] == "0 0 0 0") {$tokens[$i][2] = "0";}
					}
				// Convert RGB color values to hex ("rgb(200,60%,5)" => "#c89905")
				if ($sConvertColorValues && preg_match("/rgb\s*\(\s*([0-9%]+)\s*,\s*([0-9%]+)\s*,\s*([0-9%]+)\s*\)/iS", $tokens[$i][2], $m))
					{
					for ($i2 = 1, $l2 = count($m); $i2 < $l2; $i2++)
						{
						if (strpos("%", $m[$i2]) !== false)
							{
							$m[$i2] = substr($m[$i2], 0, -1);
							$m[$i2] = (int) (256 * ($m[$i2] / 100));
							}
						$m[$i2] = str_pad(dechex($m[$i2]),  2, "0", STR_PAD_LEFT);
						}
					$tokens[$i][2] = str_replace($m[0], "#" . $m[1] . $m[2] . $m[3], $tokens[$i][2]);
					}
				// Compress color values ("#aabbcc" to "#abc") 
				if ($sCompressColorValues && preg_match("/\#([0-9a-f]{6})/iS", $tokens[$i][2], $m))
					{
					$m[1] = strtolower($m[1]);
					if (substr($m[1], 0, 1) == substr($m[1], 1, 1) && substr($m[1], 2, 1) == substr($m[1], 3, 1) && substr($m[1], 4, 1) == substr($m[1], 5, 1))
						{
						$tokens[$i][2] = str_replace($m[0], "#" . substr($m[1], 0, 1) . substr($m[1], 2, 1) . substr($m[1], 4, 1), $tokens[$i][2]);
						}
					}
				}
			}
		// Create minified css
		$r = "";
		for($i = 0, $l = count($tokens); $i < $l; $i++)
			{
			// T_AT_RULE
			if ($tokens[$i][0] == self::T_AT_RULE)
				{
				$r .= "@" . $tokens[$i][1] . " " . $tokens[$i][2] . ";";
				}
			// T_AT_MEDIA_START
			elseif ($tokens[$i][0] == self::T_AT_MEDIA_START)
				{
				if (count($tokens[$i][1]) == 1 && $tokens[$i][1][0] == "all")
					{
					$r .= "@media{";
					}
				else
					{
					$r .= "@media " . implode(",", $tokens[$i][1]) . "{";
					}
				}
			// T_AT_FONT_FACE_START
			elseif ($tokens[$i][0] == self::T_AT_FONT_FACE_START)
				{
				$r .= "@font-face{";
				}
			// T_FONT_FACE_DECLARATION
			elseif ($tokens[$i][0] == self::T_FONT_FACE_DECLARATION)
				{
				$r .= $tokens[$i][1] . ":" . $tokens[$i][2] . ($sRemoveLastSemicolon && $tokens[$i+1][0] == self::T_AT_FONT_FACE_END ? "" : ";");
				}
			// T_AT_PAGE_START
			elseif ($tokens[$i][0] == self::T_AT_PAGE_START)
				{
				$r .= "@page{";
				}
			// T_PAGE_DECLARATION
			elseif ($tokens[$i][0] == self::T_PAGE_DECLARATION)
				{
				$r .= $tokens[$i][1] . ":" . $tokens[$i][2] . ($sRemoveLastSemicolon && $tokens[$i+1][0] == self::T_AT_PAGE_END ? "" : ";");
				}
			// T_SELECTORS
			elseif ($tokens[$i][0] == self::T_SELECTORS)
				{
				$r .= implode(",", $tokens[$i][1]);
				}
			// Start of declarations
			elseif ($tokens[$i][0] == self::T_DECLARATIONS_START)
				{
				$r .=  "{";
				}
			// T_DECLARATION
			elseif ($tokens[$i][0] == self::T_DECLARATION)
				{
				if ($sConvertCss3Properties && isset(self::$transformations[$tokens[$i][1]]))
					{
					foreach (self::$transformations[$tokens[$i][1]] as $value)
						{
						if (!is_array($value))
							{
							$r .= $value . ":" . $tokens[$i][2] . ";";
							}
						elseif (is_array($value) && is_callable($value))
							{
							$r.= call_user_func_array($value, array($tokens[$i][1], $tokens[$i][2]));
							
							}
						}
					}
				$r .= $tokens[$i][1] . ":" . $tokens[$i][2] . ($sRemoveLastSemicolon && $tokens[$i+1][0] == self::T_DECLARATIONS_END ? "" : ";");
				}
			// T_DECLARATIONS_END, T_AT_MEDIA_END, T_AT_FONT_FACE_END, T_AT_PAGE_END
			elseif (in_array($tokens[$i][0], array(self::T_DECLARATIONS_END, self::T_AT_MEDIA_END, self::T_AT_FONT_FACE_END, self::T_AT_PAGE_END)))
				{
				$r .= "}";
				}
			else
				{
				// Tokens with no output:
				// T_COMMENT
				// T_RULESET_START
				// T_RULESET_END
				// T_AT_VARIABLES_START
				// T_VARIABLE_DECLARATION
				// T_AT_VARIABLES_END
				}
			}
		return $r;
		}
	/**
	 * Parses the Css and returns a array of tokens.
	 * 
	 * @param string $css
	 * @return array
	 */
	public static function parse($css)
		{
		// Settings
		$sDefaultScope		= array("all");						// Default scope
		$sDefaultTrim		= " \t\n\r\0\x0B";					// Default trim charlist
		$sTokenChars		= "@{}();:\n\"'/*,";				// Tokens triggering parser processing
		// Basic variables
		$c					= null;								// Current char
		$p					= null;								// Previous char
		$buffer 			= "";								// Buffer
		$state				= array(self::T_DOCUMENT);			// State stack
		$currentState		= self::T_DOCUMENT;					// Current state
		$scope				= $sDefaultScope;					// Current scope
		$stringChar			= null;								// String delimiter char
		$isFilterWs			= true;								// Filter double whitespaces?
		$selectors			= array();							// Array with collected selectors
		$r 					= array();							// Return value
		// Prepare css
		$css				= str_replace("\r\n", "\n", $css);	// Windows to Unix line endings
		$css				= str_replace("\r", "\n", $css);	// Mac to Unix line endings
		while (strpos($css, "\n\n") !== false)
			{
			$css			= str_replace("\n\n", "\n", $css);	// Remove double line endings
			}
		$css				= str_replace("\t", " ", $css);		// Convert tabs to spaces
		// Parse css 
		for ($i = 0, $l = strlen($css); $i < $l; $i++)
			{
			$c = substr($css, $i, 1);
			// Filter out double spaces
			if ($isFilterWs && $c == " " && $c == $p)
				{
				continue;
				}
			$buffer .= $c;
			if (strpos($sTokenChars, $c) !== false)
				{
				// 
				$currentState	= $state[count($state) - 1];
				/*
				 * Start of comment
				 */
				if ($p == "/" && $c == "*" && $currentState != self::T_STRING && $currentState != self::T_COMMENT)
					{
					$saveBuffer = substr($buffer, 0, -2); // save the buffer (will get restored with comment ending)
					$buffer 	= $c;
					$isFilterWs	= false;
					array_push($state, self::T_COMMENT);
					}
				/*
				 * End of comment
				 */
				elseif ($p == "*" && $c == "/" && $currentState == self::T_COMMENT)
					{
					$r[]		= array(self::T_COMMENT, trim($buffer));
					$buffer		= $saveBuffer;
					$isFilterWs	= true;
					array_pop($state);
					}
				/*
				 * Start of string
				 */
				elseif (($c == "\"" || $c == "'") && $currentState != self::T_STRING && $currentState != self::T_COMMENT && $currentState != self::T_STRING_URL)
					{
					$stringChar	= $c;
					$isFilterWs	= false;
					array_push($state, self::T_STRING);
					}
				/**
				 * Escaped LF in string => remove escape backslash and LF
				 */
				elseif ($c == "\n" && $p == "\\" && $currentState == self::T_STRING)
					{
					$buffer = substr($buffer, 0, -2);
					}
				/*
				 * End of string
				 */
				elseif ($c === $stringChar && $currentState == self::T_STRING)
					{
					if ($p == "\\") // Previous char is a escape char
						{
						$count = 1; 
						$i2 = $i -2;
						while (substr($css, $i2, 1) == "\\")
							{
							$count++;
							$i2--;
							}
						// if count of escape chars is uneven => continue with string...
						if ($count % 2)
							{
							continue;
							}
						}
					// ...else end the string
					$isFilterWs	= true;
					array_pop($state);
					$stringChar = null;
					}
				/**
				 * Start of url string property
				 */
				elseif ($c == "(" && ($currentState != self::T_COMMENT && $currentState != self::T_STRING) && strtolower(substr($css, $i - 3, 3) == "url") 
					&& ($currentState == self::T_DECLARATION || $currentState == self::T_FONT_FACE_DECLARATION || $currentState == self::T_PAGE_DECLARATION || $currentState == self::T_VARIABLE_DECLARATION))
					{
					array_push($state, self::T_STRING_URL);
					}
				/**
				 * End of url string property
				 */
				elseif (($c == ")" || $c == "\n") && ($currentState != self::T_COMMENT && $currentState != self::T_STRING) && $currentState == self::T_STRING_URL)
					{
					if ($p == "\\")
						{
						continue;
						}
					array_pop($state);
					}
				/*
				 * Start of at-rule @media block
				 */
				elseif ($c == "@" && $currentState == self::T_DOCUMENT && strtolower(substr($css, $i, 6)) == "@media")
					{
					$i			= $i + 6;
					$buffer 	= "";
					array_push($state, self::T_AT_MEDIA_START);
					}
				/*
				 * At-rule @media block media types
				 */
				elseif ($c == "{" && $currentState == self::T_AT_MEDIA_START)
					{
					$buffer 	= strtolower(trim($buffer, $sDefaultTrim . "{"));
					$scope		= $buffer != "" ? array_filter(array_map("trim", explode(",", $buffer))) : $sDefaultScope;
					$r[]		= array(self::T_AT_MEDIA_START, $scope);
					$i			= $i++;
					$buffer		= "";
					array_pop($state);
					array_push($state, self::T_AT_MEDIA);
					}
				/*
				 * End of at-rule @media block
				 */
				elseif ($currentState == self::T_AT_MEDIA && $c == "}")
					{
					$r[]		= array(self::T_AT_MEDIA_END);
					$scope		= $sDefaultScope;
					$buffer		= "";
					array_pop($state);
					}
				/*
				 * Start of at-rule @font-face block
				 */
				elseif ($c == "@" && $currentState == self::T_DOCUMENT && strtolower(substr($css, $i, 10)) == "@font-face")
					{
					$r[]		= array(self::T_AT_FONT_FACE_START);
					$i			= $i + 10;
					$buffer 	= "";
					array_push($state, self::T_AT_FONT_FACE);
					}
				/*
				 * @font-face declaration: Property
				 */
				elseif ($c == ":" && $currentState == self::T_AT_FONT_FACE)
					{
					$property	= trim($buffer, $sDefaultTrim . ":{");
					$buffer		= "";
					array_push($state, self::T_FONT_FACE_DECLARATION);
					}
				/*
				 * @font-face declaration: Value
				 */
				elseif (($c == ";" || $c == "}" || $c == "\n") && $currentState == self::T_FONT_FACE_DECLARATION)
					{
					$value		= trim($buffer, $sDefaultTrim . ";}");
					$r[]		= array(self::T_FONT_FACE_DECLARATION, $property, $value, $scope);
					$buffer		= "";
					array_pop($state);
					if ($c == "}") // @font-face declaration closed with a right curly brace => closes @font-face block
						{
						array_pop($state);
						$r[]	= array(self::T_AT_FONT_FACE_END);
						}
					}
				/*
				 * End of at-rule @font-face block
				 */
				elseif ($c == "}" && $currentState == self::T_AT_FONT_FACE)
					{
					$r[]		= array(self::T_AT_FONT_FACE_END);
					$buffer		= "";
					array_pop($state);
					}
				/*
				 * Start of at-rule @page block
				 */
				elseif ($c == "@" && $currentState == self::T_DOCUMENT && strtolower(substr($css, $i, 5)) == "@page")
					{
					$r[]		= array(self::T_AT_PAGE_START);
					$i			= $i + 5;
					$buffer 	= "";
					array_push($state, self::T_AT_PAGE);
					}
				/*
				 * @page declaration: Property
				 */
				elseif ($c == ":" && $currentState == self::T_AT_PAGE)
					{
					$property	= trim($buffer, $sDefaultTrim . ":{");
					$buffer		= "";
					array_push($state, self::T_PAGE_DECLARATION);
					}
				/*
				 * @page declaration: Value
				 */
				elseif (($c == ";" || $c == "}" || $c == "\n") && $currentState == self::T_PAGE_DECLARATION)
					{
					$value		= trim($buffer, $sDefaultTrim . ";}");
					$r[]		= array(self::T_PAGE_DECLARATION, $property, $value, $scope);
					$buffer		= "";
					array_pop($state);
					if ($c == "}") // @page declaration closed with a right curly brace => closes @font-face block
						{
						array_pop($state);
						$r[]	= array(self::T_AT_PAGE_END);
						}
					}
				/*
				 * End of at-rule @page block
				 */
				elseif ($c == "}" && $currentState == self::T_AT_PAGE)
					{
					$r[]		= array(self::T_AT_PAGE_END);
					$buffer		= "";
					array_pop($state);
					}
				/*
				 * Start of at-rule @variables block
				 */
				elseif ($c == "@" && $currentState == self::T_DOCUMENT &&  strtolower(substr($css, $i, 10)) == "@variables")
					{
					$i			= $i + 10;
					$buffer 	= "";
					array_push($state, self::T_AT_VARIABLES_START);
					}
				/*
				 * @variables media types
				 */
				elseif ($c == "{" && $currentState == self::T_AT_VARIABLES_START)
					{
					$buffer 	= strtolower(trim($buffer, $sDefaultTrim . "{"));
					$r[]		= array(self::T_AT_VARIABLES_START, $scope);
					$scope		= $buffer != "" ? array_filter(array_map("trim", explode(",", $buffer))) : $sDefaultScope;
					$i			= $i++;
					$buffer		= "";
					array_pop($state);
					array_push($state, self::T_AT_VARIABLES);
					}
				/*
				 * @variables declaration: Property
				 */
				elseif ($c == ":" && $currentState == self::T_AT_VARIABLES)
					{
					$property	= trim($buffer, $sDefaultTrim . ":");
					$buffer		= "";
					array_push($state, self::T_VARIABLE_DECLARATION);
					}
				/*
				 * @variables declaration: Value
				 */
				elseif (($c == ";" || $c == "}" || $c == "\n") && $currentState == self::T_VARIABLE_DECLARATION)
					{
					$value		= trim($buffer, $sDefaultTrim . ";}");
					$r[]		= array(self::T_VARIABLE_DECLARATION, $property, $value, $scope);
					$buffer		= "";
					array_pop($state);
					if ($c == "}") // @variable declaration closed with a right curly brace => closes @variables block
						{
						array_pop($state);
						$r[]	= array(self::T_AT_VARIABLES_END);
						$scope	= $sDefaultScope;
						}
					}
				/*
				 * End of at-rule @variables block
				 */
				elseif ($c == "}" && $currentState == self::T_AT_VARIABLES)
					{
					$r[]		= array(self::T_AT_VARIABLES_END);
					$scope		= $sDefaultScope;
					$buffer		= "";
					array_pop($state);
					}
				/*
				 * Start of document level at-rule
				 */
				elseif ($c == "@" && $currentState == self::T_DOCUMENT)
					{
					$buffer		= "";
					array_push($state, self::T_AT_RULE);
					}
				/*
				 * End of document level at-rule
				 */
				elseif ($c == ";" && $currentState == self::T_AT_RULE)
					{
					$pos		= strpos($buffer, " ");
					$rule		= substr($buffer, 0, $pos);
					$value		= trim(substr($buffer, $pos), $sDefaultTrim . ";");
					$r[]		= array(self::T_AT_RULE, $rule, $value);
					$buffer		= "";
					array_pop($state);
					}
				/**
				 * Selector
				 */
				elseif ($c == "," && ($currentState == self::T_AT_MEDIA || $currentState ==  self::T_DOCUMENT))
					{
					$selectors[]= trim($buffer, $sDefaultTrim . ",");
					$buffer		= "";
					}
				/*
				 * Start of ruleset
				 */
				elseif ($c == "{" && ($currentState == self::T_AT_MEDIA || $currentState == self::T_DOCUMENT))
					{
					$selectors[]= trim($buffer, $sDefaultTrim . "{");
					$selectors 	= array_filter(array_map("trim", $selectors));
					$r[]		= array(self::T_RULESET_START);
					$r[]		= array(self::T_SELECTORS, $selectors);
					$r[]		= array(self::T_DECLARATIONS_START);
					$buffer		= "";
					$selectors	= array();
					array_push($state, self::T_DECLARATIONS);
					}
				/*
				 * Declaration: Property
				 */
				elseif ($c == ":" && $currentState == self::T_DECLARATIONS)
					{
					$property	= trim($buffer, $sDefaultTrim . ":;");
					$buffer		= "";
					array_push($state, self::T_DECLARATION);
					}
				/*
				 * Declaration: Value
				 */
				elseif (($c == ";" || $c == "}" || $c == "\n") && $currentState == self::T_DECLARATION)
					{
					$value		= trim($buffer, $sDefaultTrim . ";}");
					$r[]		= array(self::T_DECLARATION, $property, $value, $scope);
					$buffer		= "";
					array_pop($state);
					if ($c == "}") // declaration closed with a right curly brace => close ruleset
						{
						array_pop($state);
						$r[]	= array(self::T_DECLARATIONS_END);
						$r[]	= array(self::T_RULESET_END);
						}
					}
				/*
				 * End of ruleset
				 */
				elseif ($c == "}" && $currentState == self::T_DECLARATIONS)
					{
					$r[]		= array(self::T_DECLARATIONS_END);
					$r[]		= array(self::T_RULESET_END);
					$buffer		= "";
					array_pop($state);
					}
				
				}
			
			$p = $c;
			}
		return $r;
		}
	/**
	 * Transforms "opacity: {value}" into browser specific counterparts.
	 * 
	 * @param string $property
	 * @param string $value
	 * @return string
	 */
	private static function _tOpacity($property, $value)
		{
		$ieValue = (int) ((float) $value * 100);
		$r  = "-moz-opacity:" . $value . ";";						// Firefox < 3.5
		$r .= "-ms-filter: \"alpha(opacity=" . $ieValue . ")\";";	// Internet Explorer 8
		$r .= "filter: alpha(opacity=" . $ieValue . ");zoom: 1;";	// Internet Explorer 4 - 7
		return $r;
		}
	/**
	 * Transforms "white-space: pre-wrap" into browser specific counterparts.
	 * 
	 * @param string $property
	 * @param string $value
	 * @return string
	 */
	private static function _tWhiteSpacePreWrap($property, $value)
		{
		if (strtolower($value) == "pre-wrap")
			{
			$r  = "white-space:-moz-pre-wrap;";		// Mozilla
			$r .= "white-space:-webkit-pre-wrap;";	// Webkit
			$r .= "white-space:-khtml-pre-wrap;";	// khtml
			$r .= "white-space:-pre-wrap;";			// Opera 4 - 6
			$r .= "white-space:-o-pre-wrap;";		// Opera 7+
			$r .= "word-wrap:break-word;";			// Internet Explorer 5.5+
			return $r;
			}
		else
			{
			return "";
			}
		}
	}
?>