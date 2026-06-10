<?php
/**
 * Smarty Internal Plugin Resource Stream
 * Implements the streams as resource for Smarty template
 *


 * @author     Uwe Tews
 * @author     Rodney Rehm
 */

namespace Smarty\Resource;

use Smarty\Smarty;
use Smarty\Template;
use Smarty\Template\Source;

/**
 * Smarty Internal Plugin Resource Stream
 * Implements the streams as resource for Smarty template
 *
 * @link       https://php.net/streams


 */
class StreamPlugin extends RecompiledPlugin {

	/**
	 * populate Source Object with meta data from Resource
	 *
	 * @param Source $source source object
	 * @param Template $_template template object
	 *
	 * @return void
	 */
	public function populate(Source $source, ?Template $_template = null) {
		$source->uid = false;
		$source->content = $this->getContent($source);
		$source->timestamp = $source->exists = !!$source->content;
	}

	/**
	 * Load template's source from stream into current template object
	 *
	 * @param Source $source source object
	 *
	 * @return string template source
	 */
	public function getContent(Source $source) {

		if (strpos($source->getResourceName(), '://') !== false) {
			$filepath = $source->getResourceName();
		} else {
			$filepath = str_replace(':', '://', $source->getFullResourceName());
		}

		$t = '';
		// the availability of the stream has already been checked in Smarty\Resource\Base::fetch()
		$fp = fopen($filepath, 'r+');
		if ($fp) {
			while (!feof($fp) && ($current_line = fgets($fp)) !== false) {
				$t .= $current_line;
			}
			fclose($fp);
			return $t;
		} else {
			return false;
		}
	}

}
