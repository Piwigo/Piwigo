# Smarty 3 template engine
## Lexer/Parser generator repository

The "smarty/smarty-lexer" package is used to generate the lexer and parser class files.


**For Smarty versions >= 3.1.22 require "smarty/smarty-lexer": "~3.1" to get the latest version of the package**


**For Smarty versions <= 3.1.21 The "smarty/smarty-lexer" version must be identical with the target Smarty version**

Use for example 

	"require": {
	   "smarty/smarty-lexer": "3.1.18"
	}

in your composer.json file to get the generator for Smarty 3.1.18.



To generate the template lexer and parser run: `php Create_Template_Parser.php`

It will create
* `smarty_internal_templatelexer.php` from `smarty_internal_templatelexer.plex`
* `smarty_internal_templateparser.php` from `smarty_internal_templateparser.y`

To generate the config file lexer and parser run: `php Create_Config_Parser.php`

It will create
* `smarty_internal_configfilelexer.php` from `smarty_internal_configfilelexer.plex`
* `smarty_internal_configfileparser.php` from `smarty_internal_configfileparser.y`
   
If the "smarty/smarty" package was installed by composer the generated lexer and parser files will be copied
automatically into the distribution.
   