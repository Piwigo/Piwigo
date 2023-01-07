<?php
require_once './ParserGenerator.php';
ini_set('max_execution_time',300);
ini_set('xdebug.max_nesting_level',300);
$me = new PHP_ParserGenerator;
$me->main();
