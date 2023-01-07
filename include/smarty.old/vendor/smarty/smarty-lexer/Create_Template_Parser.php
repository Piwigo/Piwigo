<?php
ini_set('max_execution_time',300);
ini_set('xdebug.max_nesting_level',300);

$smartyPath = '../smarty/libs/sysplugins/';
$lexerPath = '../smarty/lexer/';
if (!is_dir($lexerPath)) {
    echo '<br><br>Fatal error: Missing lexer / parser definition folder \'lexer\' in distribution <br>';
    exit(1);
}

copy("{$smartyPath}smarty_internal_templatelexer.php", "{$lexerPath}smarty_internal_templatelexer.php.bak");
copy("{$smartyPath}smarty_internal_templateparser.php", "{$lexerPath}smarty_internal_templateparser.php.bak");
// Create Lexer
require_once './LexerGenerator.php';
$lex = new PHP_LexerGenerator("{$lexerPath}smarty_internal_templatelexer.plex");
unset($lex);

// Create Parser
require_once './ParserGenerator.php';
$parser = new PHP_ParserGenerator();
$parser->main("{$lexerPath}smarty_internal_templateparser.y");
unset($parser);

$content = file_get_contents("{$lexerPath}smarty_internal_templateparser.php");
$content = preg_replace(array('#/\*\s*\d+\s*\*/#', "#'lhs'#", "#'rhs'#"), array('', 0, 1), $content);
file_put_contents("{$lexerPath}smarty_internal_templateparser.php", $content);

copy("{$lexerPath}smarty_internal_templatelexer.php", "{$smartyPath}smarty_internal_templatelexer.php");
copy("{$lexerPath}smarty_internal_templateparser.php", "{$smartyPath}smarty_internal_templateparser.php");
