<?php
/**
 * Smarty Internal Plugin Smarty Template Compiler Base
 * This file contains the basic classes and methods for compiling Smarty templates with lexer/parser
 *


 * @author     Uwe Tews
 */

namespace Smarty\Compiler;

use Smarty\Compile\BlockCompiler;
use Smarty\Compile\DefaultHandlerBlockCompiler;
use Smarty\Compile\DefaultHandlerFunctionCallCompiler;
use Smarty\Compile\ModifierCompiler;
use Smarty\Compile\ObjectMethodBlockCompiler;
use Smarty\Compile\ObjectMethodCallCompiler;
use Smarty\Compile\FunctionCallCompiler;
use Smarty\Compile\PrintExpressionCompiler;
use Smarty\Lexer\TemplateLexer;
use Smarty\Parser\TemplateParser;
use Smarty\Smarty;
use Smarty\Compile\Tag\ExtendsTag;
use Smarty\CompilerException;
use Smarty\Exception;
use function array_merge;
use function is_array;
use function strlen;
use function substr;

/**
 * Class SmartyTemplateCompiler
 *


 */
class Template extends BaseCompiler {

	/**
	 * counter for prefix variable number
	 *
	 * @var int
	 */
	public static $prefixVariableNumber = 0;

	/**
	 * Parser object
	 *
	 * @var \Smarty\Parser\TemplateParser
	 */
	private $parser = null;

	/**
	 * hash for nocache sections
	 *
	 * @var mixed
	 */
	public $nocache_hash = null;

	/**
	 * suppress generation of nocache code
	 *
	 * @var bool
	 */
	public $suppressNocacheProcessing = false;

	/**
	 * caching enabled (copied from template object)
	 *
	 * @var int
	 */
	public $caching = 0;

	/**
	 * tag stack
	 *
	 * @var array
	 */
	private $_tag_stack = [];

	/**
	 * tag stack count
	 *
	 * @var array
	 */
	private $_tag_stack_count = [];

	/**
	 * current template
	 *
	 * @var \Smarty\Template
	 */
	private $template = null;

	/**
	 * merged included sub template data
	 *
	 * @var array
	 */
	public $mergedSubTemplatesData = [];

	/**
	 * merged sub template code
	 *
	 * @var array
	 */
	public $mergedSubTemplatesCode = [];

	/**
	 * source line offset for error messages
	 *
	 * @var int
	 */
	public $trace_line_offset = 0;

	/**
	 * trace uid
	 *
	 * @var string
	 */
	public $trace_uid = '';

	/**
	 * trace file path
	 *
	 * @var string
	 */
	public $trace_filepath = '';

	/**
	 * Template functions
	 *
	 * @var array
	 */
	public $tpl_function = [];

	/**
	 * compiled template or block function code
	 *
	 * @var string
	 */
	public $blockOrFunctionCode = '';

	/**
	 * flags for used modifier plugins
	 *
	 * @var array
	 */
	public $modifier_plugins = [];

	/**
	 * parent compiler object for merged subtemplates and template functions
	 *
	 * @var \Smarty\Compiler\Template
	 */
	private $parent_compiler = null;

	/**
	 * Flag true when compiling nocache section
	 *
	 * @var bool
	 */
	public $nocache = false;

	/**
	 * Flag true when tag is compiled as nocache
	 *
	 * @var bool
	 */
	public $tag_nocache = false;

	/**
	 * Compiled tag prefix code
	 *
	 * @var array
	 */
	public $prefix_code = [];

	/**
	 * Prefix code  stack
	 *
	 * @var array
	 */
	public $prefixCodeStack = [];

	/**
	 * A variable string was compiled
	 *
	 * @var bool
	 */
	public $has_variable_string = false;

	/**
	 * Stack for {setfilter} {/setfilter}
	 *
	 * @var array
	 */
	public $variable_filter_stack = [];

	/**
	 * Nesting count of looping tags like {foreach}, {for}, {section}, {while}
	 *
	 * @var int
	 */
	public $loopNesting = 0;

	/**
	 * Strip preg pattern
	 *
	 * @var string
	 */
	public $stripRegEx = '![\t ]*[\r\n]+[\t ]*!';

	/**
	 * General storage area for tag compiler plugins
	 *
	 * @var array
	 */
	public $_cache = array();

	/**
	 * Lexer preg pattern for left delimiter
	 *
	 * @var string
	 */
	private $ldelPreg = '[{]';

	/**
	 * Lexer preg pattern for right delimiter
	 *
	 * @var string
	 */
	private $rdelPreg = '[}]';

	/**
	 * Length of right delimiter
	 *
	 * @var int
	 */
	private $rdelLength = 0;

	/**
	 * Length of left delimiter
	 *
	 * @var int
	 */
	private $ldelLength = 0;

	/**
	 * Lexer preg pattern for user literals
	 *
	 * @var string
	 */
	private $literalPreg = '';

	/**
	 * array of callbacks called when the normal compile process of template is finished
	 *
	 * @var array
	 */
	public $postCompileCallbacks = [];

	/**
	 * prefix code
	 *
	 * @var string
	 */
	public $prefixCompiledCode = '';

	/**
	 * postfix code
	 *
	 * @var string
	 */
	public $postfixCompiledCode = '';
	/**
	 * @var ObjectMethodBlockCompiler
	 */
	private $objectMethodBlockCompiler;
	/**
	 * @var DefaultHandlerBlockCompiler
	 */
	private $defaultHandlerBlockCompiler;
	/**
	 * @var BlockCompiler
	 */
	private $blockCompiler;
	/**
	 * @var DefaultHandlerFunctionCallCompiler
	 */
	private $defaultHandlerFunctionCallCompiler;
	/**
	 * @var FunctionCallCompiler
	 */
	private $functionCallCompiler;
	/**
	 * @var ObjectMethodCallCompiler
	 */
	private $objectMethodCallCompiler;
	/**
	 * @var ModifierCompiler
	 */
	private $modifierCompiler;
	/**
	 * @var PrintExpressionCompiler
	 */
	private $printExpressionCompiler;

	/**
	 * Depth of nested {nocache}{/nocache} blocks. If outside, this is 0. If inside, this is 1 or higher (if nested).
	 * @var int
	 */
	private $noCacheStackDepth = 0;

	/**
	 * disabled auto-escape (when set to true, the next variable output is not auto-escaped)
	 *
	 * @var boolean
	 */
	private $raw_output = false;

	/**
	 * Initialize compiler
	 *
	 * @param Smarty $smarty global instance
	 */
	public function __construct(Smarty $smarty) {
		$this->smarty = $smarty;
		$this->nocache_hash = str_replace(
			[
				'.',
				',',
			],
			'_',
			uniqid(mt_rand(), true)
		);

		$this->modifierCompiler = new ModifierCompiler();
		$this->functionCallCompiler = new FunctionCallCompiler();
		$this->defaultHandlerFunctionCallCompiler = new DefaultHandlerFunctionCallCompiler();
		$this->blockCompiler = new BlockCompiler();
		$this->defaultHandlerBlockCompiler = new DefaultHandlerBlockCompiler();
		$this->objectMethodBlockCompiler = new ObjectMethodBlockCompiler();
		$this->objectMethodCallCompiler = new ObjectMethodCallCompiler();
		$this->printExpressionCompiler = new PrintExpressionCompiler();
	}

	/**
	 * Method to compile a Smarty template
	 *
	 * @param \Smarty\Template $template template object to compile
	 *
	 * @return string code
	 * @throws Exception
	 */
	public function compileTemplate(\Smarty\Template $template) {
		return $template->createCodeFrame(
			$this->compileTemplateSource($template),
			$this->smarty->runPostFilters($this->blockOrFunctionCode, $this->template) .
			join('', $this->mergedSubTemplatesCode),
			false,
			$this
		);
	}

	/**
	 * Compile template source and run optional post filter
	 *
	 * @param \Smarty\Template $template
	 * @param Template|null $parent_compiler
	 *
	 * @return string
	 * @throws CompilerException
	 * @throws Exception
	 */
	public function compileTemplateSource(\Smarty\Template $template, ?\Smarty\Compiler\Template $parent_compiler = null) {
		try {
			// save template object in compiler class
			$this->template = $template;
			if ($this->smarty->debugging) {
				$this->smarty->getDebug()->start_compile($this->template);
			}
			$this->parent_compiler = $parent_compiler ? $parent_compiler : $this;

			if (empty($template->getCompiled()->nocache_hash)) {
				$template->getCompiled()->nocache_hash = $this->nocache_hash;
			} else {
				$this->nocache_hash = $template->getCompiled()->nocache_hash;
			}
			$this->caching = $template->caching;

			// flag for nocache sections
			$this->nocache = false;
			$this->tag_nocache = false;
			// reset has nocache code flag
			$this->template->getCompiled()->setNocacheCode(false);

			$this->has_variable_string = false;
			$this->prefix_code = [];
			// add file dependency
			if ($this->template->getSource()->handler->checkTimestamps()) {
				$this->parent_compiler->getTemplate()->getCompiled()->file_dependency[$this->template->getSource()->uid] =
					[
						$this->template->getSource()->getResourceName(),
						$this->template->getSource()->getTimeStamp(),
						$this->template->getSource()->type,
					];
			}
			// get template source
			if (!empty($this->template->getSource()->components)) {

				$_compiled_code = '<?php $_smarty_tpl->getInheritance()->init($_smarty_tpl, true); ?>';

				$i = 0;
				$reversed_components = array_reverse($this->template->getSource()->components);
				foreach ($reversed_components as $source) {
					$i++;
					if ($i === count($reversed_components)) {
						$_compiled_code .= '<?php $_smarty_tpl->getInheritance()->endChild($_smarty_tpl); ?>';
					}
					$_compiled_code .= $this->compileTag(
						'include',
						[
							var_export($source->resource, true),
							['scope' => 'parent'],
						]
					);
				}
				$_compiled_code = $this->smarty->runPostFilters($_compiled_code, $this->template);
			} else {
				// get template source
				$_content = $this->template->getSource()->getContent();
				$_compiled_code = $this->smarty->runPostFilters(
					$this->doCompile(
						$this->smarty->runPreFilters($_content, $this->template),
						true
					),
					$this->template
				);
			}

		} catch (\Exception $e) {
			if ($this->smarty->debugging) {
				$this->smarty->getDebug()->end_compile($this->template);
			}
			$this->_tag_stack = [];
			// free memory
			$this->parent_compiler = null;
			$this->template = null;
			$this->parser = null;
			throw $e;
		}
		if ($this->smarty->debugging) {
			$this->smarty->getDebug()->end_compile($this->template);
		}
		$this->parent_compiler = null;
		$this->parser = null;
		return $_compiled_code;
	}

	/**
	 * Compile Tag
	 * This is a call back from the lexer/parser
	 *
	 * Save current prefix code
	 * Compile tag
	 * Merge tag prefix code with saved one
	 * (required nested tags in attributes)
	 *
	 * @param string $tag tag name
	 * @param array $args array with tag attributes
	 * @param array $parameter array with compilation parameter
	 *
	 * @return string compiled code
	 * @throws Exception
	 * @throws CompilerException
	 */
	public function compileTag($tag, $args, $parameter = []) {
		$this->prefixCodeStack[] = $this->prefix_code;
		$this->prefix_code = [];
		$result = $this->compileTag2($tag, $args, $parameter);
		$this->prefix_code = array_merge($this->prefix_code, array_pop($this->prefixCodeStack));
		return $result;
	}

	/**
	 * Compiles code for modifier execution
	 *
	 * @param $modifierlist
	 * @param $value
	 *
	 * @return string compiled code
	 * @throws CompilerException
	 * @throws Exception
	 */
	public function compileModifier($modifierlist, $value) {
		return $this->modifierCompiler->compile([], $this, ['modifierlist' => $modifierlist, 'value' => $value]);
	}

	/**
	 * compile variable
	 *
	 * @param string $variable
	 *
	 * @return string
	 */
	public function triggerTagNoCache($variable): void {
		if (!strpos($variable, '(')) {
			// not a variable variable
			$var = trim($variable, '\'');
			$this->tag_nocache = $this->tag_nocache |
				$this->template->getVariable(
					$var,
					true,
					false
				)->isNocache();
		}
	}

	/**
	 * compile config variable
	 *
	 * @param string $variable
	 *
	 * @return string
	 */
	public function compileConfigVariable($variable) {
		// return '$_smarty_tpl->config_vars[' . $variable . ']';
		return '$_smarty_tpl->getConfigVariable(' . $variable . ')';
	}

	/**
	 * This method is called from parser to process a text content section if strip is enabled
	 * - remove text from inheritance child templates as they may generate output
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	public function processText($text) {

		if (strpos($text, '<') === false) {
			return preg_replace($this->stripRegEx, '', $text);
		}

		$store = [];
		$_store = 0;

		// capture html elements not to be messed with
		$_offset = 0;
		if (preg_match_all(
			'#(<script[^>]*>.*?</script[^>]*>)|(<textarea[^>]*>.*?</textarea[^>]*>)|(<pre[^>]*>.*?</pre[^>]*>)#is',
			$text,
			$matches,
			PREG_OFFSET_CAPTURE | PREG_SET_ORDER
		)
		) {
			foreach ($matches as $match) {
				$store[] = $match[0][0];
				$_length = strlen($match[0][0]);
				$replace = '@!@SMARTY:' . $_store . ':SMARTY@!@';
				$text = substr_replace($text, $replace, $match[0][1] - $_offset, $_length);
				$_offset += $_length - strlen($replace);
				$_store++;
			}
		}
		$expressions = [// replace multiple spaces between tags by a single space
			'#(:SMARTY@!@|>)[\040\011]+(?=@!@SMARTY:|<)#s' => '\1 \2',
			// remove newline between tags
			'#(:SMARTY@!@|>)[\040\011]*[\n]\s*(?=@!@SMARTY:|<)#s' => '\1\2',
			// remove multiple spaces between attributes (but not in attribute values!)
			'#(([a-z0-9]\s*=\s*("[^"]*?")|(\'[^\']*?\'))|<[a-z0-9_]+)\s+([a-z/>])#is' => '\1 \5',
			'#>[\040\011]+$#Ss' => '> ',
			'#>[\040\011]*[\n]\s*$#Ss' => '>',
			$this->stripRegEx => '',
		];
		$text = preg_replace(array_keys($expressions), array_values($expressions), $text);
		$_offset = 0;
		if (preg_match_all(
			'#@!@SMARTY:([0-9]+):SMARTY@!@#is',
			$text,
			$matches,
			PREG_OFFSET_CAPTURE | PREG_SET_ORDER
		)
		) {
			foreach ($matches as $match) {
				$_length = strlen($match[0][0]);
				$replace = $store[$match[1][0]];
				$text = substr_replace($text, $replace, $match[0][1] + $_offset, $_length);
				$_offset += strlen($replace) - $_length;
				$_store++;
			}
		}
		return $text;
	}

	/**
	 * lazy loads internal compile plugin for tag compile objects cached for reuse.
	 *
	 * class name format:  \Smarty\Compile\TagName
	 *
	 * @param string $tag tag name
	 *
	 * @return ?\Smarty\Compile\CompilerInterface tag compiler object or null if not found or untrusted by security policy
	 */
	public function getTagCompiler($tag): ?\Smarty\Compile\CompilerInterface {
        $tag = strtolower($tag);

		if (isset($this->smarty->security_policy) && !$this->smarty->security_policy->isTrustedTag($tag, $this)) {
			return null;
		}

		foreach ($this->smarty->getExtensions() as $extension) {
			if ($compiler = $extension->getTagCompiler($tag)) {
				return $compiler;
			}
		}

		return null;
	}

	/**
	 * lazy loads internal compile plugin for modifier compile objects cached for reuse.
	 *
	 * @param string $modifier tag name
	 *
	 * @return bool|\Smarty\Compile\Modifier\ModifierCompilerInterface tag compiler object or false if not found or untrusted by security policy
	 */
	public function getModifierCompiler($modifier) {

		if (isset($this->smarty->security_policy) && !$this->smarty->security_policy->isTrustedModifier($modifier, $this)) {
			return false;
		}

		foreach ($this->smarty->getExtensions() as $extension) {
			if ($modifierCompiler = $extension->getModifierCompiler($modifier)) {
				return $modifierCompiler;
			}
		}

		return false;
	}

	/**
	 * Check for plugins by default plugin handler
	 *
	 * @param string $tag name of tag
	 * @param string $plugin_type type of plugin
	 *
	 * @return callback|null
	 * @throws \Smarty\CompilerException
	 */
	public function getPluginFromDefaultHandler($tag, $plugin_type) {

		$defaultPluginHandlerFunc = $this->smarty->getDefaultPluginHandlerFunc();

		if (!is_callable($defaultPluginHandlerFunc)) {
			return null;
		}


		$callback = null;
		$script = null;
		$cacheable = true;

		$result = \call_user_func_array(
			$defaultPluginHandlerFunc,
			[
				$tag,
				$plugin_type,
				null, // This used to pass $this->template, but this parameter has been removed in 5.0
				&$callback,
				&$script,
				&$cacheable,
			]
		);
		if ($result) {
			$this->tag_nocache = $this->tag_nocache || !$cacheable;
			if ($script !== null) {
				if (is_file($script)) {
					include_once $script;
				} else {
					$this->trigger_template_error("Default plugin handler: Returned script file '{$script}' for '{$tag}' not found");
				}
			}
			if (is_callable($callback)) {
				return $callback;
			} else {
				$this->trigger_template_error("Default plugin handler: Returned callback for '{$tag}' not callable");
			}
		}
		return null;
	}

	/**
	 * Append code segments and remove unneeded ?> <?php transitions
	 *
	 * @param string $left
	 * @param string $right
	 *
	 * @return string
	 */
	public function appendCode(string $left, string $right): string
	{
		if (preg_match('/\s*\?>\s?$/D', $left) && preg_match('/^<\?php\s+/', $right)) {
			$left = preg_replace('/\s*\?>\s?$/D', "\n", $left);
			$left .= preg_replace('/^<\?php\s+/', '', $right);
		} else {
			$left .= $right;
		}
		return $left;
	}

	/**
	 * Inject inline code for nocache template sections
	 * This method gets the content of each template element from the parser.
	 * If the content is compiled code, and it should be not be cached the code is injected
	 * into the rendered output.
	 *
	 * @param string $content content of template element
	 *
	 * @return string  content
	 */
	public function processNocacheCode($content) {

		// If the template is not evaluated, and we have a nocache section and/or a nocache tag
		// generate replacement code
		if (!empty($content)
			&& !($this->template->getSource()->handler->recompiled)
			&& $this->caching
			&& $this->isNocacheActive()
		) {
			$this->template->getCompiled()->setNocacheCode(true);
			$_output = addcslashes($content, '\'\\');
			$_output =
				"<?php echo '" . $this->getNocacheBlockStartMarker() . $_output . $this->getNocacheBlockEndMarker() . "';?>\n";
		} else {
			$_output = $content;
		}

		$this->modifier_plugins = [];
		$this->suppressNocacheProcessing = false;
		$this->tag_nocache = false;
		return $_output;
	}


	private function getNocacheBlockStartMarker(): string {
		return "/*%%SmartyNocache:{$this->nocache_hash}%%*/";
	}

	private function getNocacheBlockEndMarker(): string {
		return "/*/%%SmartyNocache:{$this->nocache_hash}%%*/";
	}


	/**
	 * Get Id
	 *
	 * @param string $input
	 *
	 * @return bool|string
	 */
	public function getId($input) {
		if (preg_match('~^([\'"]*)([0-9]*[a-zA-Z_]\w*)\1$~', $input, $match)) {
			return $match[2];
		}
		return false;
	}

	/**
	 * Set nocache flag in variable or create new variable
	 *
	 * @param string $varName
	 */
	public function setNocacheInVariable($varName) {
		// create nocache var to make it know for further compiling
		if ($_var = $this->getId($varName)) {
			if ($this->template->hasVariable($_var)) {
				$this->template->getVariable($_var)->setNocache(true);
			} else {
				$this->template->assign($_var, null, true);
			}
		}
	}

	/**
	 * display compiler error messages without dying
	 * If parameter $args is empty it is a parser detected syntax error.
	 * In this case the parser is called to obtain information about expected tokens.
	 * If parameter $args contains a string this is used as error message
	 *
	 * @param string $args individual error message or null
	 * @param string $line line-number
	 * @param null|bool $tagline if true the line number of last tag
	 *
	 * @throws \Smarty\CompilerException when an unexpected token is found
	 */
	public function trigger_template_error($args = null, $line = null, $tagline = null) {
		$lex = $this->parser->lex;
		if ($tagline === true) {
			// get line number of Tag
			$line = $lex->taglineno;
		} elseif (!isset($line)) {
			// get template source line which has error
			$line = $lex->line;
		} else {
			$line = (int)$line;
		}
		if (in_array(
			$this->template->getSource()->type,
			[
				'eval',
				'string',
			]
		)
		) {
			$templateName = $this->template->getSource()->type . ':' . trim(
					preg_replace(
						'![\t\r\n]+!',
						' ',
						strlen($lex->data) > 40 ?
							substr($lex->data, 0, 40) .
							'...' : $lex->data
					)
				);
		} else {
			$templateName = $this->template->getSource()->getFullResourceName();
		}
		//        $line += $this->trace_line_offset;
		$match = preg_split("/\n/", $lex->data);
		$error_text =
			'Syntax error in template "' . (empty($this->trace_filepath) ? $templateName : $this->trace_filepath) .
			'"  on line ' . ($line + $this->trace_line_offset) . ' "' .
			trim(preg_replace('![\t\r\n]+!', ' ', $match[$line - 1])) . '" ';
		if (isset($args)) {
			// individual error message
			$error_text .= $args;
		} else {
			$expect = [];
			// expected token from parser
			$error_text .= ' - Unexpected "' . $lex->value . '"';
			if (count($this->parser->yy_get_expected_tokens($this->parser->yymajor)) <= 4) {
				foreach ($this->parser->yy_get_expected_tokens($this->parser->yymajor) as $token) {
					$exp_token = $this->parser->yyTokenName[$token];
					if (isset($lex->smarty_token_names[$exp_token])) {
						// token type from lexer
						$expect[] = '"' . $lex->smarty_token_names[$exp_token] . '"';
					} else {
						// otherwise internal token name
						$expect[] = $this->parser->yyTokenName[$token];
					}
				}
				$error_text .= ', expected one of: ' . implode(' , ', $expect);
			}
		}
		if ($this->smarty->_parserdebug) {
			$this->parser->errorRunDown();
			echo ob_get_clean();
			flush();
		}
		$e = new CompilerException(
			$error_text,
			0,
			$this->template->getSource()->getFilepath() ?? $this->template->getSource()->getFullResourceName(),
			$line
		);
		$e->source = trim(preg_replace('![\t\r\n]+!', ' ', $match[$line - 1]));
		$e->desc = $args;
		$e->template = $this->template->getSource()->getFullResourceName();
		throw $e;
	}

	/**
	 * Return var_export() value with all white spaces removed
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */
	public function getVarExport($value) {
		return preg_replace('/\s/', '', var_export($value, true));
	}

	/**
	 *  enter double quoted string
	 *  - save tag stack count
	 */
	public function enterDoubleQuote() {
		array_push($this->_tag_stack_count, $this->getTagStackCount());
	}

	/**
	 * Return tag stack count
	 *
	 * @return int
	 */
	public function getTagStackCount() {
		return count($this->_tag_stack);
	}

	/**
	 * @param $lexerPreg
	 *
	 * @return mixed
	 */
	public function replaceDelimiter($lexerPreg) {
		return str_replace(
			['SMARTYldel', 'SMARTYliteral', 'SMARTYrdel', 'SMARTYautoliteral', 'SMARTYal'],
			[
				$this->ldelPreg, $this->literalPreg, $this->rdelPreg,
				$this->smarty->getAutoLiteral() ? '{1,}' : '{9}',
				$this->smarty->getAutoLiteral() ? '' : '\\s*',
			],
			$lexerPreg
		);
	}

	/**
	 * Build lexer regular expressions for left and right delimiter and user defined literals
	 */
	public function initDelimiterPreg() {
		$ldel = $this->smarty->getLeftDelimiter();
		$this->ldelLength = strlen($ldel);
		$this->ldelPreg = '';
		foreach (str_split($ldel, 1) as $chr) {
			$this->ldelPreg .= '[' . preg_quote($chr, '/') . ']';
		}
		$rdel = $this->smarty->getRightDelimiter();
		$this->rdelLength = strlen($rdel);
		$this->rdelPreg = '';
		foreach (str_split($rdel, 1) as $chr) {
			$this->rdelPreg .= '[' . preg_quote($chr, '/') . ']';
		}
		$literals = $this->smarty->getLiterals();
		if (!empty($literals)) {
			foreach ($literals as $key => $literal) {
				$literalPreg = '';
				foreach (str_split($literal, 1) as $chr) {
					$literalPreg .= '[' . preg_quote($chr, '/') . ']';
				}
				$literals[$key] = $literalPreg;
			}
			$this->literalPreg = '|' . implode('|', $literals);
		} else {
			$this->literalPreg = '';
		}
	}

	/**
	 *  leave double quoted string
	 *  - throw exception if block in string was not closed
	 *
	 * @throws \Smarty\CompilerException
	 */
	public function leaveDoubleQuote() {
		if (array_pop($this->_tag_stack_count) !== $this->getTagStackCount()) {
			$tag = $this->getOpenBlockTag();
			$this->trigger_template_error(
				"unclosed '{{$tag}}' in doubled quoted string",
				null,
				true
			);
		}
	}

	/**
	 * Get left delimiter preg
	 *
	 * @return string
	 */
	public function getLdelPreg() {
		return $this->ldelPreg;
	}

	/**
	 * Get right delimiter preg
	 *
	 * @return string
	 */
	public function getRdelPreg() {
		return $this->rdelPreg;
	}

	/**
	 * Get length of left delimiter
	 *
	 * @return int
	 */
	public function getLdelLength() {
		return $this->ldelLength;
	}

	/**
	 * Get length of right delimiter
	 *
	 * @return int
	 */
	public function getRdelLength() {
		return $this->rdelLength;
	}

	/**
	 * Get name of current open block tag
	 *
	 * @return string|boolean
	 */
	public function getOpenBlockTag() {
		$tagCount = $this->getTagStackCount();
		if ($tagCount) {
			return $this->_tag_stack[$tagCount - 1][0];
		} else {
			return false;
		}
	}

	/**
	 * Check if $value contains variable elements
	 *
	 * @param mixed $value
	 *
	 * @return bool|int
	 */
	public function isVariable($value) {
		if (is_string($value)) {
			return preg_match('/[$(]/', $value);
		}
		if (is_bool($value) || is_numeric($value)) {
			return false;
		}
		if (is_array($value)) {
			foreach ($value as $k => $v) {
				if ($this->isVariable($k) || $this->isVariable($v)) {
					return true;
				}
			}
			return false;
		}
		return false;
	}

	/**
	 * Get new prefix variable name
	 *
	 * @return string
	 */
	public function getNewPrefixVariable() {
		++self::$prefixVariableNumber;
		return $this->getPrefixVariable();
	}

	/**
	 * Get current prefix variable name
	 *
	 * @return string
	 */
	public function getPrefixVariable() {
		return '$_prefixVariable' . self::$prefixVariableNumber;
	}

	/**
	 * append  code to prefix buffer
	 *
	 * @param string $code
	 */
	public function appendPrefixCode($code) {
		$this->prefix_code[] = $code;
	}

	/**
	 * get prefix code string
	 *
	 * @return string
	 */
	public function getPrefixCode() {
		$code = '';
		$prefixArray = array_merge($this->prefix_code, array_pop($this->prefixCodeStack));
		$this->prefixCodeStack[] = [];
		foreach ($prefixArray as $c) {
			$code = $this->appendCode($code, (string) $c);
		}
		$this->prefix_code = [];
		return $code;
	}

	public function cStyleComment($string) {
		return '/*' . str_replace('*/', '* /', $string) . '*/';
	}

	public function compileChildBlock() {
		return $this->blockCompiler->compileChild($this);
	}

	public function compileParentBlock() {
		return $this->blockCompiler->compileParent($this);
	}

	/**
	 * Compile Tag
	 *
	 * @param string $tag tag name
	 * @param array $args array with tag attributes
	 * @param array $parameter array with compilation parameter
	 *
	 * @return string compiled code
	 * @throws Exception
	 * @throws CompilerException
	 */
	private function compileTag2($tag, $args, $parameter) {
		// $args contains the attributes parsed and compiled by the lexer/parser

		$this->handleNocacheFlag($args);

		// compile built-in tags
		if ($tagCompiler = $this->getTagCompiler($tag)) {
			if (!isset($this->smarty->security_policy) || $this->smarty->security_policy->isTrustedTag($tag, $this)) {
				$this->tag_nocache = $this->tag_nocache | !$tagCompiler->isCacheable();
				$_output = $tagCompiler->compile($args, $this, $parameter);
				if (!empty($parameter['modifierlist'])) {
					throw new CompilerException('No modifiers allowed on ' . $tag);
				}
				return $_output;
			}
		}

		// call to function previously defined by {function} tag
		if ($this->canCompileTemplateFunctionCall($tag)) {

			if (!empty($parameter['modifierlist'])) {
				throw new CompilerException('No modifiers allowed on ' . $tag);
			}

			$args['_attr']['name'] = "'{$tag}'";
			$tagCompiler = $this->getTagCompiler('call');
			return $tagCompiler === null ? false : $tagCompiler->compile($args, $this, $parameter);
		}

		// remaining tastes: (object-)function, (object-function-)block, custom-compiler
		// opening and closing tags for these are handled with the same handler
		$base_tag = $this->getBaseTag($tag);

		// check if tag is a registered object
		if (isset($this->smarty->registered_objects[$base_tag]) && isset($parameter['object_method'])) {
			return $this->compileRegisteredObjectMethodCall($base_tag, $args, $parameter, $tag);
		}

		// check if tag is a function
		if ($this->smarty->getFunctionHandler($tag)) {
			if (!isset($this->smarty->security_policy) || $this->smarty->security_policy->isTrustedTag($tag, $this)) {
				return (new \Smarty\Compile\PrintExpressionCompiler())->compile(
					['nofilter'], // functions are never auto-escaped
					$this,
					['value' =>	$this->compileFunctionCall($tag, $args, $parameter)]
				);
			}
		}

		// check if tag is a block
		if ($this->smarty->getBlockHandler($base_tag)) {
			if (!isset($this->smarty->security_policy) || $this->smarty->security_policy->isTrustedTag($base_tag, $this)) {
				return $this->blockCompiler->compile($args, $this, $parameter, $tag, $base_tag);
			}
		}

		// the default plugin handler is a handler of last resort, it may also handle not specifically registered tags.
		if ($callback = $this->getPluginFromDefaultHandler($tag, Smarty::PLUGIN_COMPILER)) {
			if (!empty($parameter['modifierlist'])) {
				throw new CompilerException('No modifiers allowed on ' . $tag);
			}
			$tagCompiler = new \Smarty\Compile\Tag\BCPluginWrapper($callback);
			return $tagCompiler->compile($args, $this, $parameter);
		}

		if ($this->getPluginFromDefaultHandler($base_tag, Smarty::PLUGIN_FUNCTION)) {
			return $this->defaultHandlerFunctionCallCompiler->compile($args, $this, $parameter, $tag, $tag);
		}

		if ($this->getPluginFromDefaultHandler($base_tag, Smarty::PLUGIN_BLOCK)) {
			return $this->defaultHandlerBlockCompiler->compile($args, $this, $parameter, $tag, $base_tag);
		}

		$this->trigger_template_error("unknown tag '{$tag}'", null, true);
	}

	/**
	 * Sets $this->tag_nocache if attributes contain the 'nocache' flag.
	 *
	 * @param array $attributes
	 *
	 * @return void
	 */
	private function handleNocacheFlag(array $attributes) {
		foreach ($attributes as $value) {
			if (is_string($value) && trim($value, '\'" ') == 'nocache') {
				$this->tag_nocache = true;
			}
		}
	}

	private function getBaseTag($tag) {
		if (strlen($tag) < 6 || substr($tag, -5) !== 'close') {
			return $tag;
		} else {
			return substr($tag, 0, -5);
		}
	}

	/**
	 * Compiles the output of a variable or expression.
	 *
	 * @param $value
	 * @param $attributes
	 * @param $modifiers
	 *
	 * @return string
	 * @throws Exception
	 */
	public function compilePrintExpression($value, $attributes = [], $modifiers = null) {
		$this->handleNocacheFlag($attributes);
		return $this->printExpressionCompiler->compile($attributes, $this, [
			'value'=> $value,
			'modifierlist' => $modifiers,
		]);
	}

	/**
	 * method to compile a Smarty template
	 *
	 * @param mixed $_content template source
	 * @param bool $isTemplateSource
	 *
	 * @return bool true if compiling succeeded, false if it failed
	 * @throws \Smarty\CompilerException
	 */
	protected function doCompile($_content, $isTemplateSource = false) {
		/* here is where the compiling takes place. Smarty
		  tags in the templates are replaces with PHP code,
		  then written to compiled files. */
		// init the lexer/parser to compile the template
		$this->parser = new TemplateParser(
				new TemplateLexer(
					str_replace(
						[
							"\r\n",
							"\r",
						],
						"\n",
						$_content
					),
					$this
				),
				$this
			);
		if ($isTemplateSource && $this->template->caching) {
			$this->parser->insertPhpCode("<?php\n\$_smarty_tpl->getCompiled()->nocache_hash = '{$this->nocache_hash}';\n?>\n");
		}
		if ($this->smarty->_parserdebug) {
			$this->parser->PrintTrace();
			$this->parser->lex->PrintTrace();
		}
		// get tokens from lexer and parse them
		while ($this->parser->lex->yylex()) {
			if ($this->smarty->_parserdebug) {
				echo "Line {$this->parser->lex->line} Parsing  {$this->parser->yyTokenName[$this->parser->lex->token]} Token " .
					$this->parser->lex->value;
			}
			$this->parser->doParse($this->parser->lex->token, $this->parser->lex->value);
		}
		// finish parsing process
		$this->parser->doParse(0, 0);
		// check for unclosed tags
		if ($this->getTagStackCount() > 0) {
			// get stacked info
			[$openTag, $_data] = array_pop($this->_tag_stack);
			$this->trigger_template_error(
				"unclosed " . $this->smarty->getLeftDelimiter() . $openTag .
				$this->smarty->getRightDelimiter() . " tag"
			);
		}
		// call post compile callbacks
		foreach ($this->postCompileCallbacks as $cb) {
			$callbackFunction = $cb[0];
			$parameters = $cb;
			$parameters[0] = $this;
			$callbackFunction(...$parameters);
		}
		// return compiled code
		return $this->prefixCompiledCode . $this->parser->retvalue . $this->postfixCompiledCode;
	}

	/**
	 * Register a post compile callback
	 * - when the callback is called after template compiling the compiler object will be inserted as first parameter
	 *
	 * @param callback $callback
	 * @param array $parameter optional parameter array
	 * @param string $key optional key for callback
	 * @param bool $replace if true replace existing keyed callback
	 */
	public function registerPostCompileCallback($callback, $parameter = [], $key = null, $replace = false) {
		array_unshift($parameter, $callback);
		if (isset($key)) {
			if ($replace || !isset($this->postCompileCallbacks[$key])) {
				$this->postCompileCallbacks[$key] = $parameter;
			}
		} else {
			$this->postCompileCallbacks[] = $parameter;
		}
	}

	/**
	 * Remove a post compile callback
	 *
	 * @param string $key callback key
	 */
	public function unregisterPostCompileCallback($key) {
		unset($this->postCompileCallbacks[$key]);
	}

	/**
	 * @param string $tag
	 *
	 * @return bool
	 * @throws Exception
	 */
	private function canCompileTemplateFunctionCall(string $tag): bool {
		return
			isset($this->parent_compiler->tpl_function[$tag])
			|| (
				$this->template->getSmarty()->hasRuntime('TplFunction')
				&& ($this->template->getSmarty()->getRuntime('TplFunction')->getTplFunction($this->template, $tag) !== false)
			);
	}

	/**
	 * @throws CompilerException
	 */
	private function compileRegisteredObjectMethodCall(string $base_tag, array $args, array $parameter, string $tag) {

		$method = $parameter['object_method'];
		$allowedAsBlockFunction = in_array($method, $this->smarty->registered_objects[$base_tag][3]);

		if ($base_tag === $tag) {
			// opening tag

			$allowedAsNormalFunction = empty($this->smarty->registered_objects[$base_tag][1])
				|| in_array($method, $this->smarty->registered_objects[$base_tag][1]);

			if ($allowedAsBlockFunction) {
				return $this->objectMethodBlockCompiler->compile($args, $this, $parameter, $tag, $method);
			} elseif ($allowedAsNormalFunction) {
				return $this->objectMethodCallCompiler->compile($args, $this, $parameter, $tag, $method);
			}

			$this->trigger_template_error(
				'not allowed method "' . $method . '" in registered object "' .
				$tag . '"',
				null,
				true
			);
		}

		// closing tag
		if ($allowedAsBlockFunction) {
			return $this->objectMethodBlockCompiler->compile($args, $this, $parameter, $tag, $method);
		}

		$this->trigger_template_error(
			'not allowed closing tag method "' . $method .
			'" in registered object "' . $base_tag . '"',
			null,
			true
		);
	}

	public function compileFunctionCall(string $base_tag, array $args, array $parameter = []) {
		return $this->functionCallCompiler->compile($args, $this, $parameter, $base_tag, $base_tag);
	}

	public function compileModifierInExpression(string $function, array $_attr) {
		$value = array_shift($_attr);
		return $this->compileModifier([array_merge([$function], $_attr)], $value);
	}

	/**
	 * @return TemplateParser|null
	 */
	public function getParser(): ?TemplateParser {
		return $this->parser;
	}

	/**
	 * @param TemplateParser|null $parser
	 */
	public function setParser(?TemplateParser $parser): void {
		$this->parser = $parser;
	}

	/**
	 * @return \Smarty\Template|null
	 */
	public function getTemplate(): ?\Smarty\Template {
		return $this->template;
	}

	/**
	 * @param \Smarty\Template|null $template
	 */
	public function setTemplate(?\Smarty\Template $template): void {
		$this->template = $template;
	}

	/**
	 * @return Template|null
	 */
	public function getParentCompiler(): ?Template {
		return $this->parent_compiler;
	}

	/**
	 * @param Template|null $parent_compiler
	 */
	public function setParentCompiler(?Template $parent_compiler): void {
		$this->parent_compiler = $parent_compiler;
	}


	/**
	 * Push opening tag name on stack
	 * Optionally additional data can be saved on stack
	 *
	 * @param string $openTag the opening tag's name
	 * @param mixed $data optional data saved
	 */
	public function openTag($openTag, $data = null) {
		$this->_tag_stack[] = [$openTag, $data];
		if ($openTag == 'nocache') {
			$this->noCacheStackDepth++;
		}
	}

	/**
	 * Pop closing tag
	 * Raise an error if this stack-top doesn't match with expected opening tags
	 *
	 * @param array|string $expectedTag the expected opening tag names
	 *
	 * @return mixed        any type the opening tag's name or saved data
	 * @throws CompilerException
	 */
	public function closeTag($expectedTag) {
		if ($this->getTagStackCount() > 0) {
			// get stacked info
			[$_openTag, $_data] = array_pop($this->_tag_stack);
			// open tag must match with the expected ones
			if (in_array($_openTag, (array)$expectedTag)) {

				if ($_openTag == 'nocache') {
					$this->noCacheStackDepth--;
				}

				if (is_null($_data)) {
					// return opening tag
					return $_openTag;
				} else {
					// return restored data
					return $_data;
				}
			}
			// wrong nesting of tags
			$this->trigger_template_error("unclosed '" . $this->getTemplate()->getLeftDelimiter() . "{$_openTag}" .
				$this->getTemplate()->getRightDelimiter() . "' tag");
			return;
		}
		// wrong nesting of tags
		$this->trigger_template_error('unexpected closing tag', null, true);
	}

	/**
	 * Returns true if we are in a {nocache}...{/nocache} block, but false if inside {block} tag inside a {nocache} block...
	 * @return bool
	 */
	public function isNocacheActive(): bool {
		return !$this->suppressNocacheProcessing && ($this->noCacheStackDepth > 0 || $this->tag_nocache);
	}

	/**
	 * Returns the full tag stack, used in the compiler for {break}
	 * @return array
	 */
	public function getTagStack(): array {
		return $this->_tag_stack;
	}

	/**
	 * Should the next variable output be raw (true) or auto-escaped (false)
	 * @return bool
	 */
	public function isRawOutput(): bool {
		return $this->raw_output;
	}

	/**
	 * Should the next variable output be raw (true) or auto-escaped (false)
	 * @param bool $raw_output
	 * @return void
	 */
	public function setRawOutput(bool $raw_output): void {
		$this->raw_output = $raw_output;
	}
}
