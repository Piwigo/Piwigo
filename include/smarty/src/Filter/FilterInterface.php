<?php

namespace Smarty\Filter;

interface FilterInterface {

	public function filter($code, \Smarty\Template $template);

}