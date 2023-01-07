<?php
/**
 * PHP_LexerGenerator, a php 5 lexer generator.
 *
 * This lexer generator translates a file in a format similar to
 * re2c ({@link http://re2c.org}) and translates it into a PHP 5-based lexer
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   php
 * @package    PHP_LexerGenerator
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2006 Gregory Beaver
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id: Lexer.php 246683 2007-11-22 04:43:52Z instance $
 * @since      File available since Release 0.1.0
 */
require_once './LexerGenerator/Parser.php';
/**
 * Token scanner for plex files.
 *
 * This scanner detects comments beginning with "/*!lex2php" and
 * then returns their components (processing instructions, patterns, strings
 * action code, and regexes)
 * @package    PHP_LexerGenerator
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2006 Gregory Beaver
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    @package_version@
 * @since      Class available since Release 0.1.0
 */
class PHP_LexerGenerator_Lexer
{
    private $data;
    private $N;
    private $state;
    /**
     * Current line number in input
     * @var int
     */
    public $line;
    /**
     * Number of scanning errors detected
     * @var int
     */
    public $errors = 0;
    /**
     * integer identifier of the current token
     * @var int
     */
    public $token;
    /**
     * string content of current token
     * @var string
     */
    public $value;

    const CODE = PHP_LexerGenerator_Parser::CODE;
    const COMMENTEND = PHP_LexerGenerator_Parser::COMMENTEND;
    const COMMENTSTART = PHP_LexerGenerator_Parser::COMMENTSTART;
    const PATTERN = PHP_LexerGenerator_Parser::PATTERN;
    const PHPCODE = PHP_LexerGenerator_Parser::PHPCODE;
    const PI = PHP_LexerGenerator_Parser::PI;
    const QUOTE = PHP_LexerGenerator_Parser::QUOTE;
    const SINGLEQUOTE = PHP_LexerGenerator_Parser::SINGLEQUOTE;
    const SUBPATTERN = PHP_LexerGenerator_Parser::SUBPATTERN;

    /**
     * prepare scanning
     * @param string the input
     */
    public function __construct($data)
    {
        $this->data = str_replace("\r\n", "\n", $data);
        $this->N = 0;
        $this->line = 1;
        $this->state = 'Start';
        $this->errors = 0;
    }

    /**
     * Output an error message
     * @param string
     */
    private function error($msg)
    {
        echo 'Error on line ' . $this->line . ': ' . $msg;
        $this->errors++;
    }

    /**
     * Initial scanning state lexer
     * @return boolean
     */
    private function lexStart()
    {
        if ($this->N >= strlen($this->data)) {
            return false;
        }
        $a = strpos($this->data, '/*!lex2php' . "\n", $this->N);
        if ($a === false) {
            $this->value = substr($this->data, $this->N);
            $this->N = strlen($this->data);
            $this->token = self::PHPCODE;

            return true;
        }
        if ($a > $this->N) {
            $this->value = substr($this->data, $this->N, $a - $this->N);
            $this->N = $a;
            $this->token = self::PHPCODE;

            return true;
        }
        $this->value = '/*!lex2php' . "\n";
        $this->N += 11; // strlen("/*lex2php\n")
        $this->token = self::COMMENTSTART;
        $this->state = 'Declare';

        return true;
    }

    /**
     * lexer for top-level canning state after the initial declaration comment
     * @return boolean
     */
    private function lexStartNonDeclare()
    {
        if ($this->N >= strlen($this->data)) {
            return false;
        }
        $a = strpos($this->data, '/*!lex2php' . "\n", $this->N);
        if ($a === false) {
            $this->value = substr($this->data, $this->N);
            $this->N = strlen($this->data);
            $this->token = self::PHPCODE;

            return true;
        }
        if ($a > $this->N) {
            $this->value = substr($this->data, $this->N, $a - $this->N);
            $this->N = $a;
            $this->token = self::PHPCODE;

            return true;
        }
        $this->value = '/*!lex2php' . "\n";
        $this->N += 11; // strlen("/*lex2php\n")
        $this->token = self::COMMENTSTART;
        $this->state = 'Rule';

        return true;
    }

    /**
     * lexer for declaration comment state
     * @return boolean
     */
    private function lexDeclare()
    {
        while (true) {
            $this -> skipWhitespaceEol();
            if (
                $this->N + 1 >= strlen($this->data)
                || $this->data[$this->N] != '/'
                || $this->data[$this->N + 1] != '/'
            ) {
                break;
            }
            // Skip single-line comment
            while (
                $this->N < strlen($this->data)
                && $this->data[$this->N] != "\n"
            ) {
                ++$this->N;
            }
        }
        if ($this->data[$this->N] == '*' && $this->data[$this->N + 1] == '/') {
            $this->state = 'StartNonDeclare';
            $this->value = '*/';
            $this->N += 2;
            $this->token = self::COMMENTEND;

            return true;
        }
        if (preg_match('/\G%([a-z]+)/', $this->data, $token, null, $this->N)) {
            $this->value = $token[1];
            $this->N += strlen($token[1]) + 1;
            $this->state = 'DeclarePI';
            $this->token = self::PI;

            return true;
        }
        if (preg_match('/\G[a-zA-Z_][a-zA-Z0-9_]*/', $this->data, $token, null, $this->N)) {
            $this->value = $token[0];
            $this->token = self::PATTERN;
            $this->N += strlen($token[0]);
            $this->state = 'DeclareEquals';

            return true;
        }
        $this->error('expecting declaration of sub-patterns');

        return false;
    }

    /**
     * lexer for processor instructions within declaration comment
     * @return boolean
     */
    private function lexDeclarePI()
    {
        $this -> skipWhitespace();
        if ($this->data[$this->N] == "\n") {
            $this->N++;
            $this->state = 'Declare';
            $this->line++;

            return $this->lexDeclare();
        }
        if ($this->data[$this->N] == '{') {
            return $this->lexCode();
        }
        if (!preg_match("/\G[^\n]+/", $this->data, $token, null, $this->N)) {
            $this->error('Unexpected end of file');

            return false;
        }
        $this->value = $token[0];
        $this->N += strlen($this->value);
        $this->token = self::SUBPATTERN;

        return true;
    }

    /**
     * lexer for processor instructions inside rule comments
     * @return boolean
     */
    private function lexDeclarePIRule()
    {
        $this -> skipWhitespace();
        if ($this->data[$this->N] == "\n") {
            $this->N++;
            $this->state = 'Rule';
            $this->line++;

            return $this->lexRule();
        }
        if ($this->data[$this->N] == '{') {
            return $this->lexCode();
        }
        if (!preg_match("/\G[^\n]+/", $this->data, $token, null, $this->N)) {
            $this->error('Unexpected end of file');

            return false;
        }
        $this->value = $token[0];
        $this->N += strlen($this->value);
        $this->token = self::SUBPATTERN;

        return true;
    }

    /**
     * lexer for the state representing scanning between a pattern and the "=" sign
     * @return boolean
     */
    private function lexDeclareEquals()
    {
        $this -> skipWhitespace();
        if ($this->N >= strlen($this->data)) {
            $this->error('unexpected end of input, expecting "=" for sub-pattern declaration');
        }
        if ($this->data[$this->N] != '=') {
            $this->error('expecting "=" for sub-pattern declaration');

            return false;
        }
        $this->N++;
        $this->state = 'DeclareRightside';
        $this -> skipWhitespace();
        if ($this->N >= strlen($this->data)) {
            $this->error('unexpected end of file, expecting right side of sub-pattern declaration');

            return false;
        }

        return $this->lexDeclareRightside();
    }

    /**
     * lexer for the right side of a pattern, detects quotes or regexes
     * @return boolean
     */
    private function lexDeclareRightside()
    {
        if ($this->data[$this->N] == "\n") {
            $this->state = 'lexDeclare';
            $this->N++;
            $this->line++;

            return $this->lexDeclare();
        }
        if ($this->data[$this->N] == '"') {
            return $this->lexQuote();
        }
        if ($this->data[$this->N] == '\'') {
            return $this->lexQuote('\'');
        }
        $this -> skipWhitespace();
        // match a pattern
        $test = $this->data[$this->N];
        $token = $this->N + 1;
        $a = 0;
        do {
            if ($a++) {
                $token++;
            }
            $token = strpos($this->data, $test, $token);
        } while ($token !== false && ($this->data[$token - 1] == '\\'
                 && $this->data[$token - 2] != '\\'));
        if ($token === false) {
            $this->error('Unterminated regex pattern (started with "' . $test . '"');

            return false;
        }
        if (substr_count($this->data, "\n", $this->N, $token - $this->N)) {
            $this->error('Regex pattern extends over multiple lines');

            return false;
        }
        $this->value = substr($this->data, $this->N + 1, $token - $this->N - 1);
        // unescape the regex marker
        // we will re-escape when creating the final regex
        $this->value = str_replace('\\' . $test, $test, $this->value);
        $this->N = $token + 1;
        $this->token = self::SUBPATTERN;

        return true;
    }

    /**
     * lexer for quoted literals
     * @return boolean
     */
    private function lexQuote($quote = '"')
    {
        $token = $this->N + 1;
        $a = 0;
        do {
            if ($a++) {
                $token++;
            }
            $token = strpos($this->data, $quote, $token);
        } while ($token !== false && $token < strlen($this->data) &&
                  ($this->data[$token - 1] == '\\' && $this->data[$token - 2] != '\\'));
        if ($token === false) {
            $this->error('unterminated quote');

            return false;
        }
        if (substr_count($this->data, "\n", $this->N, $token - $this->N)) {
            $this->error('quote extends over multiple lines');

            return false;
        }
        $this->value = substr($this->data, $this->N + 1, $token - $this->N - 1);
        $this->value = str_replace('\\'.$quote, $quote, $this->value);
        $this->value = str_replace('\\\\', '\\', $this->value);
        $this->N = $token + 1;
        if ($quote == '\'') {
            $this->token = self::SINGLEQUOTE;
        } else {
            $this->token = self::QUOTE;
        }

        return true;
    }

    /**
     * lexer for rules
     * @return boolean
     */
    private function lexRule()
    {
        while (
            $this->N < strlen($this->data)
            && (
                $this->data[$this->N] == ' '
                || $this->data[$this->N] == "\t"
                || $this->data[$this->N] == "\n"
            ) || (
                $this->N < strlen($this->data) - 1
                && $this->data[$this->N] == '/'
                && $this->data[$this->N + 1] == '/'
            )
        ) {
            if ($this->data[$this->N] == '/' && $this->data[$this->N + 1] == '/') {
                // Skip single line comments
                $next_newline = strpos($this->data, "\n", $this->N) + 1;
                if ($next_newline) {
                    $this->N = $next_newline;
                } else {
                    $this->N = sizeof($this->data);
                }
                $this->line++;
            } else {
                if ($this->data[$this->N] == "\n") {
                    $this->line++;
                }
                $this->N++; // skip all whitespace
            }
        }

        if ($this->N >= strlen($this->data)) {
            $this->error('unexpected end of input, expecting rule declaration');
        }
        if ($this->data[$this->N] == '*' && $this->data[$this->N + 1] == '/') {
            $this->state = 'StartNonDeclare';
            $this->value = '*/';
            $this->N += 2;
            $this->token = self::COMMENTEND;

            return true;
        }
        if ($this->data[$this->N] == '\'') {
            return $this->lexQuote('\'');
        }
        if (preg_match('/\G%([a-zA-Z_]+)/', $this->data, $token, null, $this->N)) {
            $this->value = $token[1];
            $this->N += strlen($token[1]) + 1;
            $this->state = 'DeclarePIRule';
            $this->token = self::PI;

            return true;
        }
        if ($this->data[$this->N] == "{") {
            return $this->lexCode();
        }
        if ($this->data[$this->N] == '"') {
            return $this->lexQuote();
        }
        if (preg_match('/\G[a-zA-Z_][a-zA-Z0-9_]*/', $this->data, $token, null, $this->N)) {
            $this->value = $token[0];
            $this->N += strlen($token[0]);
            $this->token = self::SUBPATTERN;

            return true;
        } else {
            $this->error('expecting token rule (quotes or sub-patterns)');

            return false;
        }
    }

    /**
     * lexer for php code blocks
     * @return boolean
     */
    private function lexCode()
    {
        $cp = $this->N + 1;
        for ($level = 1; $cp < strlen($this->data) && ($level > 1 || $this->data[$cp] != '}'); $cp++) {
            if ($this->data[$cp] == '{') {
                $level++;
            } elseif ($this->data[$cp] == '}') {
                $level--;
            } elseif ($this->data[$cp] == '/' && $this->data[$cp + 1] == '/') {
                /* Skip C++ style comments */
                $cp += 2;
                $z = strpos($this->data, "\n", $cp);
                if ($z === false) {
                    $cp = strlen($this->data);
                    break;
                }
                $cp = $z;
            } elseif ($this->data[$cp] == "'" || $this->data[$cp] == '"') {
                /* String a character literals */
                $startchar = $this->data[$cp];
                $prevc = 0;
                for ($cp++; $cp < strlen($this->data) && ($this->data[$cp] != $startchar || $prevc === '\\'); $cp++) {
                    if ($prevc === '\\') {
                        $prevc = 0;
                    } else {
                        $prevc = $this->data[$cp];
                    }
                }
            }
        }
        if ($cp >= strlen($this->data)) {
            $this->error("PHP code starting on this line is not terminated before the end of the file.");
            $this->error++;

            return false;
        } else {
            $this->value = substr($this->data, $this->N + 1, $cp - $this->N - 1);
            $this->token = self::CODE;
            $this->N = $cp + 1;

            return true;
        }
    }

    /**
     * Skip whitespace characters
     */
    private function skipWhitespace()
    {
        while (
            $this->N < strlen($this->data)
            && (
                $this->data[$this->N] == ' '
                || $this->data[$this->N] == "\t"
            )
        ) {
            $this->N++; // skip whitespace
        }
    }

    /**
     * Skip whitespace and EOL characters
     */
    private function skipWhitespaceEol()
    {
        while (
            $this->N < strlen($this->data)
            && (
                $this->data[$this->N] == ' '
                || $this->data[$this->N] == "\t"
                || $this->data[$this->N] == "\n"
            )
        ) {
            if ($this->data[$this->N] == "\n") {
                ++$this -> line;
            }
            $this->N++; // skip whitespace
        }
    }

    /**
     * Primary scanner
     *
     * In addition to lexing, this properly increments the line number of lexing.
     * This calls the proper sub-lexer based on the parser state
     * @param  unknown_type $parser
     * @return unknown
     */
    public function advance($parser)
    {
        if ($this->N >= strlen($this->data)) {
            return false;
        }
        if ($this->{'lex' . $this->state}()) {
            $this->line += substr_count($this->value, "\n");

            return true;
        }

        return false;
    }
}
