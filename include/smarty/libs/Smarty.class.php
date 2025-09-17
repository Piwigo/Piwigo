<?php

/////////////////////////////////////////////////////////////////////
// This is a stub PSR-4 loading script that gets all the pieces of //
// Smarty 5.x loaded without requiring the use of composer. It's   //
// not really a 'class' file, but the name is used so we're        //
// backwards compatible with previous versions of Smarty.          //
//                                                                 //
// Example:                                                        //
// require_once("/path/to/smarty/libs/Smarty.class.php");          //
//                                                                 //
// $smarty = new Smarty\Smarty;                                    //
// $smarty->testInstall();                                         //
/////////////////////////////////////////////////////////////////////

define('__SMARTY_DIR', __DIR__ . '/../src/');

// Global function declarations
require_once(__SMARTY_DIR . "/functions.php");

spl_autoload_register(function ($class) {
	// Class prefix
	$prefix = 'Smarty\\';

	// Does the class use the namespace prefix?
	$len = strlen($prefix);
	if (strncmp($prefix, $class, $len) !== 0) {
		// If not, move to the next registered autoloader
		return;
	}

	// Hack off the prefix part
	$relative_class = substr($class, $len);

	// Build a path to the include file
	$file = __SMARTY_DIR . str_replace('\\', '/', $relative_class) . '.php';

	// If the file exists, require it
	if (file_exists($file)) {
		require_once($file);
	}
});
