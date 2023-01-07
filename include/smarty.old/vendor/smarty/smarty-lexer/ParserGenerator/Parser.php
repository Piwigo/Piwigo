<?php
/**
 * PHP_ParserGenerator, a php 5 parser generator.
 *
 * This is a direct port of the Lemon parser generator, found at
 * {@link http://www.hwaci.com/sw/lemon/}
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
 * @package    PHP_ParserGenerator
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2006 Gregory Beaver
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id: Parser.php,v 1.2 2007/03/02 16:36:24 cellog Exp $
 * @since      File available since Release 0.1.0
 */
/**
 * The grammar parser for lemon grammar files.
 *
 * @package    PHP_ParserGenerator
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2006 Gregory Beaver
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @since      Class available since Release 0.1.0
 */
class PHP_ParserGenerator_Parser
{
    const INITIALIZE = 1;
    const WAITING_FOR_DECL_OR_RULE = 2;
    const WAITING_FOR_DECL_KEYWORD = 3;
    const WAITING_FOR_DECL_ARG = 4;
    const WAITING_FOR_PRECEDENCE_SYMBOL = 5;
    const WAITING_FOR_ARROW = 6;
    const IN_RHS = 7;
    const LHS_ALIAS_1 = 8;
    const LHS_ALIAS_2 = 9;
    const LHS_ALIAS_3 = 10;
    const RHS_ALIAS_1 = 11;
    const RHS_ALIAS_2 = 12;
    const PRECEDENCE_MARK_1 = 13;
    const PRECEDENCE_MARK_2 = 14;
    const RESYNC_AFTER_RULE_ERROR = 15;
    const RESYNC_AFTER_DECL_ERROR = 16;
    const WAITING_FOR_DESTRUCTOR_SYMBOL = 17;
    const WAITING_FOR_DATATYPE_SYMBOL = 18;
    const WAITING_FOR_FALLBACK_ID = 19;

    /**
     * Name of the input file
     *
     * @var string
     */
    public $filename;
    /**
     * Linenumber at which current token starts
     * @var int
     */
    public $tokenlineno;
    /**
     * Number of parsing errors so far
     * @var int
     */
    public $errorcnt;
    /**
     * Index of current token within the input string
     * @var int
     */
    public $tokenstart;
    /**
     * Global state vector
     * @var PHP_ParserGenerator_Data
     */
    public $gp;
    /**
     * Parser state (one of the class constants for this class)
     *
     * - PHP_ParserGenerator_Parser::INITIALIZE,
     * - PHP_ParserGenerator_Parser::WAITING_FOR_DECL_OR_RULE,
     * - PHP_ParserGenerator_Parser::WAITING_FOR_DECL_KEYWORD,
     * - PHP_ParserGenerator_Parser::WAITING_FOR_DECL_ARG,
     * - PHP_ParserGenerator_Parser::WAITING_FOR_PRECEDENCE_SYMBOL,
     * - PHP_ParserGenerator_Parser::WAITING_FOR_ARROW,
     * - PHP_ParserGenerator_Parser::IN_RHS,
     * - PHP_ParserGenerator_Parser::LHS_ALIAS_1,
     * - PHP_ParserGenerator_Parser::LHS_ALIAS_2,
     * - PHP_ParserGenerator_Parser::LHS_ALIAS_3,
     * - PHP_ParserGenerator_Parser::RHS_ALIAS_1,
     * - PHP_ParserGenerator_Parser::RHS_ALIAS_2,
     * - PHP_ParserGenerator_Parser::PRECEDENCE_MARK_1,
     * - PHP_ParserGenerator_Parser::PRECEDENCE_MARK_2,
     * - PHP_ParserGenerator_Parser::RESYNC_AFTER_RULE_ERROR,
     * - PHP_ParserGenerator_Parser::RESYNC_AFTER_DECL_ERROR,
     * - PHP_ParserGenerator_Parser::WAITING_FOR_DESTRUCTOR_SYMBOL,
     * - PHP_ParserGenerator_Parser::WAITING_FOR_DATATYPE_SYMBOL,
     * - PHP_ParserGenerator_Parser::WAITING_FOR_FALLBACK_ID
     * @var int
     */
    public $state;
    /**
     * The fallback token
     * @var PHP_ParserGenerator_Symbol
     */
    public $fallback;
    /**
     * Left-hand side of the current rule
     * @var PHP_ParserGenerator_Symbol
     */
    public $lhs;
    /**
     * Alias for the LHS
     * @var string
     */
    public $lhsalias;
    /**
     * Number of right-hand side symbols seen
     * @var int
     */
    public $nrhs;
    /**
     * Right-hand side symbols
     * @var array array of {@link PHP_ParserGenerator_Symbol} objects
     */
    public $rhs = array();
    /**
     * Aliases for each RHS symbol name (or NULL)
     * @var array array of strings
     */
    public $alias = array();
    /**
     * Previous rule parsed
     * @var PHP_ParserGenerator_Rule
     */
    public $prevrule;
    /**
     * Keyword of a declaration
     *
     * This is one of the %keyword keywords in the grammar file
     * @var string
     */
    public $declkeyword;
    /**
     * Where the declaration argument should be put
     *
     * This is assigned as a reference to an internal variable
     * @var mixed
     */
    public $declargslot = array();
    /**
     * Where the declaration linenumber is put
     *
     * This is assigned as a reference to an internal variable
     * @var mixed
     */
    public $decllnslot;
    /*enum e_assoc*/
    public $declassoc;    /* Assign this association to decl arguments */
    public $preccounter;           /* Assign this precedence to decl arguments */
    /**
     * @var PHP_ParserGenerator_Rule
     */
    public $firstrule;    /* Pointer to first rule in the grammar */
    /**
     * @var PHP_ParserGenerator_Rule
     */
    public $lastrule;     /* Pointer to the most recently parsed rule */

    /**
     * @var PHP_ParserGenerator
     */
    private $lemon;

    public function __construct(PHP_ParserGenerator $lem)
    {
        $this->lemon = $lem;
    }

    /**
     * Run the preprocessor over the input file text.  The Lemon variable
     * $azDefine contains the names of all defined
     * macros.  This routine looks for "%ifdef" and "%ifndef" and "%endif" and
     * comments them out.  Text in between is also commented out as appropriate.
     * @param string
     */
    private function preprocess_input(&$z)
    {
    $start = 0;
    $lineno = $exclude = 0;
        for ($i=0; $i < strlen($z); $i++) {
            if ($z[$i] == "\n") {
                $lineno++;
            }
            if ($z[$i] != '%' || ($i > 0 && $z[$i-1] != "\n")) {
                continue;
            }
            if (substr($z, $i, 6) === "%endif" && trim($z[$i+6]) === '') {
                if ($exclude) {
                    $exclude--;
                    if ($exclude === 0) {
                        for ($j = $start; $j < $i; $j++) {
                            if ($z[$j] != "\n") $z[$j] = ' ';
                        }
                    }
                }
                for ($j = $i; $j < strlen($z) && $z[$j] != "\n"; $j++) {
                    $z[$j] = ' ';
                }
            } elseif (substr($z, $i, 6) === "%ifdef" && trim($z[$i+6]) === '' ||
                      substr($z, $i, 7) === "%ifndef" && trim($z[$i+7]) === '') {
                if ($exclude) {
                    $exclude++;
                } else {
                    $j = $i;
                    $n = strtok(substr($z, $j), " \t");
                    $exclude = 1;
                    if (isset($this->lemon->azDefine[$n])) {
                        $exclude = 0;
                    }
                    if ($z[$i + 3]=='n') {
                        // this is a rather obtuse way of checking whether this is %ifndef
                        $exclude = !$exclude;
                    }
                    if ($exclude) {
                        $start = $i;
                        $start_lineno = $lineno;
                    }
                }
                //for ($j = $i; $j < strlen($z) && $z[$j] != "\n"; $j++) $z[$j] = ' ';
                $j = strpos(substr($z, $i), "\n");
                if ($j === false) {
                    $z = substr($z, 0, $i); // remove instead of adding ' '
                } else {
                    $z = substr($z, 0, $i) . substr($z, $i + $j); // remove instead of adding ' '
                }
            }
        }
        if ($exclude) {
            throw new Exception("unterminated %ifdef starting on line $start_lineno\n");
        }
    }

    /**
     * In spite of its name, this function is really a scanner.
     *
     * It reads in the entire input file (all at once) then tokenizes it.
     * Each token is passed to the function "parseonetoken" which builds all
     * the appropriate data structures in the global state vector "gp".
     * @param PHP_ParserGenerator_Data
     */
    public function Parse(PHP_ParserGenerator_Data $gp)
    {
        $startline = 0;

        $this->gp = $gp;
        $this->filename = $gp->filename;
        $this->errorcnt = 0;
        $this->state = self::INITIALIZE;

        /* Begin by reading the input file */
        $filebuf = file_get_contents($this->filename);
        if (!$filebuf) {
            PHP_ParserGenerator::ErrorMsg($this->filename, 0, "Can't open this file for reading.");
            $gp->errorcnt++;

            return;
        }
        if (filesize($this->filename) != strlen($filebuf)) {
            ErrorMsg($this->filename, 0, "Can't read in all %d bytes of this file.",
                filesize($this->filename));
            $gp->errorcnt++;

            return;
        }

        /* Make an initial pass through the file to handle %ifdef and %ifndef */
        $this->preprocess_input($filebuf);

        /* Now scan the text of the input file */
        $lineno = 1;
        for ($cp = 0, $c = $filebuf[0]; $cp < strlen($filebuf); $cp++) {
            $c = $filebuf[$cp];
            $lineno = substr_count(substr($filebuf, 0, $cp), "\n")+1;
            //if ($c == "\n") $lineno++;              /* Keep track of the line number */
            if (trim($c) === '') {
                continue;
            }  /* Skip all white space */
            if ($filebuf[$cp] == '/' && ($cp + 1 < strlen($filebuf)) && $filebuf[$cp + 1] == '/') {
                /* Skip C++ style comments */
                $cp += 2;
                $z = strpos(substr($filebuf, $cp), "\n");
                if ($z === false) {
                    $cp = strlen($filebuf);
                    break;
                }
                $lineno++;
                $cp += $z;
                continue;
            }
            if ($filebuf[$cp] == '/' && ($cp + 1 < strlen($filebuf)) && $filebuf[$cp + 1] == '*') {
                /* Skip C style comments */
                $cp += 2;
                $z = strpos(substr($filebuf, $cp), '*/');
                if ($z !== false) {
                    $lineno += count(explode("\n", substr($filebuf, $cp, $z))) - 1;
                }
                $cp += $z + 1;
                continue;
            }
            $this->tokenstart = $cp;                /* Mark the beginning of the token */
            $this->tokenlineno = $lineno;           /* Linenumber on which token begins */
            if ($filebuf[$cp] == '"') {                     /* String literals */
                $cp++;
                $oldcp = $cp;
                $test = strpos(substr($filebuf, $cp), '"');
                if ($test === false) {
                    PHP_ParserGenerator::ErrorMsg($this->filename, $startline,
                    "String starting on this line is not terminated before the end of the file.");
                    $this->errorcnt++;
                    $nextcp = $cp = strlen($filebuf);
                } else {
                    $cp += $test;
                    $nextcp = $cp + 1;
                }
                $lineno += count(explode("\n", substr($filebuf, $oldcp, $cp - $oldcp))) - 1;
            } elseif ($filebuf[$cp] == '{') {               /* A block of C code */
                $cp++;
                for ($level = 1; $cp < strlen($filebuf) && ($level > 1 || $filebuf[$cp] != '}'); $cp++) {
                    if ($filebuf[$cp] == "\n") {
                        $lineno++;
                    } elseif ($filebuf[$cp] == '{') {
                        $level++;
                    } elseif ($filebuf[$cp] == '}') {
                        $level--;
                    } elseif ($filebuf[$cp] == '/' && $filebuf[$cp + 1] == '*') {
                        /* Skip comments */
                        $cp += 2;
                        $z = strpos(substr($filebuf, $cp), '*/');
                        if ($z !== false) {
                            $lineno += count(explode("\n", substr($filebuf, $cp, $z))) - 1;
                        }
                        $cp += $z + 2;
                    } elseif ($filebuf[$cp] == '/' && $filebuf[$cp + 1] == '/') {
                        /* Skip C++ style comments too */
                        $cp += 2;
                        $z = strpos(substr($filebuf, $cp), "\n");
                        if ($z === false) {
                            $cp = strlen($filebuf);
                            break;
                        } else {
                            $lineno++;
                        }
                        $cp += $z;
                    } elseif ($filebuf[$cp] == "'" || $filebuf[$cp] == '"') {
                        /* String a character literals */
                        $startchar = $filebuf[$cp];
                        $prevc = 0;
                        for ($cp++; $cp < strlen($filebuf) && ($filebuf[$cp] != $startchar || $prevc === '\\'); $cp++) {
                            if ($filebuf[$cp] == "\n") {
                                $lineno++;
                            }
                            if ($prevc === '\\') {
                                $prevc = 0;
                            } else {
                                $prevc = $filebuf[$cp];
                            }
                        }
                    }
                }
                if ($cp >= strlen($filebuf)) {
                    PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                        "PHP code starting on this line is not terminated before the end of the file.");
                    $this->errorcnt++;
                    $nextcp = $cp;
                } else {
                    $nextcp = $cp + 1;
                }
            } elseif (preg_match('/[a-zA-Z0-9]/', $filebuf[$cp])) {
                /* Identifiers */
                preg_match('/[a-zA-Z0-9_]+/', substr($filebuf, $cp), $preg_results);
                $cp += strlen($preg_results[0]);
                $nextcp = $cp;
            } elseif ($filebuf[$cp] == ':' && $filebuf[$cp + 1] == ':' &&
                      $filebuf[$cp + 2] == '=') {
                /* The operator "::=" */
                $cp += 3;
                $nextcp = $cp;
            } elseif (($filebuf[$cp] == '/' || $filebuf[$cp] == '|') &&
                      preg_match('/[a-zA-Z]/', $filebuf[$cp + 1])) {
                $cp += 2;
                preg_match('/[a-zA-Z0-9_]+/', substr($filebuf, $cp), $preg_results);
                $cp += strlen($preg_results[0]);
                $nextcp = $cp;
            } else {
                /* All other (one character) operators */
                $cp ++;
                $nextcp = $cp;
            }
            $this->parseonetoken(substr($filebuf, $this->tokenstart,
                $cp - $this->tokenstart)); /* Parse the token */
            $cp = $nextcp - 1;
        }
        $gp->rule = $this->firstrule;
        $gp->errorcnt = $this->errorcnt;
    }

    /**
     * Parse a single token
     * @param string token
     */
    public function parseonetoken($token)
    {
        $x = $token;
        $this->a = 0; // for referencing in WAITING_FOR_DECL_KEYWORD
        if (PHP_ParserGenerator::DEBUG) {
            printf("%s:%d: Token=[%s] state=%d\n",
                $this->filename, $this->tokenlineno, $token, $this->state);
        }
        switch ($this->state) {
            case self::INITIALIZE:
                $this->prevrule = 0;
                $this->preccounter = 0;
                $this->firstrule = $this->lastrule = 0;
                $this->gp->nrule = 0;
                /* Fall thru to next case */
            case self::WAITING_FOR_DECL_OR_RULE:
                if ($x[0] == '%') {
                    $this->state = self::WAITING_FOR_DECL_KEYWORD;
                } elseif (preg_match('/[a-z]/', $x[0])) {
                    $this->lhs = PHP_ParserGenerator_Symbol::Symbol_new($x);
                    $this->nrhs = 0;
                    $this->lhsalias = 0;
                    $this->state = self::WAITING_FOR_ARROW;
                } elseif ($x[0] == '{') {
                    if ($this->prevrule === 0) {
                        PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                            "There is no prior rule opon which to attach the code
                             fragment which begins on this line.");
                        $this->errorcnt++;
                    } elseif ($this->prevrule->code != 0) {
                        PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                            "Code fragment beginning on this line is not the first \
                             to follow the previous rule.");
                        $this->errorcnt++;
                    } else {
                        $this->prevrule->line = $this->tokenlineno;
                        $this->prevrule->code = substr($x, 1);
                    }
                } elseif ($x[0] == '[') {
                    $this->state = self::PRECEDENCE_MARK_1;
                } else {
                    PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                    "Token \"%s\" should be either \"%%\" or a nonterminal name.",
                    $x);
                    $this->errorcnt++;
                }
                break;
            case self::PRECEDENCE_MARK_1:
                if (!preg_match('/[A-Z]/', $x[0])) {
                    PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                        "The precedence symbol must be a terminal.");
                    $this->errorcnt++;
                } elseif ($this->prevrule === 0) {
                    PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                        "There is no prior rule to assign precedence \"[%s]\".", $x);
                    $this->errorcnt++;
                } elseif ($this->prevrule->precsym != 0) {
                    PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                        "Precedence mark on this line is not the first to follow the previous rule.");
                    $this->errorcnt++;
                } else {
                    $this->prevrule->precsym = PHP_ParserGenerator_Symbol::Symbol_new($x);
                }
                $this->state = self::PRECEDENCE_MARK_2;
                break;
            case self::PRECEDENCE_MARK_2:
                if ($x[0] != ']') {
                    PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                        "Missing \"]\" on precedence mark.");
                    $this->errorcnt++;
                }
                $this->state = self::WAITING_FOR_DECL_OR_RULE;
                break;
            case self::WAITING_FOR_ARROW:
                if ($x[0] == ':' && $x[1] == ':' && $x[2] == '=') {
                    $this->state = self::IN_RHS;
                } elseif ($x[0] == '(') {
                    $this->state = self::LHS_ALIAS_1;
                } else {
                    PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                        "Expected to see a \":\" following the LHS symbol \"%s\".",
                    $this->lhs->name);
                    $this->errorcnt++;
                    $this->state = self::RESYNC_AFTER_RULE_ERROR;
                }
                break;
            case self::LHS_ALIAS_1:
                if (preg_match('/[A-Za-z]/', $x[0])) {
                    $this->lhsalias = $x;
                    $this->state = self::LHS_ALIAS_2;
                } else {
                    PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                        "\"%s\" is not a valid alias for the LHS \"%s\"\n",
                        $x, $this->lhs->name);
                    $this->errorcnt++;
                    $this->state = self::RESYNC_AFTER_RULE_ERROR;
                }
                break;
            case self::LHS_ALIAS_2:
                if ($x[0] == ')') {
                    $this->state = self::LHS_ALIAS_3;
                } else {
                    PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                        "Missing \")\" following LHS alias name \"%s\".",$this->lhsalias);
                    $this->errorcnt++;
                    $this->state = self::RESYNC_AFTER_RULE_ERROR;
                }
                break;
            case self::LHS_ALIAS_3:
                if ($x == '::=') {
                    $this->state = self::IN_RHS;
                } else {
                    PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                        "Missing \"->\" following: \"%s(%s)\".",
                    $this->lhs->name, $this->lhsalias);
                    $this->errorcnt++;
                    $this->state = self::RESYNC_AFTER_RULE_ERROR;
                }
                break;
            case self::IN_RHS:
                if ($x[0] == '.') {
                    $rp = new PHP_ParserGenerator_Rule;
                    $rp->ruleline = $this->tokenlineno;
                    for ($i = 0; $i < $this->nrhs; $i++) {
                        $rp->rhs[$i] = $this->rhs[$i];
                        $rp->rhsalias[$i] = $this->alias[$i];
                    }
                    if (count(array_unique($rp->rhsalias)) != count($rp->rhsalias)) {
                        $used = array();
                        foreach ($rp->rhsalias as $i => $symbol) {
                            if (!is_string($symbol)) {
                                continue;
                            }
                            if (isset($used[$symbol])) {
                                PHP_ParserGenerator::ErrorMsg($this->filename,
                                    $this->tokenlineno,
                                    "RHS symbol \"%s\" used multiple times.",
                                    $symbol);
                                $this->errorcnt++;
                            } else {
                                $used[$symbol] = $i;
                            }
                        }
                    }
                    $rp->lhs = $this->lhs;
                    $rp->lhsalias = $this->lhsalias;
                    $rp->nrhs = $this->nrhs;
                    $rp->code = 0;
                    $rp->precsym = 0;
                    $rp->index = $this->gp->nrule++;
                    $rp->nextlhs = $rp->lhs->rule;
                    $rp->lhs->rule = $rp;
                    $rp->next = 0;
                    if ($this->firstrule === 0) {
                        $this->firstrule = $this->lastrule = $rp;
                    } else {
                        $this->lastrule->next = $rp;
                        $this->lastrule = $rp;
                    }
                    $this->prevrule = $rp;
                    $this->state = self::WAITING_FOR_DECL_OR_RULE;
                } elseif (preg_match('/[a-zA-Z]/', $x[0])) {
                    if ($this->nrhs >= PHP_ParserGenerator::MAXRHS) {
                        PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                            "Too many symbols on RHS or rule beginning at \"%s\".",
                            $x);
                        $this->errorcnt++;
                        $this->state = self::RESYNC_AFTER_RULE_ERROR;
                    } else {
                        if (isset($this->rhs[$this->nrhs - 1])) {
                            $msp = $this->rhs[$this->nrhs - 1];
                            if ($msp->type == PHP_ParserGenerator_Symbol::MULTITERMINAL) {
                                $inf = array_reduce($msp->subsym,
                                    array($this, '_printmulti'), '');
                                PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                                    'WARNING: symbol ' . $x . ' will not' .
                                    ' be part of previous multiterminal %s',
                                    substr($inf, 0, strlen($inf) - 1)
                                    );
                            }
                        }
                        $this->rhs[$this->nrhs] = PHP_ParserGenerator_Symbol::Symbol_new($x);
                        $this->alias[$this->nrhs] = 0;
                        $this->nrhs++;
                    }
                } elseif (($x[0] == '|' || $x[0] == '/') && $this->nrhs > 0) {
                    $msp = $this->rhs[$this->nrhs - 1];
                    if ($msp->type != PHP_ParserGenerator_Symbol::MULTITERMINAL) {
                        $origsp = $msp;
                        $msp = new PHP_ParserGenerator_Symbol;
                        $msp->type = PHP_ParserGenerator_Symbol::MULTITERMINAL;
                        $msp->nsubsym = 1;
                        $msp->subsym = array($origsp);
                        $msp->name = $origsp->name;
                        $this->rhs[$this->nrhs - 1] = $msp;
                    }
                    $msp->nsubsym++;
                    $msp->subsym[$msp->nsubsym - 1] = PHP_ParserGenerator_Symbol::Symbol_new(substr($x, 1));
                    if (preg_match('/[a-z]/', $x[1]) ||
                          preg_match('/[a-z]/', $msp->subsym[0]->name[0])) {
                        PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                        "Cannot form a compound containing a non-terminal");
                        $this->errorcnt++;
                    }
                } elseif ($x[0] == '(' && $this->nrhs > 0) {
                    $this->state = self::RHS_ALIAS_1;
                } else {
                    PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                        "Illegal character on RHS of rule: \"%s\".", $x);
                    $this->errorcnt++;
                    $this->state = self::RESYNC_AFTER_RULE_ERROR;
                }
                break;
            case self::RHS_ALIAS_1:
                if (preg_match('/[A-Za-z]/', $x[0])) {
                    $this->alias[$this->nrhs - 1] = $x;
                    $this->state = self::RHS_ALIAS_2;
                } else {
                    PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                        "\"%s\" is not a valid alias for the RHS symbol \"%s\"\n",
                        $x, $this->rhs[$this->nrhs - 1]->name);
                    $this->errorcnt++;
                    $this->state = self::RESYNC_AFTER_RULE_ERROR;
                }
                break;
            case self::RHS_ALIAS_2:
                if ($x[0] == ')') {
                    $this->state = self::IN_RHS;
                } else {
                    PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                        "Missing \")\" following LHS alias name \"%s\".", $this->lhsalias);
                    $this->errorcnt++;
                    $this->state = self::RESYNC_AFTER_RULE_ERROR;
                }
                break;
            case self::WAITING_FOR_DECL_KEYWORD:
                if (preg_match('/[A-Za-z]/', $x[0])) {
                    $this->declkeyword = $x;
                    $this->declargslot = &$this->a;
                    $this->decllnslot = &$this->a;
                    $this->state = self::WAITING_FOR_DECL_ARG;
                    if ('name' == $x) {
                        $this->declargslot = &$this->gp->name;
                    } elseif ('include' == $x) {
                        $this->declargslot = &$this->gp->include_code;
                        $this->decllnslot = &$this->gp->includeln;
                    } elseif ('include_class' == $x) {
                        $this->declargslot = &$this->gp->include_classcode;
                        $this->decllnslot = &$this->gp->include_classln;
                    } elseif ('declare_class' == $x) {
                        $this->declargslot = &$this->gp->declare_classcode;
                        $this->decllnslot = &$this->gp->declare_classln;
                    } elseif ('code' == $x) {
                        $this->declargslot = &$this->gp->extracode;
                        $this->decllnslot = &$this->gp->extracodeln;
                    } elseif ('token_destructor' == $x) {
                        $this->declargslot = &$this->gp->tokendest;
                        $this->decllnslot = &$this->gp->tokendestln;
                    } elseif ('default_destructor' == $x) {
                        $this->declargslot = &$this->gp->vardest;
                        $this->decllnslot = &$this->gp->vardestln;
                    } elseif ('token_prefix' == $x) {
                        $this->declargslot = &$this->gp->tokenprefix;
                    } elseif ('syntax_error' == $x) {
                        $this->declargslot = &$this->gp->error;
                        $this->decllnslot = &$this->gp->errorln;
                    } elseif ('parse_accept' == $x) {
                        $this->declargslot = &$this->gp->accept;
                        $this->decllnslot = &$this->gp->acceptln;
                    } elseif ('parse_failure' == $x) {
                        $this->declargslot = &$this->gp->failure;
                        $this->decllnslot = &$this->gp->failureln;
                    } elseif ('stack_overflow' == $x) {
                        $this->declargslot = &$this->gp->overflow;
                        $this->decllnslot = &$this->gp->overflowln;
                    } elseif ('token_type' == $x) {
                        $this->declargslot = &$this->gp->tokentype;
                    } elseif ('default_type' == $x) {
                        $this->declargslot = &$this->gp->vartype;
                    } elseif ('stack_size' == $x) {
                        $this->declargslot = &$this->gp->stacksize;
                    } elseif ('start_symbol' == $x) {
                        $this->declargslot = &$this->gp->start;
                    } elseif ('left' == $x) {
                        $this->preccounter++;
                        $this->declassoc = PHP_ParserGenerator_Symbol::LEFT;
                        $this->state = self::WAITING_FOR_PRECEDENCE_SYMBOL;
                    } elseif ('right' == $x) {
                        $this->preccounter++;
                        $this->declassoc = PHP_ParserGenerator_Symbol::RIGHT;
                        $this->state = self::WAITING_FOR_PRECEDENCE_SYMBOL;
                    } elseif ('nonassoc' == $x) {
                        $this->preccounter++;
                        $this->declassoc = PHP_ParserGenerator_Symbol::NONE;
                        $this->state = self::WAITING_FOR_PRECEDENCE_SYMBOL;
                    } elseif ('destructor' == $x) {
                        $this->state = self::WAITING_FOR_DESTRUCTOR_SYMBOL;
                    } elseif ('type' == $x) {
                        $this->state = self::WAITING_FOR_DATATYPE_SYMBOL;
                    } elseif ('fallback' == $x) {
                        $this->fallback = 0;
                        $this->state = self::WAITING_FOR_FALLBACK_ID;
                    } else {
                        PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                        "Unknown declaration keyword: \"%%%s\".", $x);
                        $this->errorcnt++;
                        $this->state = self::RESYNC_AFTER_DECL_ERROR;
                    }
                } else {
                    PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                        "Illegal declaration keyword: \"%s\".", $x);
                    $this->errorcnt++;
                    $this->state = self::RESYNC_AFTER_DECL_ERROR;
                }
                break;
            case self::WAITING_FOR_DESTRUCTOR_SYMBOL:
                if (!preg_match('/[A-Za-z]/', $x[0])) {
                    PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                        "Symbol name missing after %destructor keyword");
                    $this->errorcnt++;
                    $this->state = self::RESYNC_AFTER_DECL_ERROR;
                } else {
                    $sp = PHP_ParserGenerator_Symbol::Symbol_new($x);
                    $this->declargslot = &$sp->destructor;
                    $this->decllnslot = &$sp->destructorln;
                    $this->state = self::WAITING_FOR_DECL_ARG;
                }
                break;
            case self::WAITING_FOR_DATATYPE_SYMBOL:
                if (!preg_match('/[A-Za-z]/', $x[0])) {
                    PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                        "Symbol name missing after %destructor keyword");
                    $this->errorcnt++;
                    $this->state = self::RESYNC_AFTER_DECL_ERROR;
                } else {
                    $sp = PHP_ParserGenerator_Symbol::Symbol_new($x);
                    $this->declargslot = &$sp->datatype;
                    $this->state = self::WAITING_FOR_DECL_ARG;
                }
                break;
            case self::WAITING_FOR_PRECEDENCE_SYMBOL:
                if ($x[0] == '.') {
                    $this->state = self::WAITING_FOR_DECL_OR_RULE;
                } elseif (preg_match('/[A-Z]/', $x[0])) {
                    $sp = PHP_ParserGenerator_Symbol::Symbol_new($x);
                    if ($sp->prec >= 0) {
                        PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                            "Symbol \"%s\" has already been given a precedence.", $x);
                        $this->errorcnt++;
                    } else {
                        $sp->prec = $this->preccounter;
                        $sp->assoc = $this->declassoc;
                    }
                } else {
                    PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                        "Can't assign a precedence to \"%s\".", $x);
                    $this->errorcnt++;
                }
                break;
            case self::WAITING_FOR_DECL_ARG:
                if (preg_match('/[A-Za-z0-9{"]/', $x[0])) {
                    if ($this->declargslot != 0) {
                        PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                            "The argument \"%s\" to declaration \"%%%s\" is not the first.",
                            $x[0] == '"' ? substr($x, 1) : $x, $this->declkeyword);
                        $this->errorcnt++;
                        $this->state = self::RESYNC_AFTER_DECL_ERROR;
                    } else {
                        $this->declargslot = ($x[0] == '"' || $x[0] == '{') ? substr($x, 1) : $x;
                        $this->a = 1;
                        if (!$this->decllnslot) {
                            $this->decllnslot = $this->tokenlineno;
                        }
                        $this->state = self::WAITING_FOR_DECL_OR_RULE;
                    }
                } else {
                    PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                        "Illegal argument to %%%s: %s",$this->declkeyword, $x);
                    $this->errorcnt++;
                    $this->state = self::RESYNC_AFTER_DECL_ERROR;
                }
                break;
            case self::WAITING_FOR_FALLBACK_ID:
                if ($x[0] == '.') {
                    $this->state = self::WAITING_FOR_DECL_OR_RULE;
                } elseif (!preg_match('/[A-Z]/', $x[0])) {
                    PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                    "%%fallback argument \"%s\" should be a token", $x);
                    $this->errorcnt++;
                } else {
                    $sp = PHP_ParserGenerator_Symbol::Symbol_new($x);
                    if ($this->fallback === 0) {
                        $this->fallback = $sp;
                    } elseif (is_object($sp->fallback)) {
                        PHP_ParserGenerator::ErrorMsg($this->filename, $this->tokenlineno,
                        "More than one fallback assigned to token %s", $x);
                        $this->errorcnt++;
                    } else {
                        $sp->fallback = $this->fallback;
                        $this->gp->has_fallback = 1;
                    }
                }
                break;
            case self::RESYNC_AFTER_RULE_ERROR:
            /*      if ($x[0] == '.') $this->state = self::WAITING_FOR_DECL_OR_RULE;
            **      break; */
            case self::RESYNC_AFTER_DECL_ERROR:
                if ($x[0] == '.') {
                    $this->state = self::WAITING_FOR_DECL_OR_RULE;
                }
                if ($x[0] == '%') {
                    $this->state = self::WAITING_FOR_DECL_KEYWORD;
                }
                break;
        }
    }

    /**
     * return a descriptive string for a multi-terminal token.
     *
     * @param  string $a
     * @param  string $b
     * @return string
     */
    private function _printmulti($a, $b)
    {
        if (!$a) {
            $a = '';
        }
        $a .= $b->name . '|';

        return $a;
    }
}
