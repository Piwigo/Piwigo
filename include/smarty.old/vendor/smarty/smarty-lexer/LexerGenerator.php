<?php
/**
 * PHP_LexerGenerator, a php 5 lexer generator.
 *
 * This lexer generator translates a file in a format similar to
 * re2c ({@link http://re2c.org}) and translates it into a PHP 5-based lexer
 *
 * PHP version 5
 *
 * LICENSE:
 *
 * Copyright (c) 2006, Gregory Beaver <cellog@php.net>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in
 *       the documentation and/or other materials provided with the distribution.
 *     * Neither the name of the PHP_LexerGenerator nor the names of its
 *       contributors may be used to endorse or promote products derived
 *       from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   php
 * @package    PHP_LexerGenerator
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2006 Gregory Beaver
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    CVS: $Id: LexerGenerator.php 294970 2010-02-12 03:46:38Z clockwerx $
 * @since      File available since Release 0.1.0
 */
/**
 * The Lexer generation parser
 */
require_once './LexerGenerator/Parser.php';
/**
 * Hand-written lexer for lex2php format files
 */
require_once './LexerGenerator/Lexer.php';

/**
 * The basic home class for the lexer generator.  A lexer scans text and
 * organizes it into tokens for usage by a parser.
 *
 * Sample Usage:
 * <code>
 * require_once 'PHP/LexerGenerator.php';
 * $lex = new PHP_LexerGenerator('/path/to/lexerfile.plex');
 * </code>
 *
 * A file named "/path/to/lexerfile.php" will be created.
 *
 * File format consists of a PHP file containing specially
 * formatted comments like so:
 *
 * <code>
 * /*!lex2php
 * {@*}
 * </code>
 *
 * All lexer definition files must contain at least two lex2php comment blocks:
 *  - 1 regex declaration block
 *  - 1 or more rule declaration blocks
 *
 * The first lex2php comment is the regex declaration block and must contain
 * several processor instruction as well as defining a name for all
 * regular expressions.  Processor instructions start with
 * a "%" symbol and must be:
 *
 *  - %counter
 *  - %input
 *  - %token
 *  - %value
 *  - %line
 *
 * token and counter should define the class variables used to define lexer input
 * and the index into the input.  token and value should be used to define the class
 * variables used to store the token number and its textual value.  Finally, line
 * should be used to define the class variable used to define the current line number
 * of scanning.
 *
 * For example:
 * <code>
 * /*!lex2php
 * %counter {$this->N}
 * %input {$this->data}
 * %token {$this->token}
 * %value {$this->value}
 * %line {%this->linenumber}
 * {@*}
 * </code>
 *
 * Patterns consist of an identifier containing an letters or an underscore, and
 * a descriptive match pattern.
 *
 * Descriptive match patterns may either be regular expressions (regexes) or
 * quoted literal strings.  Here are some examples:
 *
 * <pre>
 * pattern = "quoted literal"
 * ANOTHER = /[a-zA-Z_]+/
 * COMPLEX = @<([a-zA-Z_]+)( +(([a-zA-Z_]+)=((["\'])([^\6]*)\6))+){0,1}>[^<]*</\1>@
 * </pre>
 *
 * Quoted strings must escape the \ and " characters with \" and \\.
 *
 * Regex patterns must be in Perl-compatible regular expression format (preg).
 * special characters (like \t \n or \x3H) can only be used in regexes, all
 * \ will be escaped in literal strings.
 *
 * Sub-patterns may be defined and back-references (like \1) may be used.  Any sub-
 * patterns detected will be passed to the token handler in the variable
 * $yysubmatches.
 *
 * In addition, lookahead expressions, and once-only expressions are allowed.
 * Lookbehind expressions are impossible (scanning always occurs from the
 * current position forward), and recursion (?R) can't work and is not allowed.
 *
 * <code>
 * /*!lex2php
 * %counter {$this->N}
 * %input {$this->data}
 * %token {$this->token}
 * %value {$this->value}
 * %line {%this->linenumber}
 * alpha = /[a-zA-Z]/
 * alphaplus = /[a-zA-Z]+/
 * number = /[0-9]/
 * numerals = /[0-9]+/
 * whitespace = /[ \t\n]+/
 * blah = "$\""
 * blahblah = /a\$/
 * GAMEEND = @(?:1\-0|0\-1|1/2\-1/2)@
 * PAWNMOVE = /P?[a-h]([2-7]|[18]\=(Q|R|B|N))|P?[a-h]x[a-h]([2-7]|[18]\=(Q|R|B|N))/
 * {@*}
 * </code>
 *
 * All regexes must be delimited.  Any legal preg delimiter can be used (as in @ or / in
 * the example above)
 *
 * Rule lex2php blocks each define a lexer state.  You can optionally name the state
 * with the %statename processor instruction.  State names can be used to transfer to
 * a new lexer state with the yybegin() method
 *
 * <code>
 * /*!lexphp
 * %statename INITIAL
 * blah {
 *     $this->yybegin(self::INBLAH);
 *     // note - $this->yybegin(2) would also work
 * }
 * {@*}
 * /*!lex2php
 * %statename INBLAH
 * ANYTHING {
 *     $this->yybegin(self::INITIAL);
 *     // note - $this->yybegin(1) would also work
 * }
 * {@*}
 * </code>
 *
 * You can maintain a parser state stack simply by using yypushstate() and
 * yypopstate() instead of yybegin():
 *
 * <code>
 * /*!lexphp
 * %statename INITIAL
 * blah {
 *     $this->yypushstate(self::INBLAH);
 * }
 * {@*}
 * /*!lex2php
 * %statename INBLAH
 * ANYTHING {
 *     $this->yypopstate();
 *     // now INBLAH doesn't care where it was called from
 * }
 * {@*}
 * </code>
 *
 * Code blocks can choose to skip the current token and cycle to the next token by
 * returning "false"
 *
 * <code>
 * /*!lex2php
 * WHITESPACE {
 *     return false;
 * }
 * {@*}
 * </code>
 *
 * If you wish to re-process the current token in a new state, simply return true.
 * If you forget to change lexer state, this will cause an unterminated loop,
 * so be careful!
 *
 * <code>
 * /*!lex2php
 * "(" {
 *     $this->yypushstate(self::INPARAMS);
 *     return true;
 * }
 * {@*}
 * </code>
 *
 * Lastly, if you wish to cycle to the next matching rule, return any value other than
 * true, false or null:
 *
 * <code>
 * /*!lex2php
 * "{@" ALPHA {
 *     if ($this->value == '{@internal') {
 *         return 'more';
 *     }
 *     ...
 * }
 * "{@internal" {
 *     ...
 * }
 * {@*}
 * </code>
 *
 * Note that this procedure is exceptionally inefficient, and it would be far better
 * to take advantage of PHP_LexerGenerator's top-down precedence and instead code:
 *
 * <code>
 * /*!lex2php
 * "{@internal" {
 *     ...
 * }
 * "{@" ALPHA {
 *     ...
 * }
 * {@*}
 * </code>
 * @package    PHP_LexerGenerator
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2006 Gregory Beaver
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    @package_version@
 * @since      Class available since Release 0.1.0
 * @example    TestLexer.plex Example lexer source
 * @example    TestLexer.php  Example lexer generated php code
 * @example    usage.php      Example usage of PHP_LexerGenerator
 * @example    Lexer.plex     File_ChessPGN lexer source (complex)
 * @example    Lexer.php      File_ChessPGN lexer generated php code
 */

class PHP_LexerGenerator
{
    /**
     * Plex file lexer.
     * @var PHP_LexerGenerator_Lexer
     */
    private $_lex;

    /**
     * Plex file parser.
     * @var PHP_LexerGenerator_Parser
     */
    private $_parser;

    /**
     * Path to the output PHP file.
     * @var string
     */
    private $_outfile;

    /**
     * Debug flag. When set, Parser trace information is generated.
     * @var boolean
     */
    public $debug = false;

    /**
     * Create a lexer generator and optionally generate a lexer file.
     *
     * @param string Optional plex file {@see PHP_LexerGenerator::create}.
     * @param string Optional output file {@see PHP_LexerGenerator::create}.
     */
    public function __construct($lexerfile = '', $outfile = '')
    {
        if ($lexerfile) {
            $this -> create($lexerfile, $outfile);
        }
    }

    /**
     * Create a lexer file from its skeleton plex file.
     *
     * @param string Path to the plex file.
     * @param string Optional path to output file. Default is lexerfile with
     * extension of ".php".
     */
    public function create($lexerfile, $outfile = '')
    {
        $this->_lex = new PHP_LexerGenerator_Lexer(file_get_contents($lexerfile));
        $info = pathinfo($lexerfile);
        if ($outfile) {
            $this->outfile = $outfile;
        } else {
            $this->outfile = $info['dirname'] . DIRECTORY_SEPARATOR .
                substr($info['basename'], 0,
                strlen($info['basename']) - strlen($info['extension'])) . 'php';
        }
        $this->_parser = new PHP_LexerGenerator_Parser($this->outfile, $this->_lex);
        if ($this -> debug) {
            $this->_parser->PrintTrace();
        }
        while ($this->_lex->advance($this->_parser)) {
            $this->_parser->doParse($this->_lex->token, $this->_lex->value);
        }
        $this->_parser->doParse(0, 0);
    }
}
//$a = new PHP_LexerGenerator('/development/File_ChessPGN/ChessPGN/Lexer.plex');
