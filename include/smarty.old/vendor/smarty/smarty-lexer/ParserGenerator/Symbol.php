<?php
/**
 * PHP_ParserGenerator, a php 5 parser generator.
 *
 * This is a direct port of the Lemon parser generator, found at
 * {@link http://www.hwaci.com/sw/lemon/}
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
 *     * Neither the name of the PHP_ParserGenerator nor the names of its
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
 * @package    PHP_ParserGenerator
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2006 Gregory Beaver
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    CVS: $Id: Symbol.php,v 1.1 2006/07/18 00:53:10 cellog Exp $
 * @since      File available since Release 0.1.0
 */
/**
 * Symbols (terminals and nonterminals) of the grammar are stored in this class
 *
 * @package    PHP_ParserGenerator
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2006 Gregory Beaver
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    0.1.5
 * @since      Class available since Release 0.1.0
 */
class PHP_ParserGenerator_Symbol
{
    /**
     * Symbols that start with a capital letter like FOO.
     *
     * These are tokens directly from the lexer
     */
    const TERMINAL = 1;
    /**
     * Symbols that start with a lower-case letter like foo.
     *
     * These are grammar rules like "foo ::= BLAH."
     */
    const NONTERMINAL = 2;
    /**
     * Multiple terminal symbols.
     *
     * These are a grammar rule that consists of several terminals like
     * FOO|BAR|BAZ.  Note that non-terminals cannot be in a multi-terminal,
     * and a multi-terminal acts like a single terminal.
     *
     * "FOO|BAR FOO|BAZ" is actually two multi-terminals, FOO|BAR and FOO|BAZ.
     */
    const MULTITERMINAL = 3;

    const LEFT = 1;
    const RIGHT = 2;
    const NONE = 3;
    const UNK = 4;
    /**
     * Name of the symbol
     *
     * @var string
     */
    public $name;
    /**
     * Index of this symbol.
     *
     * This will ultimately end up representing the symbol in the generated
     * parser
     * @var int
     */
    public $index;
    /**
     * Symbol type
     *
     * One of PHP_ParserGenerator_Symbol::TERMINAL,
     * PHP_ParserGenerator_Symbol::NONTERMINAL or
     * PHP_ParserGenerator_Symbol::MULTITERMINAL
     * @var int
     */
    public $type;
    /**
     * Linked list of rules that use this symbol, if it is a non-terminal.
     * @var PHP_ParserGenerator_Rule
     */
    public $rule;
    /**
     * Fallback token in case this token doesn't parse
     * @var PHP_ParserGenerator_Symbol
     */
    public $fallback;
    /**
     * Precendence, if defined.
     *
     * -1 if no unusual precedence
     * @var int
     */
    public $prec = -1;
    /**
     * Associativity if precedence is defined.
     *
     * One of PHP_ParserGenerator_Symbol::LEFT,
     * PHP_ParserGenerator_Symbol::RIGHT, PHP_ParserGenerator_Symbol::NONE
     * or PHP_ParserGenerator_Symbol::UNK
     * @var unknown_type
     */
    public $assoc;
    /**
     * First-set for all rules of this symbol
     *
     * @var array
     */
    public $firstset;
    /**
     * True if this symbol is a non-terminal and can generate an empty
     * result.
     *
     * For instance "foo ::= ."
     * @var boolean
     */
    public $lambda;
    /**
     * Code that executes whenever this symbol is popped from the stack during
     * error processing.
     *
     * @var string|0
     */
    public $destructor = 0;
    /**
     * Line number of destructor code
     * @var int
     */
    public $destructorln;
    /**
     * Unused relic of the C version of Lemon.
     *
     * The data type of information held by this object.  Only used
     * if this is a non-terminal
     * @var string
     */
    public $datatype;
    /**
     * Unused relic of the C version of Lemon.
     *
     * The data type number.  In the parser, the value
     * stack is a union.  The .yy%d element of this
     * union is the correct data type for this object
     * @var string
     */
    public $dtnum;
    /**#@+
     * The following fields are used by MULTITERMINALs only
     */
    /**
     * Number of terminal symbols in the MULTITERMINAL
     *
     * This is of course the same as count($this->subsym)
     * @var int
     */
    public $nsubsym;
    /**
     * Array of terminal symbols in the MULTITERMINAL
     * @var array an array of {@link PHP_ParserGenerator_Symbol} objects
     */
    public $subsym = array();
    /**#@-*/
    /**
     * Singleton storage of symbols
     *
     * @var array an array of PHP_ParserGenerator_Symbol objects
     */
    private static $symbol_table = array();
    /**
     * Return a pointer to the (terminal or nonterminal) symbol "x".
     * Create a new symbol if this is the first time "x" has been seen.
     * (this is a singleton)
     * @param string
     * @return PHP_ParserGenerator_Symbol
     */
    public static function Symbol_new($x)
    {
        if (isset(self::$symbol_table[$x])) {
            return self::$symbol_table[$x];
        }
        $sp = new PHP_ParserGenerator_Symbol;
        $sp->name = $x;
        $sp->type = preg_match('/[A-Z]/', $x[0]) ? self::TERMINAL : self::NONTERMINAL;
        $sp->rule = 0;
        $sp->fallback = 0;
        $sp->prec = -1;
        $sp->assoc = self::UNK;
        $sp->firstset = array();
        $sp->lambda = false;
        $sp->destructor = 0;
        $sp->datatype = 0;
        self::$symbol_table[$sp->name] = $sp;

        return $sp;
    }

    /**
     * Return the number of unique symbols
     * @return int
     */
    public static function Symbol_count()
    {
        return count(self::$symbol_table);
    }

    public static function Symbol_arrayof()
    {
        return array_values(self::$symbol_table);
    }

    public static function Symbol_find($x)
    {
        if (isset(self::$symbol_table[$x])) {
            return self::$symbol_table[$x];
        }

        return 0;
    }

    /**
     * Sort function helper for symbols
     *
     * Symbols that begin with upper case letters (terminals or tokens)
     * must sort before symbols that begin with lower case letters
     * (non-terminals).  Other than that, the order does not matter.
     *
     * We find experimentally that leaving the symbols in their original
     * order (the order they appeared in the grammar file) gives the
     * smallest parser tables in SQLite.
     * @param PHP_ParserGenerator_Symbol
     * @param PHP_ParserGenerator_Symbol
     */
    public static function sortSymbols($a, $b)
    {
        $i1 = $a->index + 10000000*(ord($a->name[0]) > ord('Z'));
        $i2 = $b->index + 10000000*(ord($b->name[0]) > ord('Z'));

        return $i1 - $i2;
    }

    /**
     * Return true if two symbols are the same.
     */
    public static function same_symbol(PHP_ParserGenerator_Symbol $a, PHP_ParserGenerator_Symbol $b)
    {
        if ($a === $b) return 1;
        if ($a->type != self::MULTITERMINAL) return 0;
        if ($b->type != self::MULTITERMINAL) return 0;
        if ($a->nsubsym != $b->nsubsym) return 0;
        for ($i = 0; $i < $a->nsubsym; $i++) {
            if ($a->subsym[$i] != $b->subsym[$i]) return 0;
        }

        return 1;
    }
}
