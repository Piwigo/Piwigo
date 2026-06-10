<?php

namespace Smarty\Runtime;
use Smarty\Template;

/**
 * Runtime Extension Capture
 *


 * @author     Uwe Tews
 */
class CaptureRuntime {

	/**
	 * Stack of capture parameter
	 *
	 * @var array
	 */
	private $captureStack = [];

	/**
	 * Current open capture sections
	 *
	 * @var int
	 */
	private $captureCount = 0;

	/**
	 * Count stack
	 *
	 * @var int[]
	 */
	private $countStack = [];

	/**
	 * Named buffer
	 *
	 * @var string[]
	 */
	private $namedBuffer = [];

	/**
	 * Open capture section
	 *
	 * @param \Smarty\Template $_template
	 * @param string $buffer capture name
	 * @param string $assign variable name
	 * @param string $append variable name
	 */
	public function open(Template $_template, $buffer, $assign, $append) {

		$this->registerCallbacks($_template);

		$this->captureStack[] = [
			$buffer,
			$assign,
			$append,
		];
		$this->captureCount++;
		ob_start();
	}

	/**
	 * Register callbacks in template class
	 *
	 * @param \Smarty\Template $_template
	 */
	private function registerCallbacks(Template $_template) {

		foreach ($_template->startRenderCallbacks as $callback) {
			if (is_array($callback) && get_class($callback[0]) == self::class) {
				// already registered
				return;
			}
		}

		$_template->startRenderCallbacks[] = [
			$this,
			'startRender',
		];
		$_template->endRenderCallbacks[] = [
			$this,
			'endRender',
		];
		$this->startRender($_template);
	}

	/**
	 * Start render callback
	 *
	 * @param \Smarty\Template $_template
	 */
	public function startRender(Template $_template) {
		$this->countStack[] = $this->captureCount;
		$this->captureCount = 0;
	}

	/**
	 * Close capture section
	 *
	 * @param \Smarty\Template $_template
	 *
	 * @throws \Smarty\Exception
	 */
	public function close(Template $_template) {
		if ($this->captureCount) {
			[$buffer, $assign, $append] = array_pop($this->captureStack);
			$this->captureCount--;
			if (isset($assign)) {
				$_template->assign($assign, ob_get_contents());
			}
			if (isset($append)) {
				$_template->append($append, ob_get_contents());
			}
			$this->namedBuffer[$buffer] = ob_get_clean();
		} else {
			$this->error($_template);
		}
	}

	/**
	 * Error exception on not matching {capture}{/capture}
	 *
	 * @param \Smarty\Template $_template
	 *
	 * @throws \Smarty\Exception
	 */
	public function error(Template $_template) {
		throw new \Smarty\Exception("Not matching {capture}{/capture} in '{$_template->template_resource}'");
	}

	/**
	 * Return content of named capture buffer by key or as array
	 *
	 * @param \Smarty\Template $_template
	 * @param string|null $name
	 *
	 * @return string|string[]|null
	 */
	public function getBuffer(Template $_template, $name = null) {
		if (isset($name)) {
			return $this->namedBuffer[$name] ?? null;
		} else {
			return $this->namedBuffer;
		}
	}

	/**
	 * End render callback
	 *
	 * @param \Smarty\Template $_template
	 *
	 * @throws \Smarty\Exception
	 */
	public function endRender(Template $_template) {
		if ($this->captureCount) {
			$this->error($_template);
		} else {
			$this->captureCount = array_pop($this->countStack);
		}
	}
}
