<?php

namespace Smarty\Template;

use Smarty\Exception;
use Smarty\Template;

/**
 * Represents a compiled version of a template or config file.
 * @author     Rodney Rehm
 */
class Compiled extends GeneratedPhpFile {

	/**
	 * nocache hash
	 *
	 * @var string|null
	 */
	public $nocache_hash = null;

	/**
	 * Included sub templates
	 * - index name
	 * - value use count
	 *
	 * @var int[]
	 */
	public $includes = [];
	/**
	 * @var bool
	 */
	private $isValid = false;

	/**
	 * get a Compiled Object of this source
	 *
	 * @param Template $_template template object
	 *
	 * @return Compiled compiled object
	 */
	public static function load($_template) {
		$compiled = new Compiled();
		if ($_template->getSource()->handler->supportsCompiledTemplates()) {
			$compiled->populateCompiledFilepath($_template);
		}
		return $compiled;
	}

	/**
	 * populate Compiled Object with compiled filepath
	 *
	 * @param Template $_template template object
	 **/
	private function populateCompiledFilepath(Template $_template) {
		$source = $_template->getSource();
		$smarty = $_template->getSmarty();
		$this->filepath = $smarty->getCompileDir();
		if (isset($_template->compile_id)) {
			$this->filepath .= preg_replace('![^\w]+!', '_', $_template->compile_id) .
				($smarty->use_sub_dirs ? DIRECTORY_SEPARATOR : '^');
		}
		// if use_sub_dirs, break file into directories
		if ($smarty->use_sub_dirs) {
			$this->filepath .= $source->uid[0] . $source->uid[1] . DIRECTORY_SEPARATOR . $source->uid[2] .
				$source->uid[3] . DIRECTORY_SEPARATOR . $source->uid[4] . $source->uid[5] .
				DIRECTORY_SEPARATOR;
		}
		$this->filepath .= $source->uid . '_';
		if ($source->isConfig) {
			$this->filepath .= (int)$smarty->config_read_hidden + (int)$smarty->config_booleanize * 2 +
				(int)$smarty->config_overwrite * 4;
		} else {
			$this->filepath .= (int)$smarty->escape_html * 2;
		}
		$this->filepath .= '.' . $source->type . '_' . $source->getBasename();

		if ($_template->caching) {
			$this->filepath .= '.cache';
		}
		$this->filepath .= '.php';
		$this->timestamp = $this->exists = is_file($this->filepath);
		if ($this->exists) {
			$this->timestamp = filemtime($this->filepath);
		}
	}

	/**
	 * render compiled template code
	 *
	 * @param Template $_template
	 *
	 * @return string
	 * @throws \Smarty\Exception
	 */
	public function render(Template $_template) {

		if ($_template->getSmarty()->debugging) {
			$_template->getSmarty()->getDebug()->start_render($_template);
		}
		if (!$this->processed) {
			$this->compileAndLoad($_template);
		}

		// @TODO Can't Cached handle this? Maybe introduce an event to decouple.
		if ($_template->caching) {
			$_template->getCached()->file_dependency =
				array_merge($_template->getCached()->file_dependency, $this->file_dependency);
		}

		$this->getRenderedTemplateCode($_template, $this->unifunc);

		// @TODO Can't Cached handle this? Maybe introduce an event to decouple and remove the $_template->caching property.
		if ($_template->caching && $this->getNocacheCode()) {
			$_template->getCached()->hashes[$this->nocache_hash] = true;
		}

		if ($_template->getSmarty()->debugging) {
			$_template->getSmarty()->getDebug()->end_render($_template);
		}
	}

	/**
	 * load compiled template or compile from source
	 *
	 * @param Template $_smarty_tpl do not change variable name, is used by compiled template
	 *
	 * @throws Exception
	 */
	private function compileAndLoad(Template $_smarty_tpl) {

		if ($_smarty_tpl->getSource()->handler->recompiled) {
			$this->recompile($_smarty_tpl);
			return;
		}

		if ($this->exists && !$_smarty_tpl->getSmarty()->force_compile
			&& !($_smarty_tpl->compile_check && $_smarty_tpl->getSource()->getTimeStamp() > $this->getTimeStamp())
		) {
			$this->loadCompiledTemplate($_smarty_tpl, false);
		}

		if (!$this->isValid) {
			$this->compileAndWrite($_smarty_tpl);
			$this->loadCompiledTemplate($_smarty_tpl);
		}

		$this->processed = true;
	}

	/**
	 * compile template from source
	 *
	 * @param Template $_smarty_tpl do not change variable name, is used by compiled template
	 *
	 * @throws Exception
	 */
	private function recompile(Template $_smarty_tpl) {
		$level = ob_get_level();
		ob_start();
		// call compiler
		try {
			eval('?>' . $this->doCompile($_smarty_tpl));
		} catch (\Exception $e) {
			while (ob_get_level() > $level) {
				ob_end_clean();
			}
			throw $e;
		}
		ob_get_clean();
		$this->timestamp = time();
		$this->exists = true;
	}

	/**
	 * compile template from source
	 *
	 * @param Template $_template
	 *
	 * @throws Exception
	 */
	public function compileAndWrite(Template $_template) {
		// compile locking
		if ($saved_timestamp = (!$_template->getSource()->handler->recompiled && is_file($this->filepath))) {
			$saved_timestamp = $this->getTimeStamp();
			touch($this->filepath);
		}
		// compile locking
		try {
			// call compiler
			$this->write($_template, $this->doCompile($_template));
		} catch (\Exception $e) {
			// restore old timestamp in case of error
			if ($saved_timestamp && is_file($this->filepath)) {
				touch($this->filepath, $saved_timestamp);
			}
			throw $e;
		}
	}

	/**
	 * Do the actual compiling.
	 *
	 * @param Template $_smarty_tpl
	 *
	 * @return string
	 * @throws Exception
	 */
	private function doCompile(Template $_smarty_tpl): string {
		$this->file_dependency = [];
		$this->includes = [];
		$this->nocache_hash = null;
		$this->unifunc = null;
		return $_smarty_tpl->getCompiler()->compileTemplate($_smarty_tpl);
	}

	/**
	 * Write compiled code by handler
	 *
	 * @param Template $_template template object
	 * @param string $code compiled code
	 *
	 * @return bool success
	 * @throws \Smarty\Exception
	 */
	private function write(Template $_template, $code) {
		if (!$_template->getSource()->handler->recompiled) {
			if ($_template->getSmarty()->writeFile($this->filepath, $code) === true) {
				$this->timestamp = $this->exists = is_file($this->filepath);
				if ($this->exists) {
					$this->timestamp = filemtime($this->filepath);
					return true;
				}
			}
			return false;
		}
		return true;
	}

	/**
	 * Load fresh compiled template by including the PHP file
	 * HHVM requires a workaround because of a PHP incompatibility
	 *
	 * @param Template $_smarty_tpl do not change/remove variable name, is used by compiled template
	 * @param bool $invalidateCachedFiles forces a revalidation of the file in opcache or apc cache (if available)
	 *
	 */
	private function loadCompiledTemplate(Template $_smarty_tpl, bool $invalidateCachedFiles = true) {
        
		if ($invalidateCachedFiles) {
			if (function_exists('opcache_invalidate')
				 && (!function_exists('ini_get') || strlen(ini_get("opcache.restrict_api")) < 1)
			) {
				opcache_invalidate($this->filepath, true);
			} elseif (function_exists('apc_compile_file')) {
				apc_compile_file($this->filepath);
			}
		}
		if (defined('HHVM_VERSION')) {
			eval('?>' . file_get_contents($this->filepath));
		} else {
			include $this->filepath;
		}

	}

	/**
	 * This function is executed automatically when a compiled or cached template file is included
	 * - Decode saved properties from compiled template and cache files
	 * - Check if compiled or cache file is valid
	 *
	 * @param Template $_template
	 * @param array $properties special template properties
	 *
	 * @return bool flag if compiled or cache file is valid
	 * @throws Exception
	 */
	public function isFresh(Template $_template, array $properties): bool {

		// on cache resources other than file check version stored in cache code
		if (\Smarty\Smarty::SMARTY_VERSION !== $properties['version']) {
			return false;
		}

		$is_valid = true;
		if (!empty($properties['file_dependency']) && $_template->compile_check) {
			$is_valid = $this->checkFileDependencies($properties['file_dependency'], $_template);
		}

		$this->isValid = $is_valid;
		$this->includes = $properties['includes'] ?? [];

		if ($is_valid) {
			$this->unifunc = $properties['unifunc'];
			$this->setNocacheCode($properties['has_nocache_code']);
			$this->file_dependency = $properties['file_dependency'];
		}
		return $is_valid && !function_exists($properties['unifunc']);
	}

	/**
	 * This method is here only to fix an issue when upgrading from Smarty v4 to v5.
	 */
	public function _decodeProperties($a, $b, $c = false): bool { return false; }

}
