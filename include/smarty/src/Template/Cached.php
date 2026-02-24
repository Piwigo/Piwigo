<?php

namespace Smarty\Template;

use Smarty\Exception;
use Smarty\Template;
use Smarty\Template\Cacheresource\Base;
use Smarty\Template\Compiler\CodeFrame;

/**
 * Represents a cached version of a template or config file.
 * @author     Rodney Rehm
 */
class Cached extends GeneratedPhpFile {

	/**
	 * Cache Is Valid
	 *
	 * @var boolean
	 */
	private $valid = null;

	/**
	 * @param bool|null $valid
	 */
	public function setValid(?bool $valid): void {
		$this->valid = $valid;
	}

	/**
	 * CacheResource Handler
	 *
	 * @var \Smarty\Cacheresource\Base
	 */
	public $handler = null;

	/**
	 * Template Cache Id (\Smarty\Template::$cache_id)
	 *
	 * @var string
	 */
	public $cache_id = null;

	/**
	 * saved cache lifetime in seconds
	 *
	 * @var int
	 */
	public $cache_lifetime = 0;

	/**
	 * Id for cache locking
	 *
	 * @var string
	 */
	public $lock_id = null;

	/**
	 * flag that cache is locked by this instance
	 *
	 * @var bool
	 */
	public $is_locked = false;

	/**
	 * Source Object
	 *
	 * @var Source
	 */
	public $source = null;

	/**
	 * Nocache hash codes of processed compiled templates
	 *
	 * @var array
	 */
	public $hashes = [];

	/**
	 * Content buffer
	 *
	 * @var string
	 */
	public $content = null;

	/**
	 * create Cached Object container
	 *
	 * @param Source $source
	 * @param \Smarty\Cacheresource\Base $handler
	 * @param $compile_id
	 * @param $cache_id
	 */
	public function __construct(Source $source, \Smarty\Cacheresource\Base $handler, $compile_id, $cache_id) {
		$this->compile_id = $compile_id;
		$this->cache_id = $cache_id;
		$this->source = $source;
		$this->handler = $handler;
	}

	/**
	 * Render cache template
	 *
	 * @param \Smarty\Template $_template
	 * @param bool $no_output_filter
	 *
	 * @throws \Exception
	 */
	public function render(Template $_template, $no_output_filter = true) {

		if (!$this->isCached($_template)) {
			$this->updateCache($_template, $no_output_filter);
		} else {
			if (!$this->processed) {
				$this->process($_template);
			}
		}

		if ($_template->getSmarty()->debugging) {
			$_template->getSmarty()->getDebug()->start_cache($_template);
		}

		$this->getRenderedTemplateCode($_template, $this->unifunc);

		if ($_template->getSmarty()->debugging) {
			$_template->getSmarty()->getDebug()->end_cache($_template);
		}

	}

	/**
	 * Check if cache is valid, lock cache if required
	 *
	 * @param Template $_template
	 *
	 * @return bool flag true if cache is valid
	 * @throws Exception
	 */
	public function isCached(Template $_template) {
		if ($this->valid !== null) {
			return $this->valid;
		}
		while (true) {
			while (true) {
				if ($this->exists === false || $_template->getSmarty()->force_compile || $_template->getSmarty()->force_cache) {
					$this->valid = false;
				} else {
					$this->valid = true;
				}
				if ($this->valid && $_template->caching === \Smarty\Smarty::CACHING_LIFETIME_CURRENT
					&& $_template->cache_lifetime >= 0 && time() > ($this->timestamp + $_template->cache_lifetime)
				) {
					// lifetime expired
					$this->valid = false;
				}
				if ($this->valid && $_template->compile_check === \Smarty\Smarty::COMPILECHECK_ON
					&& $_template->getSource()->getTimeStamp() > $this->timestamp
				) {
					$this->valid = false;
				}
				if ($this->valid || !$_template->getSmarty()->cache_locking) {
					break;
				}
				if (!$this->handler->locked($_template->getSmarty(), $this)) {
					$this->handler->acquireLock($_template->getSmarty(), $this);
					break 2;
				}
				$this->handler->populate($this, $_template);
			}
			if ($this->valid) {
				if (!$_template->getSmarty()->cache_locking || $this->handler->locked($_template->getSmarty(), $this) === null) {
					// load cache file for the following checks
					if ($_template->getSmarty()->debugging) {
						$_template->getSmarty()->getDebug()->start_cache($_template);
					}
					if ($this->handler->process($_template, $this) === false) {
						$this->valid = false;
					} else {
						$this->processed = true;
					}
					if ($_template->getSmarty()->debugging) {
						$_template->getSmarty()->getDebug()->end_cache($_template);
					}
				} else {
					$this->is_locked = true;
					continue;
				}
			} else {
				return $this->valid;
			}
			if ($this->valid && $_template->caching === \Smarty\Smarty::CACHING_LIFETIME_SAVED
				&& $_template->getCached()->cache_lifetime >= 0
				&& (time() > ($_template->getCached()->timestamp + $_template->getCached()->cache_lifetime))
			) {
				$this->valid = false;
			}
			if ($_template->getSmarty()->cache_locking) {
				if (!$this->valid) {
					$this->handler->acquireLock($_template->getSmarty(), $this);
				} elseif ($this->is_locked) {
					$this->handler->releaseLock($_template->getSmarty(), $this);
				}
			}
			return $this->valid;
		}
		return $this->valid;
	}

	/**
	 * Process cached template
	 *
	 * @param Template $_template template object
	 */
	private function process(Template $_template) {
		if ($this->handler->process($_template, $this) === false) {
			$this->valid = false;
		}
		$this->processed = $this->valid;
	}

	/**
	 * Read cache content from handler
	 *
	 * @param Template $_template template object
	 *
	 * @return string|false content
	 */
	public function readCache(Template $_template) {
		if (!$_template->getSource()->handler->recompiled) {
			return $this->handler->retrieveCachedContent($_template);
		}
		return false;
	}

	/**
	 * Write this cache object to handler
	 *
	 * @param string $content content to cache
	 *
	 * @return bool success
	 */
	public function writeCache(Template $_template, $content) {
		if (!$_template->getSource()->handler->recompiled) {
			if ($this->handler->storeCachedContent($_template, $content)) {
				$this->content = null;
				$this->timestamp = time();
				$this->exists = true;
				$this->valid = true;
				$this->cache_lifetime = $_template->cache_lifetime;
				$this->processed = false;
				if ($_template->getSmarty()->cache_locking) {
					$this->handler->releaseLock($_template->getSmarty(), $this);
				}
				return true;
			}
			$this->content = null;
			$this->timestamp = false;
			$this->exists = false;
			$this->valid = false;
			$this->processed = false;
		}
		return false;
	}

	/**
	 * Cache was invalid , so render from compiled and write to cache
	 *
	 * @param Template $_template
	 * @param bool $no_output_filter
	 *
	 * @throws \Smarty\Exception
	 */
	private function updateCache(Template $_template, $no_output_filter) {

		ob_start();

		$_template->getCompiled()->render($_template);

		if ($_template->getSmarty()->debugging) {
			$_template->getSmarty()->getDebug()->start_cache($_template);
		}

		$this->removeNoCacheHash($_template, $no_output_filter);
		$this->process($_template);

		if ($_template->getSmarty()->debugging) {
			$_template->getSmarty()->getDebug()->end_cache($_template);
		}
	}

	/**
	 * Sanitize content and write it to cache resource
	 *
	 * @param Template $_template
	 * @param bool $no_output_filter
	 *
	 * @throws \Smarty\Exception
	 */
	private function removeNoCacheHash(Template $_template, $no_output_filter) {
		$php_pattern = '/(<%|%>|<\?php|<\?|\?>|<script\s+language\s*=\s*[\"\']?\s*php\s*[\"\']?\s*>)/';
		$content = ob_get_clean();
		$hash_array = $this->hashes;
		$hash_array[$_template->getCompiled()->nocache_hash] = true;
		$hash_array = array_keys($hash_array);
		$nocache_hash = '(' . implode('|', $hash_array) . ')';
		$_template->getCached()->setNocacheCode(false);
		// get text between non-cached items
		$cache_split =
			preg_split(
				"!/\*%%SmartyNocache:{$nocache_hash}%%\*\/(.+?)/\*/%%SmartyNocache:{$nocache_hash}%%\*/!s",
				$content
			);
		// get non-cached items
		preg_match_all(
			"!/\*%%SmartyNocache:{$nocache_hash}%%\*\/(.+?)/\*/%%SmartyNocache:{$nocache_hash}%%\*/!s",
			$content,
			$cache_parts
		);
		$content = '';
		// loop over items, stitch back together
		foreach ($cache_split as $curr_idx => $curr_split) {
			if (preg_match($php_pattern, $curr_split)) {
				// escape PHP tags in template content
				$php_split = preg_split(
					$php_pattern,
					$curr_split
				);
				preg_match_all(
					$php_pattern,
					$curr_split,
					$php_parts
				);
				foreach ($php_split as $idx_php => $curr_php) {
					$content .= $curr_php;
					if (isset($php_parts[0][$idx_php])) {
						$content .= "<?php echo '{$php_parts[ 1 ][ $idx_php ]}'; ?>\n";
					}
				}
			} else {
				$content .= $curr_split;
			}
			if (isset($cache_parts[0][$curr_idx])) {
				$_template->getCached()->setNocacheCode(true);
				$content .= $cache_parts[2][$curr_idx];
			}
		}
		if (
			!$no_output_filter
			&& !$_template->getCached()->getNocacheCode()
		) {
			$content = $_template->getSmarty()->runOutputFilters($content, $_template);
		}

		$codeframe = (new \Smarty\Compiler\CodeFrame($_template))->create($content, '', true);
		$this->writeCache($_template, $codeframe);
	}

	/**
	 * @return Source|null
	 */
	public function getSource(): ?Source {
		return $this->source;
	}

	/**
	 * @param Source|null $source
	 */
	public function setSource(?Source $source): void {
		$this->source = $source;
	}

	/**
	 * Returns the generated content
	 *
	 * @param Template $template
	 *
	 * @return string|null
	 * @throws \Exception
	 */
	public function getContent(Template $template) {
		ob_start();
		$this->render($template);
		return ob_get_clean();
	}

	/**
	 * This function is executed automatically when a generated file is included
	 * - Decode saved properties
	 * - Check if file is valid
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

		if (!empty($properties['file_dependency']) && ($_template->compile_check === \Smarty\Smarty::COMPILECHECK_ON)) {
			$is_valid = $this->checkFileDependencies($properties['file_dependency'], $_template);
		}

		// CACHING_LIFETIME_SAVED cache expiry has to be validated here since otherwise we'd define the unifunc
		if ($_template->caching === \Smarty\Smarty::CACHING_LIFETIME_SAVED && $properties['cache_lifetime'] >= 0
			&& (time() > ($this->timestamp + $properties['cache_lifetime']))
		) {
			$is_valid = false;
		}

		$this->cache_lifetime = $properties['cache_lifetime'];
		$this->setValid($is_valid);

		if ($is_valid) {
			$this->unifunc = $properties['unifunc'];
			$this->setNocacheCode($properties['has_nocache_code']);
			$this->file_dependency = $properties['file_dependency'];
		}
		return $is_valid && !function_exists($properties['unifunc']);
	}

}
