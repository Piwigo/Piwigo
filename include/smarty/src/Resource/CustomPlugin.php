<?php
/**
 * Smarty Resource Plugin
 *


 * @author     Rodney Rehm
 */

namespace Smarty\Resource;
use Smarty\Smarty;
use Smarty\Template;
use Smarty\Template\Source;
use Smarty\Exception;

/**
 * Smarty Resource Plugin
 * Wrapper Implementation for custom resource plugins
 *


 */
abstract class CustomPlugin extends BasePlugin {

	/**
	 * fetch template and its modification time from data source
	 *
	 * @param string $name template name
	 * @param string  &$source template source
	 * @param integer &$mtime template modification timestamp (epoch)
	 */
	abstract protected function fetch($name, &$source, &$mtime);

	/**
	 * Fetch template's modification timestamp from data source
	 * {@internal implementing this method is optional.
	 *  Only implement it if modification times can be accessed faster than loading the complete template source.}}
	 *
	 * @param string $name template name
	 *
	 * @return integer|boolean timestamp (epoch) the template was modified, or false if not found
	 */
	protected function fetchTimestamp($name) {
		return null;
	}

	/**
	 * populate Source Object with metadata from Resource
	 *
	 * @param Source $source source object
	 * @param Template|null $_template template object
	 */
	public function populate(Source $source, ?Template $_template = null) {
		$source->uid = sha1($source->type . ':' . $source->name);
		$mtime = $this->fetchTimestamp($source->name);
		if ($mtime !== null) {
			$source->timestamp = $mtime;
		} else {
			$this->fetch($source->name, $content, $timestamp);
			$source->timestamp = $timestamp ?? false;
			if (isset($content)) {
				$source->content = $content;
			}
		}
		$source->exists = !!$source->timestamp;
	}

	/**
	 * Load template's source into current template object
	 *
	 * @param Source $source source object
	 *
	 * @return string                 template source
	 * @throws Exception        if source cannot be loaded
	 */
	public function getContent(Source $source) {
		$this->fetch($source->name, $content, $timestamp);
		if (isset($content)) {
			return $content;
		}
		throw new Exception("Unable to read template {$source->type} '{$source->name}'");
	}

	/**
	 * Determine basename for compiled filename
	 *
	 * @param Source $source source object
	 *
	 * @return string                 resource's basename
	 */
	public function getBasename(Source $source) {
		return basename($this->generateSafeName($source->name));
	}

	/**
	 * Removes special characters from $name and limits its length to 127 characters.
	 *
	 * @param $name
	 *
	 * @return string
	 */
	private function generateSafeName($name): string {
		return substr(preg_replace('/[^A-Za-z0-9._]/', '', (string)$name), 0, 127);
	}
}
