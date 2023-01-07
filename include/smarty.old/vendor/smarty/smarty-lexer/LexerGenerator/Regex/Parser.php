<?php
/* Driver template for the PHP_PHP_LexerGenerator_Regex_rGenerator parser generator. (PHP port of LEMON)
*/

/**
 * This can be used to store both the string representation of
 * a token, and any useful meta-data associated with the token.
 *
 * meta-data should be stored as an array
 */
class PHP_LexerGenerator_Regex_yyToken implements ArrayAccess
{
    public $string = '';
    public $metadata = array();

    public function __construct($s, $m = array())
    {
        if ($s instanceof PHP_LexerGenerator_Regex_yyToken) {
            $this->string = $s->string;
            $this->metadata = $s->metadata;
        } else {
            $this->string = (string) $s;
            if ($m instanceof PHP_LexerGenerator_Regex_yyToken) {
                $this->metadata = $m->metadata;
            } elseif (is_array($m)) {
                $this->metadata = $m;
            }
        }
    }

    public function __toString()
    {
        return $this->_string;
    }

    public function offsetExists($offset)
    {
        return isset($this->metadata[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->metadata[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            if (isset($value[0])) {
                $x = ($value instanceof PHP_LexerGenerator_Regex_yyToken) ?
                    $value->metadata : $value;
                $this->metadata = array_merge($this->metadata, $x);

                return;
            }
            $offset = count($this->metadata);
        }
        if ($value === null) {
            return;
        }
        if ($value instanceof PHP_LexerGenerator_Regex_yyToken) {
            if ($value->metadata) {
                $this->metadata[$offset] = $value->metadata;
            }
        } elseif ($value) {
            $this->metadata[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->metadata[$offset]);
    }
}

/** The following structure represents a single element of the
 * parser's stack.  Information stored includes:
 *
 *   +  The state number for the parser at this level of the stack.
 *
 *   +  The value of the token stored at this level of the stack.
 *      (In other words, the "major" token.)
 *
 *   +  The semantic value stored at this level of the stack.  This is
 *      the information used by the action routines in the grammar.
 *      It is sometimes called the "minor" token.
 */
class PHP_LexerGenerator_Regex_yyStackEntry
{
    public $stateno;       /* The state-number */
    public $major;         /* The major token value.  This is the code
                     ** number for the token at this stack level */
    public $minor; /* The user-supplied minor token value.  This
                     ** is the value of the token  */
};

// code external to the class is included here
#line 2 "Parser.y"

require_once './LexerGenerator/Exception.php';
#line 102 "Parser.php"

// declare_class is output here
#line 5 "Parser.y"
class PHP_LexerGenerator_Regex_Parser#line 107 "Parser.php"
{
/* First off, code is included which follows the "include_class" declaration
** in the input file. */
#line 21 "Parser.y"

    private $_lex;
    private $_subpatterns;
    private $_updatePattern;
    private $_patternIndex;
    public $result;
    public function __construct($lex)
    {
        $this->result = new PHP_LexerGenerator_ParseryyToken('');
        $this->_lex = $lex;
        $this->_subpatterns = 0;
        $this->_patternIndex = 1;
    }

    public function reset($patternIndex, $updatePattern = false)
    {
        $this->_updatePattern = $updatePattern;
        $this->_patternIndex = $patternIndex;
        $this->_subpatterns = 0;
        $this->result = new PHP_LexerGenerator_ParseryyToken('');
    }
#line 134 "Parser.php"

/* Next is all token values, as class constants
*/
/*
** These constants (all generated automatically by the parser generator)
** specify the various kinds of tokens (terminals) that the parser
** understands.
**
** Each symbol here is a terminal symbol in the grammar.
*/
    const OPENPAREN                      =  1;
    const OPENASSERTION                  =  2;
    const BAR                            =  3;
    const MULTIPLIER                     =  4;
    const MATCHSTART                     =  5;
    const MATCHEND                       =  6;
    const OPENCHARCLASS                  =  7;
    const CLOSECHARCLASS                 =  8;
    const NEGATE                         =  9;
    const TEXT                           = 10;
    const ESCAPEDBACKSLASH               = 11;
    const HYPHEN                         = 12;
    const BACKREFERENCE                  = 13;
    const COULDBEBACKREF                 = 14;
    const CONTROLCHAR                    = 15;
    const FULLSTOP                       = 16;
    const INTERNALOPTIONS                = 17;
    const CLOSEPAREN                     = 18;
    const COLON                          = 19;
    const POSITIVELOOKAHEAD              = 20;
    const NEGATIVELOOKAHEAD              = 21;
    const POSITIVELOOKBEHIND             = 22;
    const NEGATIVELOOKBEHIND             = 23;
    const PATTERNNAME                    = 24;
    const ONCEONLY                       = 25;
    const COMMENT                        = 26;
    const RECUR                          = 27;
    const YY_NO_ACTION = 230;
    const YY_ACCEPT_ACTION = 229;
    const YY_ERROR_ACTION = 228;

/* Next are that tables used to determine what action to take based on the
** current state and lookahead token.  These tables are used to implement
** functions that take a state number and lookahead value and return an
** action integer.
**
** Suppose the action integer is N.  Then the action is determined as
** follows
**
**   0 <= N < self::YYNSTATE                              Shift N.  That is,
**                                                        push the lookahead
**                                                        token onto the stack
**                                                        and goto state N.
**
**   self::YYNSTATE <= N < self::YYNSTATE+self::YYNRULE   Reduce by rule N-YYNSTATE.
**
**   N == self::YYNSTATE+self::YYNRULE                    A syntax error has occurred.
**
**   N == self::YYNSTATE+self::YYNRULE+1                  The parser accepts its
**                                                        input. (and concludes parsing)
**
**   N == self::YYNSTATE+self::YYNRULE+2                  No such action.  Denotes unused
**                                                        slots in the yy_action[] table.
**
** The action table is constructed as a single large static array $yy_action.
** Given state S and lookahead X, the action is computed as
**
**      self::$yy_action[self::$yy_shift_ofst[S] + X ]
**
** If the index value self::$yy_shift_ofst[S]+X is out of range or if the value
** self::$yy_lookahead[self::$yy_shift_ofst[S]+X] is not equal to X or if
** self::$yy_shift_ofst[S] is equal to self::YY_SHIFT_USE_DFLT, it means that
** the action is not in the table and that self::$yy_default[S] should be used instead.
**
** The formula above is for computing the action when the lookahead is
** a terminal symbol.  If the lookahead is a non-terminal (as occurs after
** a reduce action) then the static $yy_reduce_ofst array is used in place of
** the static $yy_shift_ofst array and self::YY_REDUCE_USE_DFLT is used in place of
** self::YY_SHIFT_USE_DFLT.
**
** The following are the tables generated in this section:
**
**  self::$yy_action        A single table containing all actions.
**  self::$yy_lookahead     A table containing the lookahead for each entry in
**                          yy_action.  Used to detect hash collisions.
**  self::$yy_shift_ofst    For each state, the offset into self::$yy_action for
**                          shifting terminals.
**  self::$yy_reduce_ofst   For each state, the offset into self::$yy_action for
**                          shifting non-terminals after a reduce.
**  self::$yy_default       Default action for each state.
*/
    const YY_SZ_ACTTAB = 354;
public static $yy_action = array(
 /*     0 */   229,   45,   15,   23,  104,  106,  107,  109,  108,  118,
 /*    10 */   119,  129,  128,  130,   36,   15,   23,  104,  106,  107,
 /*    20 */   109,  108,  118,  119,  129,  128,  130,   39,   15,   23,
 /*    30 */   104,  106,  107,  109,  108,  118,  119,  129,  128,  130,
 /*    40 */    25,   15,   23,  104,  106,  107,  109,  108,  118,  119,
 /*    50 */   129,  128,  130,   32,   15,   23,  104,  106,  107,  109,
 /*    60 */   108,  118,  119,  129,  128,  130,   28,   15,   23,  104,
 /*    70 */   106,  107,  109,  108,  118,  119,  129,  128,  130,   35,
 /*    80 */    15,   23,  104,  106,  107,  109,  108,  118,  119,  129,
 /*    90 */   128,  130,   92,   15,   23,  104,  106,  107,  109,  108,
 /*   100 */   118,  119,  129,  128,  130,   38,   15,   23,  104,  106,
 /*   110 */   107,  109,  108,  118,  119,  129,  128,  130,   40,   15,
 /*   120 */    23,  104,  106,  107,  109,  108,  118,  119,  129,  128,
 /*   130 */   130,   33,   15,   23,  104,  106,  107,  109,  108,  118,
 /*   140 */   119,  129,  128,  130,   30,   15,   23,  104,  106,  107,
 /*   150 */   109,  108,  118,  119,  129,  128,  130,   37,   15,   23,
 /*   160 */   104,  106,  107,  109,  108,  118,  119,  129,  128,  130,
 /*   170 */    34,   15,   23,  104,  106,  107,  109,  108,  118,  119,
 /*   180 */   129,  128,  130,   16,   23,  104,  106,  107,  109,  108,
 /*   190 */   118,  119,  129,  128,  130,   54,   24,   22,   72,   76,
 /*   200 */    85,   84,   82,   81,   80,   97,  134,  125,   93,   12,
 /*   210 */    12,   26,   83,    2,    5,    1,   11,    4,   10,   13,
 /*   220 */    49,   50,    9,   17,   46,   98,   14,   12,   18,  113,
 /*   230 */   124,   52,   43,   79,   44,   57,   42,   41,    9,   17,
 /*   240 */   127,   12,   53,   91,   18,  126,   12,   52,   43,  120,
 /*   250 */    44,   57,   42,   41,    9,   17,   47,   12,   31,  117,
 /*   260 */    18,   88,   99,   52,   43,   75,   44,   57,   42,   41,
 /*   270 */     9,   17,   51,   19,   67,   69,   18,  101,   87,   52,
 /*   280 */    43,   12,   44,   57,   42,   41,  132,   64,   63,  103,
 /*   290 */    62,   58,   66,   65,   59,   12,   60,   68,   90,  111,
 /*   300 */   116,  122,   61,  100,   60,   68,   12,  111,  116,  122,
 /*   310 */    71,    5,    1,   11,    4,   67,   69,   12,  101,   87,
 /*   320 */    12,  102,   12,   12,  112,    6,  105,  131,   78,    7,
 /*   330 */     8,   95,   77,   74,   70,   56,  123,   48,  133,   73,
 /*   340 */    27,  114,   86,   55,  115,   89,  110,  121,    3,   94,
 /*   350 */    21,   29,   96,   20,
    );
    public static $yy_lookahead = array(
 /*     0 */    29,   30,   31,   32,   33,   34,   35,   36,   37,   38,
 /*    10 */    39,   40,   41,   42,   30,   31,   32,   33,   34,   35,
 /*    20 */    36,   37,   38,   39,   40,   41,   42,   30,   31,   32,
 /*    30 */    33,   34,   35,   36,   37,   38,   39,   40,   41,   42,
 /*    40 */    30,   31,   32,   33,   34,   35,   36,   37,   38,   39,
 /*    50 */    40,   41,   42,   30,   31,   32,   33,   34,   35,   36,
 /*    60 */    37,   38,   39,   40,   41,   42,   30,   31,   32,   33,
 /*    70 */    34,   35,   36,   37,   38,   39,   40,   41,   42,   30,
 /*    80 */    31,   32,   33,   34,   35,   36,   37,   38,   39,   40,
 /*    90 */    41,   42,   30,   31,   32,   33,   34,   35,   36,   37,
 /*   100 */    38,   39,   40,   41,   42,   30,   31,   32,   33,   34,
 /*   110 */    35,   36,   37,   38,   39,   40,   41,   42,   30,   31,
 /*   120 */    32,   33,   34,   35,   36,   37,   38,   39,   40,   41,
 /*   130 */    42,   30,   31,   32,   33,   34,   35,   36,   37,   38,
 /*   140 */    39,   40,   41,   42,   30,   31,   32,   33,   34,   35,
 /*   150 */    36,   37,   38,   39,   40,   41,   42,   30,   31,   32,
 /*   160 */    33,   34,   35,   36,   37,   38,   39,   40,   41,   42,
 /*   170 */    30,   31,   32,   33,   34,   35,   36,   37,   38,   39,
 /*   180 */    40,   41,   42,   31,   32,   33,   34,   35,   36,   37,
 /*   190 */    38,   39,   40,   41,   42,    1,    2,   32,   33,   34,
 /*   200 */    35,   36,   37,   38,   39,   40,   41,   42,   18,    3,
 /*   210 */     3,   17,   10,   19,   20,   21,   22,   23,   24,   25,
 /*   220 */    26,   27,    1,    2,   18,   18,    5,    3,    7,   10,
 /*   230 */    11,   10,   11,    4,   13,   14,   15,   16,    1,    2,
 /*   240 */    10,    3,   18,    6,    7,   15,    3,   10,   11,    4,
 /*   250 */    13,   14,   15,   16,    1,    2,   18,    3,   12,    6,
 /*   260 */     7,   18,    4,   10,   11,    4,   13,   14,   15,   16,
 /*   270 */     1,    2,   18,    9,   10,   11,    7,   13,   14,   10,
 /*   280 */    11,    3,   13,   14,   15,   16,    4,   10,   11,    4,
 /*   290 */    13,   14,   15,   16,    8,    3,   10,   11,   18,   13,
 /*   300 */    14,   15,    8,    4,   10,   11,    3,   13,   14,   15,
 /*   310 */    18,   20,   21,   22,   23,   10,   11,    3,   13,   14,
 /*   320 */     3,   18,    3,    3,   18,   19,   10,   11,    4,   36,
 /*   330 */    37,    4,   18,    4,   12,   18,    4,   18,   18,    4,
 /*   340 */    12,    4,    4,   10,    4,    4,    4,    4,   18,    4,
 /*   350 */    43,   12,    4,   43,
);
    const YY_SHIFT_USE_DFLT = -1;
    const YY_SHIFT_MAX = 70;
    public static $yy_shift_ofst = array(
 /*     0 */   221,  221,  221,  221,  221,  221,  221,  221,  221,  221,
 /*    10 */   221,  221,  221,  221,  269,  253,  237,  194,  264,  305,
 /*    20 */   286,  294,  277,  277,  291,  320,  306,  316,  317,  219,
 /*    30 */   224,  230,  238,  206,  207,  319,  243,  314,  303,  254,
 /*    40 */   292,  345,  348,  261,  282,  278,  285,  324,  327,  280,
 /*    50 */   190,  229,  245,  343,  333,  330,  342,  337,  329,  332,
 /*    60 */   328,  340,  335,  338,  341,  299,  258,  339,  246,  322,
 /*    70 */   202,
);
    const YY_REDUCE_USE_DFLT = -30;
    const YY_REDUCE_MAX = 19;
    public static $yy_reduce_ofst = array(
 /*     0 */   -29,  127,  114,  101,  140,   88,   10,   -3,   23,   36,
 /*    10 */    49,   75,   62,  -16,  152,  165,  165,  293,  310,  307,
);
    public static $yyExpectedTokens = array(
        /* 0 */ array(1, 2, 5, 7, 10, 11, 13, 14, 15, 16, ),
        /* 1 */ array(1, 2, 5, 7, 10, 11, 13, 14, 15, 16, ),
        /* 2 */ array(1, 2, 5, 7, 10, 11, 13, 14, 15, 16, ),
        /* 3 */ array(1, 2, 5, 7, 10, 11, 13, 14, 15, 16, ),
        /* 4 */ array(1, 2, 5, 7, 10, 11, 13, 14, 15, 16, ),
        /* 5 */ array(1, 2, 5, 7, 10, 11, 13, 14, 15, 16, ),
        /* 6 */ array(1, 2, 5, 7, 10, 11, 13, 14, 15, 16, ),
        /* 7 */ array(1, 2, 5, 7, 10, 11, 13, 14, 15, 16, ),
        /* 8 */ array(1, 2, 5, 7, 10, 11, 13, 14, 15, 16, ),
        /* 9 */ array(1, 2, 5, 7, 10, 11, 13, 14, 15, 16, ),
        /* 10 */ array(1, 2, 5, 7, 10, 11, 13, 14, 15, 16, ),
        /* 11 */ array(1, 2, 5, 7, 10, 11, 13, 14, 15, 16, ),
        /* 12 */ array(1, 2, 5, 7, 10, 11, 13, 14, 15, 16, ),
        /* 13 */ array(1, 2, 5, 7, 10, 11, 13, 14, 15, 16, ),
        /* 14 */ array(1, 2, 7, 10, 11, 13, 14, 15, 16, ),
        /* 15 */ array(1, 2, 6, 7, 10, 11, 13, 14, 15, 16, ),
        /* 16 */ array(1, 2, 6, 7, 10, 11, 13, 14, 15, 16, ),
        /* 17 */ array(1, 2, 17, 19, 20, 21, 22, 23, 24, 25, 26, 27, ),
        /* 18 */ array(9, 10, 11, 13, 14, ),
        /* 19 */ array(10, 11, 13, 14, ),
        /* 20 */ array(8, 10, 11, 13, 14, 15, ),
        /* 21 */ array(8, 10, 11, 13, 14, 15, ),
        /* 22 */ array(10, 11, 13, 14, 15, 16, ),
        /* 23 */ array(10, 11, 13, 14, 15, 16, ),
        /* 24 */ array(20, 21, 22, 23, ),
        /* 25 */ array(3, 18, ),
        /* 26 */ array(18, 19, ),
        /* 27 */ array(10, 11, ),
        /* 28 */ array(3, 18, ),
        /* 29 */ array(10, 11, ),
        /* 30 */ array(3, 18, ),
        /* 31 */ array(10, 15, ),
        /* 32 */ array(3, 18, ),
        /* 33 */ array(3, 18, ),
        /* 34 */ array(3, 18, ),
        /* 35 */ array(3, 18, ),
        /* 36 */ array(3, 18, ),
        /* 37 */ array(3, 18, ),
        /* 38 */ array(3, 18, ),
        /* 39 */ array(3, 18, ),
        /* 40 */ array(3, 18, ),
        /* 41 */ array(4, ),
        /* 42 */ array(4, ),
        /* 43 */ array(4, ),
        /* 44 */ array(4, ),
        /* 45 */ array(3, ),
        /* 46 */ array(4, ),
        /* 47 */ array(4, ),
        /* 48 */ array(4, ),
        /* 49 */ array(18, ),
        /* 50 */ array(18, ),
        /* 51 */ array(4, ),
        /* 52 */ array(4, ),
        /* 53 */ array(4, ),
        /* 54 */ array(10, ),
        /* 55 */ array(18, ),
        /* 56 */ array(4, ),
        /* 57 */ array(4, ),
        /* 58 */ array(4, ),
        /* 59 */ array(4, ),
        /* 60 */ array(12, ),
        /* 61 */ array(4, ),
        /* 62 */ array(4, ),
        /* 63 */ array(4, ),
        /* 64 */ array(4, ),
        /* 65 */ array(4, ),
        /* 66 */ array(4, ),
        /* 67 */ array(12, ),
        /* 68 */ array(12, ),
        /* 69 */ array(12, ),
        /* 70 */ array(10, ),
        /* 71 */ array(),
        /* 72 */ array(),
        /* 73 */ array(),
        /* 74 */ array(),
        /* 75 */ array(),
        /* 76 */ array(),
        /* 77 */ array(),
        /* 78 */ array(),
        /* 79 */ array(),
        /* 80 */ array(),
        /* 81 */ array(),
        /* 82 */ array(),
        /* 83 */ array(),
        /* 84 */ array(),
        /* 85 */ array(),
        /* 86 */ array(),
        /* 87 */ array(),
        /* 88 */ array(),
        /* 89 */ array(),
        /* 90 */ array(),
        /* 91 */ array(),
        /* 92 */ array(),
        /* 93 */ array(),
        /* 94 */ array(),
        /* 95 */ array(),
        /* 96 */ array(),
        /* 97 */ array(),
        /* 98 */ array(),
        /* 99 */ array(),
        /* 100 */ array(),
        /* 101 */ array(),
        /* 102 */ array(),
        /* 103 */ array(),
        /* 104 */ array(),
        /* 105 */ array(),
        /* 106 */ array(),
        /* 107 */ array(),
        /* 108 */ array(),
        /* 109 */ array(),
        /* 110 */ array(),
        /* 111 */ array(),
        /* 112 */ array(),
        /* 113 */ array(),
        /* 114 */ array(),
        /* 115 */ array(),
        /* 116 */ array(),
        /* 117 */ array(),
        /* 118 */ array(),
        /* 119 */ array(),
        /* 120 */ array(),
        /* 121 */ array(),
        /* 122 */ array(),
        /* 123 */ array(),
        /* 124 */ array(),
        /* 125 */ array(),
        /* 126 */ array(),
        /* 127 */ array(),
        /* 128 */ array(),
        /* 129 */ array(),
        /* 130 */ array(),
        /* 131 */ array(),
        /* 132 */ array(),
        /* 133 */ array(),
        /* 134 */ array(),
);
    public static $yy_default = array(
 /*     0 */   228,  228,  228,  228,  228,  228,  228,  228,  228,  228,
 /*    10 */   228,  228,  228,  228,  228,  139,  137,  228,  228,  228,
 /*    20 */   228,  228,  152,  141,  228,  228,  228,  228,  228,  228,
 /*    30 */   228,  228,  228,  228,  228,  228,  228,  228,  228,  228,
 /*    40 */   228,  185,  187,  189,  191,  135,  212,  215,  221,  228,
 /*    50 */   228,  213,  183,  209,  228,  228,  223,  193,  205,  163,
 /*    60 */   176,  164,  203,  201,  195,  197,  199,  167,  175,  168,
 /*    70 */   228,  217,  153,  204,  206,  190,  154,  218,  216,  214,
 /*    80 */   159,  158,  157,  169,  156,  155,  202,  173,  225,  196,
 /*    90 */   226,  136,  140,  227,  186,  222,  188,  160,  220,  200,
 /*   100 */   198,  172,  219,  211,  142,  180,  143,  144,  146,  145,
 /*   110 */   224,  181,  207,  170,  194,  166,  182,  138,  147,  148,
 /*   120 */   184,  210,  174,  165,  171,  162,  177,  178,  150,  149,
 /*   130 */   151,  179,  192,  208,  161,
);
/* The next thing included is series of defines which control
** various aspects of the generated parser.
**    self::YYNOCODE      is a number which corresponds
**                        to no legal terminal or nonterminal number.  This
**                        number is used to fill in empty slots of the hash
**                        table.
**    self::YYFALLBACK    If defined, this indicates that one or more tokens
**                        have fall-back values which should be used if the
**                        original value of the token will not parse.
**    self::YYSTACKDEPTH  is the maximum depth of the parser's stack.
**    self::YYNSTATE      the combined number of states.
**    self::YYNRULE       the number of rules in the grammar
**    self::YYERRORSYMBOL is the code number of the error symbol.  If not
**                        defined, then do no error processing.
*/
    const YYNOCODE = 45;
    const YYSTACKDEPTH = 100;
    const YYNSTATE = 135;
    const YYNRULE = 93;
    const YYERRORSYMBOL = 28;
    const YYERRSYMDT = 'yy0';
    const YYFALLBACK = 0;
    /** The next table maps tokens into fallback tokens.  If a construct
     * like the following:
     *
     *      %fallback ID X Y Z.
     *
     * appears in the grammer, then ID becomes a fallback token for X, Y,
     * and Z.  Whenever one of the tokens X, Y, or Z is input to the parser
     * but it does not parse, the type of the token is changed to ID and
     * the parse is retried before an error is thrown.
     */
    public static $yyFallback = array(
    );
    /**
     * Turn parser tracing on by giving a stream to which to write the trace
     * and a prompt to preface each trace message.  Tracing is turned off
     * by making either argument NULL
     *
     * Inputs:
     *
     * - A stream resource to which trace output should be written.
     *   If NULL, then tracing is turned off.
     * - A prefix string written at the beginning of every
     *   line of trace output.  If NULL, then tracing is
     *   turned off.
     *
     * Outputs:
     *
     * - None.
     * @param resource
     * @param string
     */
    public static function Trace($TraceFILE, $zTracePrompt)
    {
        if (!$TraceFILE) {
            $zTracePrompt = 0;
        } elseif (!$zTracePrompt) {
            $TraceFILE = 0;
        }
        self::$yyTraceFILE = $TraceFILE;
        self::$yyTracePrompt = $zTracePrompt;
    }

    /**
     * Output debug information to output (php://output stream)
     */
    public static function PrintTrace()
    {
        self::$yyTraceFILE = fopen('php://output', 'w');
        self::$yyTracePrompt = '';
    }

    /**
     * @var resource|0
     */
    public static $yyTraceFILE;
    /**
     * String to prepend to debug output
     * @var string|0
     */
    public static $yyTracePrompt;
    /**
     * @var int
     */
    public $yyidx;                    /* Index of top element in stack */
    /**
     * @var int
     */
    public $yyerrcnt;                 /* Shifts left before out of the error */
    /**
     * @var array
     */
    public $yystack = array();  /* The parser's stack */

    /**
     * For tracing shifts, the names of all terminals and nonterminals
     * are required.  The following table supplies these names
     * @var array
     */
    public static $yyTokenName = array(
  '$',             'OPENPAREN',     'OPENASSERTION',  'BAR',
  'MULTIPLIER',    'MATCHSTART',    'MATCHEND',      'OPENCHARCLASS',
  'CLOSECHARCLASS',  'NEGATE',        'TEXT',          'ESCAPEDBACKSLASH',
  'HYPHEN',        'BACKREFERENCE',  'COULDBEBACKREF',  'CONTROLCHAR',
  'FULLSTOP',      'INTERNALOPTIONS',  'CLOSEPAREN',    'COLON',
  'POSITIVELOOKAHEAD',  'NEGATIVELOOKAHEAD',  'POSITIVELOOKBEHIND',  'NEGATIVELOOKBEHIND',
  'PATTERNNAME',   'ONCEONLY',      'COMMENT',       'RECUR',
  'error',         'start',         'pattern',       'basic_pattern',
  'basic_text',    'character_class',  'assertion',     'grouping',
  'lookahead',     'lookbehind',    'subpattern',    'onceonly',
  'comment',       'recur',         'conditional',   'character_class_contents',
    );

    /**
     * For tracing reduce actions, the names of all rules are required.
     * @var array
     */
    public static $yyRuleName = array(
 /*   0 */ "start ::= pattern",
 /*   1 */ "pattern ::= MATCHSTART basic_pattern MATCHEND",
 /*   2 */ "pattern ::= MATCHSTART basic_pattern",
 /*   3 */ "pattern ::= basic_pattern MATCHEND",
 /*   4 */ "pattern ::= basic_pattern",
 /*   5 */ "pattern ::= pattern BAR pattern",
 /*   6 */ "basic_pattern ::= basic_text",
 /*   7 */ "basic_pattern ::= character_class",
 /*   8 */ "basic_pattern ::= assertion",
 /*   9 */ "basic_pattern ::= grouping",
 /*  10 */ "basic_pattern ::= lookahead",
 /*  11 */ "basic_pattern ::= lookbehind",
 /*  12 */ "basic_pattern ::= subpattern",
 /*  13 */ "basic_pattern ::= onceonly",
 /*  14 */ "basic_pattern ::= comment",
 /*  15 */ "basic_pattern ::= recur",
 /*  16 */ "basic_pattern ::= conditional",
 /*  17 */ "basic_pattern ::= basic_pattern basic_text",
 /*  18 */ "basic_pattern ::= basic_pattern character_class",
 /*  19 */ "basic_pattern ::= basic_pattern assertion",
 /*  20 */ "basic_pattern ::= basic_pattern grouping",
 /*  21 */ "basic_pattern ::= basic_pattern lookahead",
 /*  22 */ "basic_pattern ::= basic_pattern lookbehind",
 /*  23 */ "basic_pattern ::= basic_pattern subpattern",
 /*  24 */ "basic_pattern ::= basic_pattern onceonly",
 /*  25 */ "basic_pattern ::= basic_pattern comment",
 /*  26 */ "basic_pattern ::= basic_pattern recur",
 /*  27 */ "basic_pattern ::= basic_pattern conditional",
 /*  28 */ "character_class ::= OPENCHARCLASS character_class_contents CLOSECHARCLASS",
 /*  29 */ "character_class ::= OPENCHARCLASS NEGATE character_class_contents CLOSECHARCLASS",
 /*  30 */ "character_class ::= OPENCHARCLASS character_class_contents CLOSECHARCLASS MULTIPLIER",
 /*  31 */ "character_class ::= OPENCHARCLASS NEGATE character_class_contents CLOSECHARCLASS MULTIPLIER",
 /*  32 */ "character_class_contents ::= TEXT",
 /*  33 */ "character_class_contents ::= ESCAPEDBACKSLASH",
 /*  34 */ "character_class_contents ::= ESCAPEDBACKSLASH HYPHEN TEXT",
 /*  35 */ "character_class_contents ::= TEXT HYPHEN TEXT",
 /*  36 */ "character_class_contents ::= TEXT HYPHEN ESCAPEDBACKSLASH",
 /*  37 */ "character_class_contents ::= BACKREFERENCE",
 /*  38 */ "character_class_contents ::= COULDBEBACKREF",
 /*  39 */ "character_class_contents ::= character_class_contents CONTROLCHAR",
 /*  40 */ "character_class_contents ::= character_class_contents ESCAPEDBACKSLASH",
 /*  41 */ "character_class_contents ::= character_class_contents TEXT",
 /*  42 */ "character_class_contents ::= character_class_contents ESCAPEDBACKSLASH HYPHEN CONTROLCHAR",
 /*  43 */ "character_class_contents ::= character_class_contents ESCAPEDBACKSLASH HYPHEN TEXT",
 /*  44 */ "character_class_contents ::= character_class_contents TEXT HYPHEN ESCAPEDBACKSLASH",
 /*  45 */ "character_class_contents ::= character_class_contents TEXT HYPHEN TEXT",
 /*  46 */ "character_class_contents ::= character_class_contents BACKREFERENCE",
 /*  47 */ "character_class_contents ::= character_class_contents COULDBEBACKREF",
 /*  48 */ "basic_text ::= TEXT",
 /*  49 */ "basic_text ::= TEXT MULTIPLIER",
 /*  50 */ "basic_text ::= FULLSTOP",
 /*  51 */ "basic_text ::= FULLSTOP MULTIPLIER",
 /*  52 */ "basic_text ::= CONTROLCHAR",
 /*  53 */ "basic_text ::= CONTROLCHAR MULTIPLIER",
 /*  54 */ "basic_text ::= ESCAPEDBACKSLASH",
 /*  55 */ "basic_text ::= ESCAPEDBACKSLASH MULTIPLIER",
 /*  56 */ "basic_text ::= BACKREFERENCE",
 /*  57 */ "basic_text ::= BACKREFERENCE MULTIPLIER",
 /*  58 */ "basic_text ::= COULDBEBACKREF",
 /*  59 */ "basic_text ::= COULDBEBACKREF MULTIPLIER",
 /*  60 */ "basic_text ::= basic_text TEXT",
 /*  61 */ "basic_text ::= basic_text TEXT MULTIPLIER",
 /*  62 */ "basic_text ::= basic_text FULLSTOP",
 /*  63 */ "basic_text ::= basic_text FULLSTOP MULTIPLIER",
 /*  64 */ "basic_text ::= basic_text CONTROLCHAR",
 /*  65 */ "basic_text ::= basic_text CONTROLCHAR MULTIPLIER",
 /*  66 */ "basic_text ::= basic_text ESCAPEDBACKSLASH",
 /*  67 */ "basic_text ::= basic_text ESCAPEDBACKSLASH MULTIPLIER",
 /*  68 */ "basic_text ::= basic_text BACKREFERENCE",
 /*  69 */ "basic_text ::= basic_text BACKREFERENCE MULTIPLIER",
 /*  70 */ "basic_text ::= basic_text COULDBEBACKREF",
 /*  71 */ "basic_text ::= basic_text COULDBEBACKREF MULTIPLIER",
 /*  72 */ "assertion ::= OPENASSERTION INTERNALOPTIONS CLOSEPAREN",
 /*  73 */ "assertion ::= OPENASSERTION INTERNALOPTIONS COLON pattern CLOSEPAREN",
 /*  74 */ "grouping ::= OPENASSERTION COLON pattern CLOSEPAREN",
 /*  75 */ "grouping ::= OPENASSERTION COLON pattern CLOSEPAREN MULTIPLIER",
 /*  76 */ "conditional ::= OPENASSERTION OPENPAREN TEXT CLOSEPAREN pattern CLOSEPAREN MULTIPLIER",
 /*  77 */ "conditional ::= OPENASSERTION OPENPAREN TEXT CLOSEPAREN pattern CLOSEPAREN",
 /*  78 */ "conditional ::= OPENASSERTION lookahead pattern CLOSEPAREN",
 /*  79 */ "conditional ::= OPENASSERTION lookahead pattern CLOSEPAREN MULTIPLIER",
 /*  80 */ "conditional ::= OPENASSERTION lookbehind pattern CLOSEPAREN",
 /*  81 */ "conditional ::= OPENASSERTION lookbehind pattern CLOSEPAREN MULTIPLIER",
 /*  82 */ "lookahead ::= OPENASSERTION POSITIVELOOKAHEAD pattern CLOSEPAREN",
 /*  83 */ "lookahead ::= OPENASSERTION NEGATIVELOOKAHEAD pattern CLOSEPAREN",
 /*  84 */ "lookbehind ::= OPENASSERTION POSITIVELOOKBEHIND pattern CLOSEPAREN",
 /*  85 */ "lookbehind ::= OPENASSERTION NEGATIVELOOKBEHIND pattern CLOSEPAREN",
 /*  86 */ "subpattern ::= OPENASSERTION PATTERNNAME pattern CLOSEPAREN",
 /*  87 */ "subpattern ::= OPENASSERTION PATTERNNAME pattern CLOSEPAREN MULTIPLIER",
 /*  88 */ "subpattern ::= OPENPAREN pattern CLOSEPAREN",
 /*  89 */ "subpattern ::= OPENPAREN pattern CLOSEPAREN MULTIPLIER",
 /*  90 */ "onceonly ::= OPENASSERTION ONCEONLY pattern CLOSEPAREN",
 /*  91 */ "comment ::= OPENASSERTION COMMENT CLOSEPAREN",
 /*  92 */ "recur ::= OPENASSERTION RECUR CLOSEPAREN",
    );

    /**
     * This function returns the symbolic name associated with a token
     * value.
     * @param int
     * @return string
     */
    public function tokenName($tokenType)
    {
        if ($tokenType === 0) {
            return 'End of Input';
        }
        if ($tokenType > 0 && $tokenType < count(self::$yyTokenName)) {
            return self::$yyTokenName[$tokenType];
        } else {
            return "Unknown";
        }
    }

    /**
     * The following function deletes the value associated with a
     * symbol.  The symbol can be either a terminal or nonterminal.
     * @param int the symbol code
     * @param mixed the symbol's value
     */
    public static function yy_destructor($yymajor, $yypminor)
    {
        switch ($yymajor) {
        /* Here is inserted the actions which take place when a
        ** terminal or non-terminal is destroyed.  This can happen
        ** when the symbol is popped from the stack during a
        ** reduce or during error processing or when a parser is
        ** being destroyed before it is finished parsing.
        **
        ** Note: during a reduce, the only symbols destroyed are those
        ** which appear on the RHS of the rule, but which are not used
        ** inside the C code.
        */
            default:  break;   /* If no destructor action specified: do nothing */
        }
    }

    /**
     * Pop the parser's stack once.
     *
     * If there is a destructor routine associated with the token which
     * is popped from the stack, then call it.
     *
     * Return the major token number for the symbol popped.
     * @param PHP_LexerGenerator_Regex_yyParser
     * @return int
     */
    public function yy_pop_parser_stack()
    {
        if (!count($this->yystack)) {
            return;
        }
        $yytos = array_pop($this->yystack);
        if (self::$yyTraceFILE && $this->yyidx >= 0) {
            fwrite(self::$yyTraceFILE,
                self::$yyTracePrompt . 'Popping ' . self::$yyTokenName[$yytos->major] .
                    "\n");
        }
        $yymajor = $yytos->major;
        self::yy_destructor($yymajor, $yytos->minor);
        $this->yyidx--;

        return $yymajor;
    }

    /**
     * Deallocate and destroy a parser.  Destructors are all called for
     * all stack elements before shutting the parser down.
     */
    public function __destruct()
    {
        while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
        if (is_resource(self::$yyTraceFILE)) {
            fclose(self::$yyTraceFILE);
        }
    }

    /**
     * Based on the current state and parser stack, get a list of all
     * possible lookahead tokens
     * @param int
     * @return array
     */
    public function yy_get_expected_tokens($token)
    {
        $state = $this->yystack[$this->yyidx]->stateno;
        $expected = self::$yyExpectedTokens[$state];
        if (in_array($token, self::$yyExpectedTokens[$state], true)) {
            return $expected;
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done++ == 100) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // too much recursion prevents proper detection
                        // so give up
                        return array_unique($expected);
                    }
                    $yyruleno = $yyact - self::YYNSTATE;
                    $this->yyidx -= self::$yyRuleInfo[$yyruleno]['rhs'];
                    $nextstate = $this->yy_find_reduce_action(
                        $this->yystack[$this->yyidx]->stateno,
                        self::$yyRuleInfo[$yyruleno]['lhs']);
                    if (isset(self::$yyExpectedTokens[$nextstate])) {
                        $expected += self::$yyExpectedTokens[$nextstate];
                            if (in_array($token,
                                  self::$yyExpectedTokens[$nextstate], true)) {
                            $this->yyidx = $yyidx;
                            $this->yystack = $stack;

                            return array_unique($expected);
                        }
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx++;
                        $x = new PHP_LexerGenerator_Regex_yyStackEntry;
                        $x->stateno = $nextstate;
                        $x->major = self::$yyRuleInfo[$yyruleno]['lhs'];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate == self::YYNSTATE + self::YYNRULE + 1) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // the last token was just ignored, we can't accept
                        // by ignoring input, this is in essence ignoring a
                        // syntax error!
                        return array_unique($expected);
                    } elseif ($nextstate === self::YY_NO_ACTION) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // input accepted, but not shifted (I guess)
                        return $expected;
                    } else {
                        $yyact = $nextstate;
                    }
                } while (true);
            }
            break;
        } while (true);

        return array_unique($expected);
    }

    /**
     * Based on the parser state and current parser stack, determine whether
     * the lookahead token is possible.
     *
     * The parser will convert the token value to an error token if not.  This
     * catches some unusual edge cases where the parser would fail.
     * @param int
     * @return bool
     */
    public function yy_is_expected_token($token)
    {
        if ($token === 0) {
            return true; // 0 is not part of this
        }
        $state = $this->yystack[$this->yyidx]->stateno;
        if (in_array($token, self::$yyExpectedTokens[$state], true)) {
            return true;
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done++ == 100) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // too much recursion prevents proper detection
                        // so give up
                        return true;
                    }
                    $yyruleno = $yyact - self::YYNSTATE;
                    $this->yyidx -= self::$yyRuleInfo[$yyruleno]['rhs'];
                    $nextstate = $this->yy_find_reduce_action(
                        $this->yystack[$this->yyidx]->stateno,
                        self::$yyRuleInfo[$yyruleno]['lhs']);
                    if (isset(self::$yyExpectedTokens[$nextstate]) &&
                          in_array($token, self::$yyExpectedTokens[$nextstate], true)) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;

                        return true;
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx++;
                        $x = new PHP_LexerGenerator_Regex_yyStackEntry;
                        $x->stateno = $nextstate;
                        $x->major = self::$yyRuleInfo[$yyruleno]['lhs'];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate == self::YYNSTATE + self::YYNRULE + 1) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        if (!$token) {
                            // end of input: this is valid
                            return true;
                        }
                        // the last token was just ignored, we can't accept
                        // by ignoring input, this is in essence ignoring a
                        // syntax error!
                        return false;
                    } elseif ($nextstate === self::YY_NO_ACTION) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // input accepted, but not shifted (I guess)
                        return true;
                    } else {
                        $yyact = $nextstate;
                    }
                } while (true);
            }
            break;
        } while (true);
        $this->yyidx = $yyidx;
        $this->yystack = $stack;

        return true;
    }

    /**
     * Find the appropriate action for a parser given the terminal
     * look-ahead token iLookAhead.
     *
     * If the look-ahead token is YYNOCODE, then check to see if the action is
     * independent of the look-ahead.  If it is, return the action, otherwise
     * return YY_NO_ACTION.
     * @param int The look-ahead token
     */
    public function yy_find_shift_action($iLookAhead)
    {
        $stateno = $this->yystack[$this->yyidx]->stateno;

        /* if ($this->yyidx < 0) return self::YY_NO_ACTION;  */
        if (!isset(self::$yy_shift_ofst[$stateno])) {
            // no shift actions
            return self::$yy_default[$stateno];
        }
        $i = self::$yy_shift_ofst[$stateno];
        if ($i === self::YY_SHIFT_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB ||
              self::$yy_lookahead[$i] != $iLookAhead) {
            if (count(self::$yyFallback) && $iLookAhead < count(self::$yyFallback)
                   && ($iFallback = self::$yyFallback[$iLookAhead]) != 0) {
                if (self::$yyTraceFILE) {
                    fwrite(self::$yyTraceFILE, self::$yyTracePrompt . "FALLBACK " .
                        self::$yyTokenName[$iLookAhead] . " => " .
                        self::$yyTokenName[$iFallback] . "\n");
                }

                return $this->yy_find_shift_action($iFallback);
            }

            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    /**
     * Find the appropriate action for a parser given the non-terminal
     * look-ahead token $iLookAhead.
     *
     * If the look-ahead token is self::YYNOCODE, then check to see if the action is
     * independent of the look-ahead.  If it is, return the action, otherwise
     * return self::YY_NO_ACTION.
     * @param int Current state number
     * @param int The look-ahead token
     */
    public function yy_find_reduce_action($stateno, $iLookAhead)
    {
        /* $stateno = $this->yystack[$this->yyidx]->stateno; */

        if (!isset(self::$yy_reduce_ofst[$stateno])) {
            return self::$yy_default[$stateno];
        }
        $i = self::$yy_reduce_ofst[$stateno];
        if ($i == self::YY_REDUCE_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB ||
              self::$yy_lookahead[$i] != $iLookAhead) {
            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    /**
     * Perform a shift action.
     * @param int The new state to shift in
     * @param int The major token to shift in
     * @param mixed the minor token to shift in
     */
    public function yy_shift($yyNewState, $yyMajor, $yypMinor)
    {
        $this->yyidx++;
        if ($this->yyidx >= self::YYSTACKDEPTH) {
            $this->yyidx--;
            if (self::$yyTraceFILE) {
                fprintf(self::$yyTraceFILE, "%sStack Overflow!\n", self::$yyTracePrompt);
            }
            while ($this->yyidx >= 0) {
                $this->yy_pop_parser_stack();
            }
            /* Here code is inserted which will execute if the parser
            ** stack ever overflows */

            return;
        }
        $yytos = new PHP_LexerGenerator_Regex_yyStackEntry;
        $yytos->stateno = $yyNewState;
        $yytos->major = $yyMajor;
        $yytos->minor = $yypMinor;
        array_push($this->yystack, $yytos);
        if (self::$yyTraceFILE && $this->yyidx > 0) {
            fprintf(self::$yyTraceFILE, "%sShift %d\n", self::$yyTracePrompt,
                $yyNewState);
            fprintf(self::$yyTraceFILE, "%sStack:", self::$yyTracePrompt);
            for ($i = 1; $i <= $this->yyidx; $i++) {
                fprintf(self::$yyTraceFILE, " %s",
                    self::$yyTokenName[$this->yystack[$i]->major]);
            }
            fwrite(self::$yyTraceFILE,"\n");
        }
    }

    /**
     * The following table contains information about every rule that
     * is used during the reduce.
     *
     * <pre>
     * array(
     *  array(
     *   int $lhs;         Symbol on the left-hand side of the rule
     *   int $nrhs;     Number of right-hand side symbols in the rule
     *  ),...
     * );
     * </pre>
     */
    public static $yyRuleInfo = array(
  array( 'lhs' => 29, 'rhs' => 1 ),
  array( 'lhs' => 30, 'rhs' => 3 ),
  array( 'lhs' => 30, 'rhs' => 2 ),
  array( 'lhs' => 30, 'rhs' => 2 ),
  array( 'lhs' => 30, 'rhs' => 1 ),
  array( 'lhs' => 30, 'rhs' => 3 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 1 ),
  array( 'lhs' => 31, 'rhs' => 2 ),
  array( 'lhs' => 31, 'rhs' => 2 ),
  array( 'lhs' => 31, 'rhs' => 2 ),
  array( 'lhs' => 31, 'rhs' => 2 ),
  array( 'lhs' => 31, 'rhs' => 2 ),
  array( 'lhs' => 31, 'rhs' => 2 ),
  array( 'lhs' => 31, 'rhs' => 2 ),
  array( 'lhs' => 31, 'rhs' => 2 ),
  array( 'lhs' => 31, 'rhs' => 2 ),
  array( 'lhs' => 31, 'rhs' => 2 ),
  array( 'lhs' => 31, 'rhs' => 2 ),
  array( 'lhs' => 33, 'rhs' => 3 ),
  array( 'lhs' => 33, 'rhs' => 4 ),
  array( 'lhs' => 33, 'rhs' => 4 ),
  array( 'lhs' => 33, 'rhs' => 5 ),
  array( 'lhs' => 43, 'rhs' => 1 ),
  array( 'lhs' => 43, 'rhs' => 1 ),
  array( 'lhs' => 43, 'rhs' => 3 ),
  array( 'lhs' => 43, 'rhs' => 3 ),
  array( 'lhs' => 43, 'rhs' => 3 ),
  array( 'lhs' => 43, 'rhs' => 1 ),
  array( 'lhs' => 43, 'rhs' => 1 ),
  array( 'lhs' => 43, 'rhs' => 2 ),
  array( 'lhs' => 43, 'rhs' => 2 ),
  array( 'lhs' => 43, 'rhs' => 2 ),
  array( 'lhs' => 43, 'rhs' => 4 ),
  array( 'lhs' => 43, 'rhs' => 4 ),
  array( 'lhs' => 43, 'rhs' => 4 ),
  array( 'lhs' => 43, 'rhs' => 4 ),
  array( 'lhs' => 43, 'rhs' => 2 ),
  array( 'lhs' => 43, 'rhs' => 2 ),
  array( 'lhs' => 32, 'rhs' => 1 ),
  array( 'lhs' => 32, 'rhs' => 2 ),
  array( 'lhs' => 32, 'rhs' => 1 ),
  array( 'lhs' => 32, 'rhs' => 2 ),
  array( 'lhs' => 32, 'rhs' => 1 ),
  array( 'lhs' => 32, 'rhs' => 2 ),
  array( 'lhs' => 32, 'rhs' => 1 ),
  array( 'lhs' => 32, 'rhs' => 2 ),
  array( 'lhs' => 32, 'rhs' => 1 ),
  array( 'lhs' => 32, 'rhs' => 2 ),
  array( 'lhs' => 32, 'rhs' => 1 ),
  array( 'lhs' => 32, 'rhs' => 2 ),
  array( 'lhs' => 32, 'rhs' => 2 ),
  array( 'lhs' => 32, 'rhs' => 3 ),
  array( 'lhs' => 32, 'rhs' => 2 ),
  array( 'lhs' => 32, 'rhs' => 3 ),
  array( 'lhs' => 32, 'rhs' => 2 ),
  array( 'lhs' => 32, 'rhs' => 3 ),
  array( 'lhs' => 32, 'rhs' => 2 ),
  array( 'lhs' => 32, 'rhs' => 3 ),
  array( 'lhs' => 32, 'rhs' => 2 ),
  array( 'lhs' => 32, 'rhs' => 3 ),
  array( 'lhs' => 32, 'rhs' => 2 ),
  array( 'lhs' => 32, 'rhs' => 3 ),
  array( 'lhs' => 34, 'rhs' => 3 ),
  array( 'lhs' => 34, 'rhs' => 5 ),
  array( 'lhs' => 35, 'rhs' => 4 ),
  array( 'lhs' => 35, 'rhs' => 5 ),
  array( 'lhs' => 42, 'rhs' => 7 ),
  array( 'lhs' => 42, 'rhs' => 6 ),
  array( 'lhs' => 42, 'rhs' => 4 ),
  array( 'lhs' => 42, 'rhs' => 5 ),
  array( 'lhs' => 42, 'rhs' => 4 ),
  array( 'lhs' => 42, 'rhs' => 5 ),
  array( 'lhs' => 36, 'rhs' => 4 ),
  array( 'lhs' => 36, 'rhs' => 4 ),
  array( 'lhs' => 37, 'rhs' => 4 ),
  array( 'lhs' => 37, 'rhs' => 4 ),
  array( 'lhs' => 38, 'rhs' => 4 ),
  array( 'lhs' => 38, 'rhs' => 5 ),
  array( 'lhs' => 38, 'rhs' => 3 ),
  array( 'lhs' => 38, 'rhs' => 4 ),
  array( 'lhs' => 39, 'rhs' => 4 ),
  array( 'lhs' => 40, 'rhs' => 3 ),
  array( 'lhs' => 41, 'rhs' => 3 ),
    );

    /**
     * The following table contains a mapping of reduce action to method name
     * that handles the reduction.
     *
     * If a rule is not set, it has no handler.
     */
    public static $yyReduceMap = array(
        0 => 0,
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        6 => 4,
        7 => 4,
        9 => 4,
        10 => 4,
        12 => 4,
        13 => 4,
        14 => 4,
        15 => 4,
        16 => 4,
        5 => 5,
        17 => 17,
        18 => 17,
        20 => 17,
        21 => 17,
        23 => 17,
        24 => 17,
        25 => 17,
        26 => 17,
        27 => 17,
        28 => 28,
        29 => 29,
        30 => 30,
        31 => 31,
        32 => 32,
        48 => 32,
        50 => 32,
        33 => 33,
        54 => 33,
        34 => 34,
        35 => 35,
        36 => 36,
        37 => 37,
        56 => 37,
        38 => 38,
        58 => 38,
        39 => 39,
        64 => 39,
        40 => 40,
        66 => 40,
        41 => 41,
        60 => 41,
        62 => 41,
        42 => 42,
        43 => 43,
        44 => 44,
        45 => 45,
        46 => 46,
        68 => 46,
        47 => 47,
        70 => 47,
        49 => 49,
        51 => 49,
        52 => 52,
        53 => 53,
        55 => 55,
        57 => 57,
        59 => 59,
        61 => 61,
        63 => 61,
        65 => 65,
        67 => 67,
        69 => 69,
        71 => 71,
        72 => 72,
        73 => 73,
        74 => 74,
        75 => 75,
        76 => 76,
        77 => 77,
        78 => 78,
        79 => 79,
        80 => 80,
        84 => 80,
        81 => 81,
        82 => 82,
        83 => 83,
        85 => 85,
        86 => 86,
        87 => 87,
        88 => 88,
        89 => 89,
        90 => 90,
        91 => 91,
        92 => 92,
    );
    /* Beginning here are the reduction cases.  A typical example
    ** follows:
    **  #line <lineno> <grammarfile>
    **   function yy_r0($yymsp){ ... }           // User supplied code
    **  #line <lineno> <thisfile>
    */
#line 47 "Parser.y"
    public function yy_r0()
    {
    $this->yystack[$this->yyidx + 0]->minor->string = str_replace('"', '\\"', $this->yystack[$this->yyidx + 0]->minor->string);
    $x = $this->yystack[$this->yyidx + 0]->minor->metadata;
    $x['subpatterns'] = $this->_subpatterns;
    $this->yystack[$this->yyidx + 0]->minor->metadata = $x;
    $this->_subpatterns = 0;
    $this->result = $this->yystack[$this->yyidx + 0]->minor;
    }
#line 1259 "Parser.php"
#line 56 "Parser.y"
    public function yy_r1()
    {
    throw new PHP_LexerGenerator_Exception('Cannot include start match "' .
        $this->yystack[$this->yyidx + -2]->minor . '" or end match "' . $this->yystack[$this->yyidx + 0]->minor . '"');
    }
#line 1265 "Parser.php"
#line 60 "Parser.y"
    public function yy_r2()
    {
    throw new PHP_LexerGenerator_Exception('Cannot include start match "' .
        B . '"');
    }
#line 1271 "Parser.php"
#line 64 "Parser.y"
    public function yy_r3()
    {
    throw new PHP_LexerGenerator_Exception('Cannot include end match "' . $this->yystack[$this->yyidx + 0]->minor . '"');
    }
#line 1276 "Parser.php"
#line 67 "Parser.y"
    public function yy_r4(){$this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;    }
#line 1279 "Parser.php"
#line 68 "Parser.y"
    public function yy_r5()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -2]->minor->string . '|' . $this->yystack[$this->yyidx + 0]->minor->string, array(
        'pattern' => $this->yystack[$this->yyidx + -2]->minor['pattern'] . '|' . $this->yystack[$this->yyidx + 0]->minor['pattern']));
    }
#line 1285 "Parser.php"
#line 84 "Parser.y"
    public function yy_r17()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -1]->minor->string . $this->yystack[$this->yyidx + 0]->minor->string, array(
        'pattern' => $this->yystack[$this->yyidx + -1]->minor['pattern'] . $this->yystack[$this->yyidx + 0]->minor['pattern']));
    }
#line 1291 "Parser.php"
#line 123 "Parser.y"
    public function yy_r28()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('[' . $this->yystack[$this->yyidx + -1]->minor->string . ']', array(
        'pattern' => '[' . $this->yystack[$this->yyidx + -1]->minor['pattern'] . ']'));
    }
#line 1297 "Parser.php"
#line 127 "Parser.y"
    public function yy_r29()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('[^' . $this->yystack[$this->yyidx + -1]->minor->string . ']', array(
        'pattern' => '[^' . $this->yystack[$this->yyidx + -1]->minor['pattern'] . ']'));
    }
#line 1303 "Parser.php"
#line 131 "Parser.y"
    public function yy_r30()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('[' . $this->yystack[$this->yyidx + -2]->minor->string . ']' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => '[' . $this->yystack[$this->yyidx + -2]->minor['pattern'] . ']' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1309 "Parser.php"
#line 135 "Parser.y"
    public function yy_r31()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('[^' . $this->yystack[$this->yyidx + -2]->minor->string . ']' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => '[^' . $this->yystack[$this->yyidx + -2]->minor['pattern'] . ']' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1315 "Parser.php"
#line 140 "Parser.y"
    public function yy_r32()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1321 "Parser.php"
#line 144 "Parser.y"
    public function yy_r33()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('\\\\' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1327 "Parser.php"
#line 148 "Parser.y"
    public function yy_r34()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('\\\\' . $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1333 "Parser.php"
#line 152 "Parser.y"
    public function yy_r35()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1339 "Parser.php"
#line 156 "Parser.y"
    public function yy_r36()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -2]->minor . '-\\\\' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1345 "Parser.php"
#line 160 "Parser.y"
    public function yy_r37()
    {
    if (((int) substr($this->yystack[$this->yyidx + 0]->minor, 1)) > $this->_subpatterns) {
        throw new PHP_LexerGenerator_Exception('Back-reference refers to non-existent ' .
            'sub-pattern ' . substr($this->yystack[$this->yyidx + 0]->minor, 1));
    }
    $this->yystack[$this->yyidx + 0]->minor = substr($this->yystack[$this->yyidx + 0]->minor, 1);
    // adjust back-reference for containing ()
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('\\\\' . ($this->yystack[$this->yyidx + 0]->minor + $this->_patternIndex), array(
        'pattern' => '\\' . ($this->_updatePattern ? ($this->yystack[$this->yyidx + 0]->minor + $this->_patternIndex) : $this->yystack[$this->yyidx + 0]->minor)));
    }
#line 1357 "Parser.php"
#line 170 "Parser.y"
    public function yy_r38()
    {
    if (((int) substr($this->yystack[$this->yyidx + 0]->minor, 1)) > $this->_subpatterns) {
        throw new PHP_LexerGenerator_Exception($this->yystack[$this->yyidx + 0]->minor . ' will be interpreted as an invalid' .
            ' back-reference, use "\\0' . substr($this->yystack[$this->yyidx + 0]->minor, 1) . ' for octal');
    }
    $this->yystack[$this->yyidx + 0]->minor = substr($this->yystack[$this->yyidx + 0]->minor, 1);
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('\\\\' . ($this->yystack[$this->yyidx + 0]->minor + $this->_patternIndex), array(
        'pattern' => '\\' . ($this->_updatePattern ? ($this->yystack[$this->yyidx + 0]->minor + $this->_patternIndex) : $this->yystack[$this->yyidx + 0]->minor)));
    }
#line 1368 "Parser.php"
#line 179 "Parser.y"
    public function yy_r39()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -1]->minor->string . '\\' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -1]->minor['pattern'] . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1374 "Parser.php"
#line 183 "Parser.y"
    public function yy_r40()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -1]->minor->string . '\\\\' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -1]->minor['pattern'] . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1380 "Parser.php"
#line 187 "Parser.y"
    public function yy_r41()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -1]->minor->string . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -1]->minor['pattern'] . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1386 "Parser.php"
#line 191 "Parser.y"
    public function yy_r42()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -3]->minor->string . '\\\\' . $this->yystack[$this->yyidx + -2]->minor . '-\\' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -3]->minor['pattern'] . $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1392 "Parser.php"
#line 195 "Parser.y"
    public function yy_r43()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -3]->minor->string . '\\\\' . $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -3]->minor['pattern'] . $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1398 "Parser.php"
#line 199 "Parser.y"
    public function yy_r44()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -3]->minor->string . $this->yystack[$this->yyidx + -2]->minor . '-\\\\' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -3]->minor['pattern'] . $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1404 "Parser.php"
#line 203 "Parser.y"
    public function yy_r45()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -3]->minor->string . $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -3]->minor['pattern'] . $this->yystack[$this->yyidx + -2]->minor . '-' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1410 "Parser.php"
#line 207 "Parser.y"
    public function yy_r46()
    {
    if (((int) substr($this->yystack[$this->yyidx + 0]->minor, 1)) > $this->_subpatterns) {
        throw new PHP_LexerGenerator_Exception('Back-reference refers to non-existent ' .
            'sub-pattern ' . substr($this->yystack[$this->yyidx + 0]->minor, 1));
    }
    $this->yystack[$this->yyidx + 0]->minor = substr($this->yystack[$this->yyidx + 0]->minor, 1);
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -1]->minor->string . '\\\\' . ($this->yystack[$this->yyidx + 0]->minor + $this->_patternIndex), array(
        'pattern' => $this->yystack[$this->yyidx + -1]->minor['pattern'] . '\\' . ($this->_updatePattern ? ($this->yystack[$this->yyidx + 0]->minor + $this->_patternIndex) : $this->yystack[$this->yyidx + 0]->minor)));
    }
#line 1421 "Parser.php"
#line 216 "Parser.y"
    public function yy_r47()
    {
    if (((int) substr($this->yystack[$this->yyidx + 0]->minor, 1)) > $this->_subpatterns) {
        throw new PHP_LexerGenerator_Exception($this->yystack[$this->yyidx + 0]->minor . ' will be interpreted as an invalid' .
            ' back-reference, use "\\0' . substr($this->yystack[$this->yyidx + 0]->minor, 1) . ' for octal');
    }
    $this->yystack[$this->yyidx + 0]->minor = substr($this->yystack[$this->yyidx + 0]->minor, 1);
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -1]->minor->string . '\\\\' . ($this->yystack[$this->yyidx + 0]->minor + $this->_patternIndex), array(
        'pattern' => $this->yystack[$this->yyidx + -1]->minor['pattern'] . '\\' . ($this->_updatePattern ? ($this->yystack[$this->yyidx + 0]->minor + $this->_patternIndex) : $this->yystack[$this->yyidx + 0]->minor)));
    }
#line 1432 "Parser.php"
#line 230 "Parser.y"
    public function yy_r49()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1438 "Parser.php"
#line 242 "Parser.y"
    public function yy_r52()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('\\' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1444 "Parser.php"
#line 246 "Parser.y"
    public function yy_r53()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('\\' . $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1450 "Parser.php"
#line 254 "Parser.y"
    public function yy_r55()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('\\\\' . $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1456 "Parser.php"
#line 268 "Parser.y"
    public function yy_r57()
    {
    if (((int) substr($this->yystack[$this->yyidx + -1]->minor, 1)) > $this->_subpatterns) {
        throw new PHP_LexerGenerator_Exception('Back-reference refers to non-existent ' .
            'sub-pattern ' . substr($this->yystack[$this->yyidx + -1]->minor, 1));
    }
    $this->yystack[$this->yyidx + -1]->minor = substr($this->yystack[$this->yyidx + -1]->minor, 1);
    // adjust back-reference for containing ()
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('\\\\' . ($this->yystack[$this->yyidx + -1]->minor + $this->_patternIndex) . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => '\\' . ($this->_updatePattern ? ($this->yystack[$this->yyidx + -1]->minor + $this->_patternIndex) : $this->yystack[$this->yyidx + -1]->minor) . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1468 "Parser.php"
#line 287 "Parser.y"
    public function yy_r59()
    {
    if (((int) substr($this->yystack[$this->yyidx + -1]->minor, 1)) > $this->_subpatterns) {
        throw new PHP_LexerGenerator_Exception($this->yystack[$this->yyidx + -1]->minor . ' will be interpreted as an invalid' .
            ' back-reference, use "\\0' . substr($this->yystack[$this->yyidx + -1]->minor, 1) . ' for octal');
    }
    $this->yystack[$this->yyidx + -1]->minor = substr($this->yystack[$this->yyidx + -1]->minor, 1);
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('\\\\' . ($this->yystack[$this->yyidx + -1]->minor + $this->_patternIndex) . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => '\\' . ($this->_updatePattern ? ($this->yystack[$this->yyidx + -1]->minor + $this->_patternIndex) : $this->yystack[$this->yyidx + -1]->minor) . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1479 "Parser.php"
#line 300 "Parser.y"
    public function yy_r61()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -2]->minor->string . $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -2]->minor['pattern'] . $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1485 "Parser.php"
#line 316 "Parser.y"
    public function yy_r65()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -2]->minor->string . '\\' . $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -2]->minor['pattern'] . $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1491 "Parser.php"
#line 324 "Parser.y"
    public function yy_r67()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -2]->minor->string . '\\\\' . $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -2]->minor['pattern'] . $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1497 "Parser.php"
#line 337 "Parser.y"
    public function yy_r69()
    {
    if (((int) substr($this->yystack[$this->yyidx + -1]->minor, 1)) > $this->_subpatterns) {
        throw new PHP_LexerGenerator_Exception('Back-reference refers to non-existent ' .
            'sub-pattern ' . substr($this->yystack[$this->yyidx + -1]->minor, 1));
    }
    $this->yystack[$this->yyidx + -1]->minor = substr($this->yystack[$this->yyidx + -1]->minor, 1);
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -2]->minor->string . '\\\\' . ($this->yystack[$this->yyidx + -1]->minor + $this->_patternIndex) . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -2]->minor['pattern'] . '\\' . ($this->_updatePattern ? ($this->yystack[$this->yyidx + -1]->minor + $this->_patternIndex) : $this->yystack[$this->yyidx + -1]->minor) . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1508 "Parser.php"
#line 355 "Parser.y"
    public function yy_r71()
    {
    if (((int) substr($this->yystack[$this->yyidx + -1]->minor, 1)) > $this->_subpatterns) {
        throw new PHP_LexerGenerator_Exception($this->yystack[$this->yyidx + -1]->minor . ' will be interpreted as an invalid' .
            ' back-reference, use "\\0' . substr($this->yystack[$this->yyidx + -1]->minor, 1) . ' for octal');
    }
    $this->yystack[$this->yyidx + -1]->minor = substr($this->yystack[$this->yyidx + -1]->minor, 1);
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken($this->yystack[$this->yyidx + -2]->minor->string . '\\\\' . ($this->yystack[$this->yyidx + -1]->minor + $this->_patternIndex) . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => $this->yystack[$this->yyidx + -2]->minor['pattern'] . '\\' . ($this->_updatePattern ? ($this->yystack[$this->yyidx + -1]->minor + $this->_patternIndex) : $this->yystack[$this->yyidx + -1]->minor) . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1519 "Parser.php"
#line 365 "Parser.y"
    public function yy_r72()
    {
    throw new PHP_LexerGenerator_Exception('Error: cannot set preg options directly with "' .
        $this->yystack[$this->yyidx + -2]->minor . $this->yystack[$this->yyidx + -1]->minor . $this->yystack[$this->yyidx + 0]->minor . '"');
    }
#line 1525 "Parser.php"
#line 369 "Parser.y"
    public function yy_r73()
    {
    throw new PHP_LexerGenerator_Exception('Error: cannot set preg options directly with "' .
        $this->yystack[$this->yyidx + -4]->minor . $this->yystack[$this->yyidx + -3]->minor . $this->yystack[$this->yyidx + -2]->minor . $this->yystack[$this->yyidx + -1]->minor['pattern'] . $this->yystack[$this->yyidx + 0]->minor . '"');
    }
#line 1531 "Parser.php"
#line 374 "Parser.y"
    public function yy_r74()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(?:' . $this->yystack[$this->yyidx + -1]->minor->string . ')', array(
        'pattern' => '(?:' . $this->yystack[$this->yyidx + -1]->minor['pattern'] . ')'));
    }
#line 1537 "Parser.php"
#line 378 "Parser.y"
    public function yy_r75()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(?:' . $this->yystack[$this->yyidx + -2]->minor->string . ')' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => '(?:' . $this->yystack[$this->yyidx + -2]->minor['pattern'] . ')' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1543 "Parser.php"
#line 383 "Parser.y"
    public function yy_r76()
    {
    if ($this->yystack[$this->yyidx + -4]->minor != 'R') {
        if (!preg_match('/[1-9][0-9]*/', $this->yystack[$this->yyidx + -4]->minor)) {
            throw new PHP_LexerGenerator_Exception('Invalid sub-pattern conditional: "(?(' . $this->yystack[$this->yyidx + -4]->minor . ')"');
        }
        if ($this->yystack[$this->yyidx + -4]->minor > $this->_subpatterns) {
            throw new PHP_LexerGenerator_Exception('sub-pattern conditional . "' . $this->yystack[$this->yyidx + -4]->minor . '" refers to non-existent sub-pattern');
        }
    } else {
        throw new PHP_LexerGenerator_Exception('Recursive conditional (?(' . $this->yystack[$this->yyidx + -4]->minor . ')" cannot work in this lexer');
    }
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(?(' . $this->yystack[$this->yyidx + -4]->minor . ')' . $this->yystack[$this->yyidx + -2]->minor->string . ')' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => '(?(' . $this->yystack[$this->yyidx + -4]->minor . ')' . $this->yystack[$this->yyidx + -2]->minor['pattern'] . ')' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1559 "Parser.php"
#line 397 "Parser.y"
    public function yy_r77()
    {
    if ($this->yystack[$this->yyidx + -3]->minor != 'R') {
        if (!preg_match('/[1-9][0-9]*/', $this->yystack[$this->yyidx + -3]->minor)) {
            throw new PHP_LexerGenerator_Exception('Invalid sub-pattern conditional: "(?(' . $this->yystack[$this->yyidx + -3]->minor . ')"');
        }
        if ($this->yystack[$this->yyidx + -3]->minor > $this->_subpatterns) {
            throw new PHP_LexerGenerator_Exception('sub-pattern conditional . "' . $this->yystack[$this->yyidx + -3]->minor . '" refers to non-existent sub-pattern');
        }
    } else {
        throw new PHP_LexerGenerator_Exception('Recursive conditional (?(' . $this->yystack[$this->yyidx + -3]->minor . ')" cannot work in this lexer');
    }
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(?(' . $this->yystack[$this->yyidx + -3]->minor . ')' . $this->yystack[$this->yyidx + -1]->minor->string . ')', array(
        'pattern' => '(?(' . $this->yystack[$this->yyidx + -3]->minor . ')' . $this->yystack[$this->yyidx + -1]->minor['pattern'] . ')'));
    }
#line 1575 "Parser.php"
#line 411 "Parser.y"
    public function yy_r78()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(?' . $this->yystack[$this->yyidx + -2]->minor->string . $this->yystack[$this->yyidx + -1]->minor->string . ')', array(
        'pattern' => '(?' . $this->yystack[$this->yyidx + -2]->minor['pattern'] . $this->yystack[$this->yyidx + -1]->minor['pattern'] . ')'));
    }
#line 1581 "Parser.php"
#line 415 "Parser.y"
    public function yy_r79()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(?' . $this->yystack[$this->yyidx + -3]->minor->string . $this->yystack[$this->yyidx + -2]->minor->string . ')' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => '(?' . $this->yystack[$this->yyidx + -3]->minor['pattern'] . $this->yystack[$this->yyidx + -2]->minor['pattern'] . ')' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1587 "Parser.php"
#line 419 "Parser.y"
    public function yy_r80()
    {
    throw new PHP_LexerGenerator_Exception('Look-behind assertions cannot be used: "(?<=' .
        $this->yystack[$this->yyidx + -1]->minor['pattern'] . ')');
    }
#line 1593 "Parser.php"
#line 423 "Parser.y"
    public function yy_r81()
    {
    throw new PHP_LexerGenerator_Exception('Look-behind assertions cannot be used: "(?<=' .
        $this->yystack[$this->yyidx + -2]->minor['pattern'] . ')');
    }
#line 1599 "Parser.php"
#line 428 "Parser.y"
    public function yy_r82()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(?=' . $this->yystack[$this->yyidx + -1]->minor->string . ')', array(
        'pattern'=> '(?=' . $this->yystack[$this->yyidx + -1]->minor['pattern'] . ')'));
    }
#line 1605 "Parser.php"
#line 432 "Parser.y"
    public function yy_r83()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(?!' . $this->yystack[$this->yyidx + -1]->minor->string . ')', array(
        'pattern' => '(?!' . $this->yystack[$this->yyidx + -1]->minor['pattern'] . ')'));
    }
#line 1611 "Parser.php"
#line 441 "Parser.y"
    public function yy_r85()
    {
    throw new PHP_LexerGenerator_Exception('Look-behind assertions cannot be used: "(?<!' .
        $this->yystack[$this->yyidx + -1]->minor['pattern'] . ')');
    }
#line 1617 "Parser.php"
#line 446 "Parser.y"
    public function yy_r86()
    {
    throw new PHP_LexerGenerator_Exception('Cannot use named sub-patterns: "(' .
        $this->yystack[$this->yyidx + -2]->minor['pattern'] . ')');
    }
#line 1623 "Parser.php"
#line 450 "Parser.y"
    public function yy_r87()
    {
    throw new PHP_LexerGenerator_Exception('Cannot use named sub-patterns: "(' .
        $this->yystack[$this->yyidx + -3]->minor['pattern'] . ')');
    }
#line 1629 "Parser.php"
#line 454 "Parser.y"
    public function yy_r88()
    {
    $this->_subpatterns++;
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(' . $this->yystack[$this->yyidx + -1]->minor->string . ')', array(
        'pattern' => '(' . $this->yystack[$this->yyidx + -1]->minor['pattern'] . ')'));
    }
#line 1636 "Parser.php"
#line 459 "Parser.y"
    public function yy_r89()
    {
    $this->_subpatterns++;
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(' . $this->yystack[$this->yyidx + -2]->minor->string . ')' . $this->yystack[$this->yyidx + 0]->minor, array(
        'pattern' => '(' . $this->yystack[$this->yyidx + -2]->minor['pattern'] . ')' . $this->yystack[$this->yyidx + 0]->minor));
    }
#line 1643 "Parser.php"
#line 465 "Parser.y"
    public function yy_r90()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(?>' . $this->yystack[$this->yyidx + -1]->minor->string . ')', array(
        'pattern' => '(?>' . $this->yystack[$this->yyidx + -1]->minor['pattern'] . ')'));
    }
#line 1649 "Parser.php"
#line 470 "Parser.y"
    public function yy_r91()
    {
    $this->_retvalue = new PHP_LexerGenerator_ParseryyToken('(' . $this->yystack[$this->yyidx + -1]->minor->string . ')', array(
        'pattern' => '(' . $this->yystack[$this->yyidx + -1]->minor['pattern'] . ')'));
    }
#line 1655 "Parser.php"
#line 475 "Parser.y"
    public function yy_r92()
    {
    throw new Exception('(?R) cannot work in this lexer');
    }
#line 1660 "Parser.php"

    /**
     * placeholder for the left hand side in a reduce operation.
     *
     * For a parser with a rule like this:
     * <pre>
     * rule(A) ::= B. { A = 1; }
     * </pre>
     *
     * The parser will translate to something like:
     *
     * <code>
     * function yy_r0(){$this->_retvalue = 1;}
     * </code>
     */
    private $_retvalue;

    /**
     * Perform a reduce action and the shift that must immediately
     * follow the reduce.
     *
     * For a rule such as:
     *
     * <pre>
     * A ::= B blah C. { dosomething(); }
     * </pre>
     *
     * This function will first call the action, if any, ("dosomething();" in our
     * example), and then it will pop three states from the stack,
     * one for each entry on the right-hand side of the expression
     * (B, blah, and C in our example rule), and then push the result of the action
     * back on to the stack with the resulting state reduced to (as described in the .out
     * file)
     * @param int Number of the rule by which to reduce
     */
    public function yy_reduce($yyruleno)
    {
        //int $yygoto;                     /* The next state */
        //int $yyact;                      /* The next action */
        //mixed $yygotominor;        /* The LHS of the rule reduced */
        //PHP_LexerGenerator_Regex_yyStackEntry $yymsp;            /* The top of the parser's stack */
        //int $yysize;                     /* Amount to pop the stack */
        if (self::$yyTraceFILE && $yyruleno >= 0
              && $yyruleno < count(self::$yyRuleName)) {
            fprintf(self::$yyTraceFILE, "%sReduce (%d) [%s].\n",
                self::$yyTracePrompt, $yyruleno,
                self::$yyRuleName[$yyruleno]);
        }

        $this->_retvalue = $yy_lefthand_side = null;
        if (isset(self::$yyReduceMap[$yyruleno])) {
            // call the action
            $this->_retvalue = null;
            $this->{'yy_r' . self::$yyReduceMap[$yyruleno]}();
            $yy_lefthand_side = $this->_retvalue;
        }
        $yygoto = self::$yyRuleInfo[$yyruleno]['lhs'];
        $yysize = self::$yyRuleInfo[$yyruleno]['rhs'];
        $this->yyidx -= $yysize;
        for ($i = $yysize; $i; $i--) {
            // pop all of the right-hand side parameters
            array_pop($this->yystack);
        }
        $yyact = $this->yy_find_reduce_action($this->yystack[$this->yyidx]->stateno, $yygoto);
        if ($yyact < self::YYNSTATE) {
            /* If we are not debugging and the reduce action popped at least
            ** one element off the stack, then we can push the new element back
            ** onto the stack here, and skip the stack overflow test in yy_shift().
            ** That gives a significant speed improvement. */
            if (!self::$yyTraceFILE && $yysize) {
                $this->yyidx++;
                $x = new PHP_LexerGenerator_Regex_yyStackEntry;
                $x->stateno = $yyact;
                $x->major = $yygoto;
                $x->minor = $yy_lefthand_side;
                $this->yystack[$this->yyidx] = $x;
            } else {
                $this->yy_shift($yyact, $yygoto, $yy_lefthand_side);
            }
        } elseif ($yyact == self::YYNSTATE + self::YYNRULE + 1) {
            $this->yy_accept();
        }
    }

    /**
     * The following code executes when the parse fails
     *
     * Code from %parse_fail is inserted here
     */
    public function yy_parse_failed()
    {
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sFail!\n", self::$yyTracePrompt);
        } while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
        /* Here code is inserted which will be executed whenever the
        ** parser fails */
    }

    /**
     * The following code executes when a syntax error first occurs.
     *
     * %syntax_error code is inserted here
     * @param int The major type of the error token
     * @param mixed The minor type of the error token
     */
    public function yy_syntax_error($yymajor, $TOKEN)
    {
#line 6 "Parser.y"

/* ?><?php */
    // we need to add auto-escaping of all stuff that needs it for result.
    // and then validate the original regex only
    echo "Syntax Error on line " . $this->_lex->line . ": token '" .
        $this->_lex->value . "' while parsing rule:";
    foreach ($this->yystack as $entry) {
        echo $this->tokenName($entry->major) . ' ';
    }
    foreach ($this->yy_get_expected_tokens($yymajor) as $token) {
        $expect[] = self::$yyTokenName[$token];
    }
    throw new Exception('Unexpected ' . $this->tokenName($yymajor) . '(' . $TOKEN
        . '), expected one of: ' . implode(',', $expect));
#line 1788 "Parser.php"
    }

    /**
     * The following is executed when the parser accepts
     *
     * %parse_accept code is inserted here
     */
    public function yy_accept()
    {
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sAccept!\n", self::$yyTracePrompt);
        } while ($this->yyidx >= 0) {
            $stack = $this->yy_pop_parser_stack();
        }
        /* Here code is inserted which will be executed whenever the
        ** parser accepts */
    }

    /**
     * The main parser program.
     *
     * The first argument is the major token number.  The second is
     * the token value string as scanned from the input.
     *
     * @param int the token number
     * @param mixed the token value
     * @param mixed any extra arguments that should be passed to handlers
     */
    public function doParse($yymajor, $yytokenvalue)
    {
//        $yyact;            /* The parser action. */
//        $yyendofinput;     /* True if we are at the end of input */
        $yyerrorhit = 0;   /* True if yymajor has invoked an error */

        /* (re)initialize the parser, if necessary */
        if ($this->yyidx === null || $this->yyidx < 0) {
            /* if ($yymajor == 0) return; // not sure why this was here... */
            $this->yyidx = 0;
            $this->yyerrcnt = -1;
            $x = new PHP_LexerGenerator_Regex_yyStackEntry;
            $x->stateno = 0;
            $x->major = 0;
            $this->yystack = array();
            array_push($this->yystack, $x);
        }
        $yyendofinput = ($yymajor==0);

        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sInput %s\n",
                self::$yyTracePrompt, self::$yyTokenName[$yymajor]);
        }

        do {
            $yyact = $this->yy_find_shift_action($yymajor);
            if ($yymajor < self::YYERRORSYMBOL &&
                  !$this->yy_is_expected_token($yymajor)) {
                // force a syntax error
                $yyact = self::YY_ERROR_ACTION;
            }
            if ($yyact < self::YYNSTATE) {
                $this->yy_shift($yyact, $yymajor, $yytokenvalue);
                $this->yyerrcnt--;
                if ($yyendofinput && $this->yyidx >= 0) {
                    $yymajor = 0;
                } else {
                    $yymajor = self::YYNOCODE;
                }
            } elseif ($yyact < self::YYNSTATE + self::YYNRULE) {
                $this->yy_reduce($yyact - self::YYNSTATE);
            } elseif ($yyact == self::YY_ERROR_ACTION) {
                if (self::$yyTraceFILE) {
                    fprintf(self::$yyTraceFILE, "%sSyntax Error!\n",
                        self::$yyTracePrompt);
                }
                if (self::YYERRORSYMBOL) {
                    /* A syntax error has occurred.
                    ** The response to an error depends upon whether or not the
                    ** grammar defines an error token "ERROR".
                    **
                    ** This is what we do if the grammar does define ERROR:
                    **
                    **  * Call the %syntax_error function.
                    **
                    **  * Begin popping the stack until we enter a state where
                    **    it is legal to shift the error symbol, then shift
                    **    the error symbol.
                    **
                    **  * Set the error count to three.
                    **
                    **  * Begin accepting and shifting new tokens.  No new error
                    **    processing will occur until three tokens have been
                    **    shifted successfully.
                    **
                    */
                    if ($this->yyerrcnt < 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $yymx = $this->yystack[$this->yyidx]->major;
                    if ($yymx == self::YYERRORSYMBOL || $yyerrorhit) {
                        if (self::$yyTraceFILE) {
                            fprintf(self::$yyTraceFILE, "%sDiscard input token %s\n",
                                self::$yyTracePrompt, self::$yyTokenName[$yymajor]);
                        }
                        $this->yy_destructor($yymajor, $yytokenvalue);
                        $yymajor = self::YYNOCODE;
                    } else {
                        while ($this->yyidx >= 0 &&
                                 $yymx != self::YYERRORSYMBOL &&
        ($yyact = $this->yy_find_shift_action(self::YYERRORSYMBOL)) >= self::YYNSTATE
                              ){
                            $this->yy_pop_parser_stack();
                        }
                        if ($this->yyidx < 0 || $yymajor==0) {
                            $this->yy_destructor($yymajor, $yytokenvalue);
                            $this->yy_parse_failed();
                            $yymajor = self::YYNOCODE;
                        } elseif ($yymx != self::YYERRORSYMBOL) {
                            $u2 = 0;
                            $this->yy_shift($yyact, self::YYERRORSYMBOL, $u2);
                        }
                    }
                    $this->yyerrcnt = 3;
                    $yyerrorhit = 1;
                } else {
                    /* YYERRORSYMBOL is not defined */
                    /* This is what we do if the grammar does not define ERROR:
                    **
                    **  * Report an error message, and throw away the input token.
                    **
                    **  * If the input token is $, then fail the parse.
                    **
                    ** As before, subsequent error messages are suppressed until
                    ** three input tokens have been successfully shifted.
                    */
                    if ($this->yyerrcnt <= 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $this->yyerrcnt = 3;
                    $this->yy_destructor($yymajor, $yytokenvalue);
                    if ($yyendofinput) {
                        $this->yy_parse_failed();
                    }
                    $yymajor = self::YYNOCODE;
                }
            } else {
                $this->yy_accept();
                $yymajor = self::YYNOCODE;
            }
        } while ($yymajor != self::YYNOCODE && $this->yyidx >= 0);
    }
}
