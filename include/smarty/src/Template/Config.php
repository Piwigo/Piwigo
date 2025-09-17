<?php

namespace Smarty\Template;

use Smarty\Smarty;
use Smarty\Template;
use Smarty\Exception;

/**
 * Smarty Config Resource Data Object
 * Metadata Container for Config Files
 *
 * @author     Uwe Tews
 */
class Config extends Source {

	/**
	 * Flag that source is a config file
	 *
	 * @var bool
	 */
	public $isConfig = true;

	/**
	 * @var array
	 */
	static protected $_incompatible_resources = ['extends' => true];

	public function createCompiler(): \Smarty\Compiler\BaseCompiler {
		return new \Smarty\Compiler\Configfile($this->smarty);
	}

	protected static function getDefaultHandlerFunc(Smarty $smarty) {
		return $smarty->default_config_handler_func;
	}
}
