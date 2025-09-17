<?php
/**
 * Smarty Internal Plugin Resource File
 *


 * @author     Uwe Tews
 * @author     Rodney Rehm
 */

namespace Smarty\Resource;

use Smarty\Smarty;
use Smarty\Template;
use Smarty\Template\Source;
use Smarty\Exception;

/**
 * Smarty Internal Plugin Resource File
 * Implements the file system as resource for Smarty templates
 *


 */
class FilePlugin extends BasePlugin {

	/**
	 * populate Source Object with metadata from Resource
	 *
	 * @param Source $source source object
	 * @param Template|null $_template template object
	 *
	 * @throws Exception
	 */
	public function populate(Source $source, ?Template $_template = null) {

		$source->uid = sha1(
			$source->name . ($source->isConfig ? $source->getSmarty()->_joined_config_dir :
				$source->getSmarty()->_joined_template_dir)
		);

		if ($path = $this->getFilePath($source->name, $source->getSmarty(), $source->isConfig)) {
			if (isset($source->getSmarty()->security_policy) && is_object($source->getSmarty()->security_policy)) {
				$source->getSmarty()->security_policy->isTrustedResourceDir($path, $source->isConfig);
			}
			$source->exists = true;
			$source->timestamp = filemtime($path);
		} else {
			$source->timestamp = $source->exists = false;
		}
	}

	/**
	 * populate Source Object with timestamp and exists from Resource
	 *
	 * @param Source $source source object
	 */
	public function populateTimestamp(Source $source) {
		$path = $this->getFilePath($source->name, $source->getSmarty(), $source->isConfig);
		if (!$source->exists) {
			$source->exists = ($path !== false && is_file($path));
		}
		if ($source->exists && $path !== false) {
			$source->timestamp = filemtime($path);
		} else {
			$source->timestamp = 0;
		}
	}

	/**
	 * Load template's source from file into current template object
	 *
	 * @param Source $source source object
	 *
	 * @return string                 template source
	 * @throws Exception        if source cannot be loaded
	 */
	public function getContent(Source $source) {
		if ($source->exists) {
			return file_get_contents($this->getFilePath($source->getResourceName(), $source->getSmarty(), $source->isConfig()));
		}
		throw new Exception(
			'Unable to read ' . ($source->isConfig ? 'config' : 'template') .
			" {$source->type} '{$source->name}'"
		);
	}

	/**
	 * Determine basename for compiled filename
	 *
	 * @param Source $source source object
	 *
	 * @return string                 resource's basename
	 */
	public function getBasename(Source $source) {
		return basename($source->getResourceName());
	}

	/**
	 * build template filepath by traversing the template_dir array
	 *
	 * @param $file
	 * @param Smarty $smarty
	 * @param bool $isConfig
	 *
	 * @return string fully qualified filepath
	 */
	public function getFilePath($file, \Smarty\Smarty $smarty, bool $isConfig = false) {
		// absolute file ?
		if ($file[0] === '/' || $file[1] === ':') {
			$file = $smarty->_realpath($file, true);
			return is_file($file) ? $file : false;
		}

		// normalize DIRECTORY_SEPARATOR
		if (strpos($file, DIRECTORY_SEPARATOR === '/' ? '\\' : '/') !== false) {
			$file = str_replace(DIRECTORY_SEPARATOR === '/' ? '\\' : '/', DIRECTORY_SEPARATOR, $file);
		}
		$_directories = $smarty->getTemplateDir(null, $isConfig);
		// template_dir index?
		if ($file[0] === '[' && preg_match('#^\[([^\]]+)\](.+)$#', $file, $fileMatch)) {
			$file = $fileMatch[2];
			$_indices = explode(',', $fileMatch[1]);
			$_index_dirs = [];
			foreach ($_indices as $index) {
				$index = trim($index);
				// try string indexes
				if (isset($_directories[$index])) {
					$_index_dirs[] = $_directories[$index];
				} elseif (is_numeric($index)) {
					// try numeric index
					$index = (int)$index;
					if (isset($_directories[$index])) {
						$_index_dirs[] = $_directories[$index];
					} else {
						// try at location index
						$keys = array_keys($_directories);
						if (isset($_directories[$keys[$index]])) {
							$_index_dirs[] = $_directories[$keys[$index]];
						}
					}
				}
			}
			if (empty($_index_dirs)) {
				// index not found
				return false;
			} else {
				$_directories = $_index_dirs;
			}
		}
		// relative file name?
		foreach ($_directories as $_directory) {
			$path = $_directory . $file;
			if (is_file($path)) {
				return (strpos($path, '.' . DIRECTORY_SEPARATOR) !== false) ? $smarty->_realpath($path) : $path;
			}
		}
		if (!isset($_index_dirs)) {
			// Could be relative to cwd
			$path = $smarty->_realpath($file, true);
			if (is_file($path)) {
				return $path;
			}
		}
		return false;
	}

	/**
	 * Returns the timestamp of the resource indicated by $resourceName, or false if it doesn't exist.
	 *
	 * @param string $resourceName
	 * @param Smarty $smarty
	 * @param bool $isConfig
	 *
	 * @return false|int
	 */
	public function getResourceNameTimestamp(string $resourceName, \Smarty\Smarty $smarty, bool $isConfig = false) {
		if ($path = $this->getFilePath($resourceName, $smarty, $isConfig)) {
			return filemtime($path);
		}
		return false;
	}
}
