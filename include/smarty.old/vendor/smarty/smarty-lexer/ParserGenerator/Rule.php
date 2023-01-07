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
 * @version    CVS: $Id: Rule.php,v 1.1 2006/07/18 00:53:10 cellog Exp $
 * @since      File available since Release 0.1.0
 */
/**
 * Each production rule in the grammar is stored in this class
 *
 * @package    PHP_ParserGenerator
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2006 Gregory Beaver
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    0.1.5
 * @since      Class available since Release 0.1.0
 */
class PHP_ParserGenerator_Rule
{
    /**
     * Left-hand side of the rule
     * @var array an array of {@link PHP_ParserGenerator_Symbol} objects
     */
    public $lhs;
    /**
     * Alias for the LHS (NULL if none)
     *
     * @var array
     */
    public $lhsalias = array();
    /**
     * Line number for the rule
     * @var int
     */
    public $ruleline;
    /**
     * Number of right-hand side symbols
     */
    public $nrhs;
    /**
     * The right-hand side symbols
     * @var array an array of {@link PHP_ParserGenerator_Symbol} objects
     */
    public $rhs;
    /**
     * Aliases for each right-hand side symbol, or null if no alis.
     *
     * In this rule:
     * <pre>
     * foo ::= BAR(A) baz(B).
     * </pre>
     *
     * The right-hand side aliases are A for BAR, and B for baz.
     * @var array aliases are indexed by the right-hand side symbol index.
     */
    public $rhsalias = array();
    /**
     * Line number at which code begins
     * @var int
     */
    public $line;
    /**
     * The code executed when this rule is reduced
     *
     * <pre>
     * foo(R) ::= BAR(A) baz(B). {R = A + B;}
     * </pre>
     *
     * In the rule above, the code is "R = A + B;"
     * @var string|0
     */
    public $code;
    /**
     * Precedence symbol for this rule
     * @var PHP_ParserGenerator_Symbol
     */
    public $precsym;
    /**
     * An index number for this rule
     *
     * Used in both naming of reduce functions and determining which rule code
     * to use for reduce actions
     * @var int
     */
    public $index;
    /**
     * True if this rule is ever reduced
     * @var boolean
     */
    public $canReduce;
    /**
     * Next rule with the same left-hand side
     * @var PHP_ParserGenerator_Rule|0
     */
    public $nextlhs;
    /**
     * Next rule in the global list
     * @var PHP_ParserGenerator_Rule|0
     */
    public $next;
}
