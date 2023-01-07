<?php
/**
 * PHP_ParserGenerator, a php 5 parser generator.
 *
 * This is a direct port of the Lemon parser generator, found at
 * {@link http://www.hwaci.com/sw/lemon/}
 *
 * There are a few PHP-specific changes to the lemon parser generator.
 *
 * - %extra_argument is removed, as class constructor can be used to
 *   pass in extra information
 * - %token_type and company are irrelevant in PHP, and so are removed
 * - %declare_class is added to define the parser class name and any
 *   implements/extends information
 * - %include_class is added to allow insertion of extra class information
 *   such as constants, a class constructor, etc.
 *
 * Other changes make the parser more robust, and also make reporting
 * syntax errors simpler.  Detection of expected tokens eliminates some
 * problematic edge cases where an unexpected token could cause the parser
 * to simply accept input.
 *
 * Otherwise, the file format is identical to the Lemon parser generator
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
 * @version    CVS: $Id: ParserGenerator.php,v 1.2 2006/12/16 04:01:58 cellog Exp $
 * @since      File available since Release 0.1.0
 */
/**#@+
 * Basic components of the parser generator
 */
require_once './ParserGenerator/Action.php';
require_once './ParserGenerator/ActionTable.php';
require_once './ParserGenerator/Config.php';
require_once './ParserGenerator/Data.php';
require_once './ParserGenerator/Symbol.php';
require_once './ParserGenerator/Rule.php';
require_once './ParserGenerator/Parser.php';
require_once './ParserGenerator/PropagationLink.php';
require_once './ParserGenerator/State.php';
/**#@-*/
/**
 * The basic home class for the parser generator
 *
 * @package    PHP_ParserGenerator
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2006 Gregory Beaver
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    0.1.5
 * @since      Class available since Release 0.1.0
 * @example    Lempar.php
 * @example    examples/Parser.y Sample parser file format (PHP_LexerGenerator's parser)
 * @example    examples/Parser.php Sample parser file format PHP code (PHP_LexerGenerator's parser)
 */
class PHP_ParserGenerator
{
    /**
     * Set this to 1 to turn on debugging of Lemon's parsing of
     * grammar files.
     */
    const DEBUG = 0;
    const MAXRHS = 1000;
    const OPT_FLAG = 1, OPT_INT = 2, OPT_DBL = 3, OPT_STR = 4,
          OPT_FFLAG = 5, OPT_FINT = 6, OPT_FDBL = 7, OPT_FSTR = 8;
    public $azDefine = array();
    private static $options = array(
        'b' => array(
            'type' => self::OPT_FLAG,
            'arg' => 'basisflag',
            'message' => 'Print only the basis in report.'
        ),
        'c' => array(
            'type' => self::OPT_FLAG,
            'arg' => 'compress',
            'message' => 'Don\'t compress the action table.'
        ),
        'D' => array(
            'type' => self::OPT_FSTR,
            'arg' => 'handle_D_option',
            'message' => 'Define an %ifdef macro.'
        ),
        'g' => array(
            'type' => self::OPT_FLAG,
            'arg' => 'rpflag',
            'message' => 'Print grammar without actions.'
        ),
        'm' => array(
            'type' => self::OPT_FLAG,
            'arg' => 'mhflag',
            'message' => 'Output a makeheaders compatible file'
        ),
        'q' => array(
            'type' => self::OPT_FLAG,
            'arg' => 'quiet',
            'message' => '(Quiet) Don\'t print the report file.'
        ),
        's' => array(
            'type' => self::OPT_FLAG,
            'arg' => 'statistics',
            'message' => 'Print parser stats to standard output.'
        ),
        'x' => array(
            'type' => self::OPT_FLAG,
            'arg' => 'version',
            'message' => 'Print the version number.'
        )
    );

    private $basisflag = 0;
    private $compress = 0;
    private $rpflag = 0;
    private $mhflag = 0;
    private $quiet = 0;
    private $statistics = 0;
    private $version = 0;
    private $size;
    /**
     * Process a flag command line argument.
     * @param int
     * @param array
     * @return int
     */
    public function handleflags($i, $argv)
    {
        if (!isset($argv[1]) || !isset(self::$options[$argv[$i][1]])) {
            throw new Exception('Command line syntax error: undefined option "' .  $argv[$i] . '"');
        }
        $v = self::$options[$argv[$i][1]] == '-';
        if (self::$options[$argv[$i][1]]['type'] == self::OPT_FLAG) {
            $this->{self::$options[$argv[$i][1]]['arg']} = 1;
        } elseif (self::$options[$argv[$i][1]]['type'] == self::OPT_FFLAG) {
            $this->{self::$options[$argv[$i][1]]['arg']}($v);
        } elseif (self::$options[$argv[$i][1]]['type'] == self::OPT_FSTR) {
            $this->{self::$options[$argv[$i][1]]['arg']}(substr($v, 2));
        } else {
            throw new Exception('Command line syntax error: missing argument on switch: "' . $argv[$i] . '"');
        }

        return 0;
    }

    /**
     * Process a command line switch which has an argument.
     * @param int
     * @param array
     * @param array
     * @return int
     */
    public function handleswitch($i, $argv)
    {
        $lv = 0;
        $dv = 0.0;
        $sv = $end = $cp = '';
        $j = 0; // int
        $errcnt = 0;
        $cp = strstr($argv[$i],'=');
        if (!$cp) {
            throw new Exception('INTERNAL ERROR: handleswitch passed bad argument, no "=" in arg');
        }
        $argv[$i] = substr($argv[$i], 0, strlen($argv[$i]) - strlen($cp));
        if (!isset(self::$options[$argv[$i]])) {
            throw new Exception('Command line syntax error: undefined option "' .  $argv[$i] .
                $cp . '"');
        }
        $cp = substr($cp, 1);
        switch (self::$options[$argv[$i]]['type']) {
            case self::OPT_FLAG:
            case self::OPT_FFLAG:
                throw new Exception('Command line syntax error: option requires an argument "' .
                    $argv[$i] . '=' . $cp . '"');
            case self::OPT_DBL:
            case self::OPT_FDBL:
                $dv = (double) $cp;
                break;
            case self::OPT_INT:
            case self::OPT_FINT:
                $lv = (int) $cp;
                break;
            case self::OPT_STR:
            case self::OPT_FSTR:
                $sv = $cp;
                break;
        }
        switch (self::$options[$argv[$i]]['type']) {
            case self::OPT_FLAG:
            case self::OPT_FFLAG:
                break;
            case self::OPT_DBL:
                $this->{self::$options[$argv[$i]]['arg']} = $dv;
                break;
            case self::OPT_FDBL:
                $this->{self::$options[$argv[$i]]['arg']}($dv);
                break;
            case self::OPT_INT:
                $this->{self::$options[$argv[$i]]['arg']} = $lv;
                break;
            case self::OPT_FINT:
                $this->{self::$options[$argv[$i]]['arg']}($lv);
                break;
            case self::OPT_STR:
                $this->{self::$options[$argv[$i]]['arg']} = $sv;
                break;
            case self::OPT_FSTR:
                $this->{self::$options[$argv[$i]]['arg']}($sv);
                break;
        }

        return 0;
    }

    /**
     * @param array arguments
     * @param array valid options
     * @return int
     */
    public function OptInit($a)
    {
        $errcnt = 0;
        $argv = $a;
        try {
            if (is_array($argv) && count($argv) && self::$options) {
                for ($i = 1; $i < count($argv); $i++) {
                    if ($argv[$i][0] == '+' || $argv[$i][0] == '-') {
                        $errcnt += $this->handleflags($i, $argv);
                    } elseif (strstr($argv[$i],'=')) {
                        $errcnt += $this->handleswitch($i, $argv);
                    }
                }
            }
        } catch (Exception $e) {
            OptPrint();
            echo $e->getMessage();
            exit(1);
        }

        return 0;
    }

    /**
     * Return the index of the N-th non-switch argument.  Return -1
     * if N is out of range.
     * @param int
     * @return int
     */
    private function argindex($n, $a)
    {
        $dashdash = 0;
        if (!is_array($a) || !count($a)) {
            return -1;
        }
        for ($i=1; $i < count($a); $i++) {
            if ($dashdash || !($a[$i][0] == '-' || $a[$i][0] == '+' ||
                  strchr($a[$i], '='))) {
                if ($n == 0) {
                    return $i;
                }
                $n--;
            }
            if ($_SERVER['argv'][$i] == '--') {
                $dashdash = 1;
            }
        }

        return -1;
    }

    /**
     * Return the value of the non-option argument as indexed by $i
     *
     * @param int
     * @param  array the value of $argv
     * @return 0|string
     */
    private function OptArg($i, $a)
    {
        if (-1 == ($ind = $this->argindex($i, $a))) {
            return 0;
        }

        return $a[$ind];
    }

    /**
     * @return int number of arguments
     */
    public function OptNArgs($a)
    {
        $cnt = $dashdash = 0;
        if (is_array($a) && count($a)) {
            for ($i = 1; $i < count($a); $i++) {
                if ($dashdash || !($a[$i][0] == '-' || $a[$i][0] == '+' ||
                      strchr($a[$i], '='))) {
                    $cnt++;
                }
                if ($a[$i] == "--") {
                    $dashdash = 1;
                }
            }
        }

        return $cnt;
    }

    /**
     * Print out command-line options
     */
    public function OptPrint()
    {
        $max = 0;
        foreach (self::$options as $label => $info) {
            $len = strlen($label) + 1;
            switch ($info['type']) {
                case self::OPT_FLAG:
                case self::OPT_FFLAG:
                    break;
                case self::OPT_INT:
                case self::OPT_FINT:
                    $len += 9;       /* length of "<integer>" */
                    break;
                case self::OPT_DBL:
                case self::OPT_FDBL:
                    $len += 6;       /* length of "<real>" */
                    break;
                case self::OPT_STR:
                case self::OPT_FSTR:
                    $len += 8;       /* length of "<string>" */
                    break;
            }
            if ($len > $max) {
                $max = $len;
            }
        }
        foreach (self::$options as $label => $info) {
            switch ($info['type']) {
                case self::OPT_FLAG:
                case self::OPT_FFLAG:
                    echo "  -$label";
                    echo str_repeat(' ', $max - strlen($label));
                    echo "  $info[message]\n";
                    break;
                case self::OPT_INT:
                case self::OPT_FINT:
                    echo "  $label=<integer>" . str_repeat(' ', $max - strlen($label) - 9);
                    echo "  $info[message]\n";
                    break;
                case self::OPT_DBL:
                case self::OPT_FDBL:
                    echo "  $label=<real>" . str_repeat(' ', $max - strlen($label) - 6);
                    echo "  $info[message]\n";
                    break;
                case self::OPT_STR:
                case self::OPT_FSTR:
                    echo "  $label=<string>" . str_repeat(' ', $max - strlen($label) - 8);
                    echo "  $info[message]\n";
                    break;
            }
        }
    }

    /**
    * This routine is called with the argument to each -D command-line option.
    * Add the macro defined to the azDefine array.
    * @param string
    */
    private function handle_D_option($z)
    {
        if ($a = strstr($z, '=')) {
            $z = substr($a, 1); // strip first =
        }
        $this->azDefine[] = $z;
    }

    /**************** From the file "main.c" ************************************/
/*
** Main program file for the LEMON parser generator.
*/

    /* The main program.  Parse the command line and do it... */
    public function main($filename = null)
    {
        $lem = new PHP_ParserGenerator_Data;
        if (!isset($filename)) {
            $this->OptInit($_SERVER['argv']);
            if ($this->version) {
                echo "Lemon version 1.0/PHP_ParserGenerator port version 0.1.5\n";
                exit(0);
            }
            if ($this->OptNArgs($_SERVER['argv']) != 1) {
                echo "Exactly one filename argument is required.\n";
                exit(1);
            }

            /* Initialize the machine */
            $lem->argv0 = $_SERVER['argv'][0];
            $lem->filename = $this->OptArg(0, $_SERVER['argv']);
        } else {
            $lem->filename = $filename;
        }
        $lem->errorcnt = 0;
        $a = pathinfo($lem->filename);
        if (isset($a['extension'])) {
            $ext = '.' . $a['extension'];
            $lem->filenosuffix = substr($lem->filename, 0, strlen($lem->filename) - strlen($ext));
        } else {
            $lem->filenosuffix = $lem->filename;
        }
        $lem->basisflag = $this->basisflag;
        $lem->has_fallback = 0;
        $lem->nconflict = 0;
        $lem->name = $lem->include_code = $lem->include_classcode = $lem->arg =
            $lem->tokentype = $lem->start = 0;
        $lem->vartype = 0;
        $lem->stacksize = 0;
        $lem->error = $lem->overflow = $lem->failure = $lem->accept = $lem->tokendest =
          $lem->tokenprefix = $lem->outname = $lem->extracode = 0;
        $lem->vardest = 0;
        $lem->tablesize = 0;
        PHP_ParserGenerator_Symbol::Symbol_new("$");
        $lem->errsym = PHP_ParserGenerator_Symbol::Symbol_new("error");

        /* Parse the input file */
        $parser = new PHP_ParserGenerator_Parser($this);
        $parser->Parse($lem);
        if ($lem->errorcnt) {
            exit($lem->errorcnt);
        }
        if ($lem->rule === 0) {
            printf("Empty grammar.\n");
            exit(1);
        }

        /* Count and index the symbols of the grammar */
        $lem->nsymbol = PHP_ParserGenerator_Symbol::Symbol_count();
        PHP_ParserGenerator_Symbol::Symbol_new("{default}");
        $lem->symbols = PHP_ParserGenerator_Symbol::Symbol_arrayof();
        for ($i = 0; $i <= $lem->nsymbol; $i++) {
            $lem->symbols[$i]->index = $i;
        }
        usort($lem->symbols, array('PHP_ParserGenerator_Symbol', 'sortSymbols'));
        for ($i = 0; $i <= $lem->nsymbol; $i++) {
            $lem->symbols[$i]->index = $i;
        }
        // find the first lower-case symbol
        for($i = 1; ord($lem->symbols[$i]->name[0]) < ord ('Z'); $i++);
        $lem->nterminal = $i;

        /* Generate a reprint of the grammar, if requested on the command line */
        if ($this->rpflag) {
            $this->Reprint();
        } else {
            /* Initialize the size for all follow and first sets */
            $this->SetSize($lem->nterminal);

            /* Find the precedence for every production rule (that has one) */
            $lem->FindRulePrecedences();

            /* Compute the lambda-nonterminals and the first-sets for every
            ** nonterminal */
            $lem->FindFirstSets();

            /* Compute all LR(0) states.  Also record follow-set propagation
            ** links so that the follow-set can be computed later */
            $lem->nstate = 0;
            $lem->FindStates();
            $lem->sorted = PHP_ParserGenerator_State::State_arrayof();

            /* Tie up loose ends on the propagation links */
            $lem->FindLinks();

            /* Compute the follow set of every reducible configuration */
            $lem->FindFollowSets();

            /* Compute the action tables */
            $lem->FindActions();

            /* Compress the action tables */
            if ($this->compress===0) {
                $lem->CompressTables();
            }

            /* Reorder and renumber the states so that states with fewer choices
            ** occur at the end. */
            $lem->ResortStates();

            /* Generate a report of the parser generated.  (the "y.output" file) */
            if (!$this->quiet) {
                $lem->ReportOutput();
            }

            /* Generate the source code for the parser */
            $lem->ReportTable($this->mhflag);

    /* Produce a header file for use by the scanner.  (This step is
    ** omitted if the "-m" option is used because makeheaders will
    ** generate the file for us.) */
//            if (!$this->mhflag) {
//                $this->ReportHeader();
//            }
        }
        if ($this->statistics) {
            printf("Parser statistics: %d terminals, %d nonterminals, %d rules\n",
                $lem->nterminal, $lem->nsymbol - $lem->nterminal, $lem->nrule);
            printf("                   %d states, %d parser table entries, %d conflicts\n",
                $lem->nstate, $lem->tablesize, $lem->nconflict);
        }
        if ($lem->nconflict) {
            printf("%d parsing conflicts.\n", $lem->nconflict);
        }
        //exit($lem->errorcnt + $lem->nconflict);

        return ($lem->errorcnt + $lem->nconflict);
    }

    public function SetSize($n)
    {
        $this->size = $n + 1;
    }

    /**
     * Merge in a merge sort for a linked list
     * Inputs:
     *  - a:       A sorted, null-terminated linked list.  (May be null).
     *  - b:       A sorted, null-terminated linked list.  (May be null).
     *  - cmp:     A pointer to the comparison function.
     *  - offset:  Offset in the structure to the "next" field.
     *
     * Return Value:
     *   A pointer to the head of a sorted list containing the elements
     *   of both a and b.
     *
     * Side effects:
     *   The "next" pointers for elements in the lists a and b are
     *   changed.
     */
    public static function merge($a, $b, $cmp, $offset)
    {
        if ($a === 0) {
            $head = $b;
        } elseif ($b === 0) {
            $head = $a;
        } else {
            if (call_user_func($cmp, $a, $b) < 0) {
                $ptr = $a;
                $a = $a->$offset;
            } else {
                $ptr = $b;
                $b = $b->$offset;
            }
            $head = $ptr;
            while ($a && $b) {
                if (call_user_func($cmp, $a, $b) < 0) {
                    $ptr->$offset = $a;
                    $ptr = $a;
                    $a = $a->$offset;
                } else {
                    $ptr->$offset = $b;
                    $ptr = $b;
                    $b = $b->$offset;
                }
            }
            if ($a !== 0) {
                $ptr->$offset = $a;
            } else {
                $ptr->$offset = $b;
            }
        }

        return $head;
    }

    /*
    ** Inputs:
    **   list:      Pointer to a singly-linked list of structures.
    **   next:      Pointer to pointer to the second element of the list.
    **   cmp:       A comparison function.
    **
    ** Return Value:
    **   A pointer to the head of a sorted list containing the elements
    **   orginally in list.
    **
    ** Side effects:
    **   The "next" pointers for elements in list are changed.
    */
    #define LISTSIZE 30
    public static function msort($list, $next, $cmp)
    {
        if ($list === 0) {
            return $list;
        }
        if ($list->$next === 0) {
            return $list;
        }
        $set = array_fill(0, 30, 0);
        while ($list) {
            $ep = $list;
            $list = $list->$next;
            $ep->$next = 0;
            for ($i = 0; $i < 29 && $set[$i] !== 0; $i++) {
                $ep = self::merge($ep, $set[$i], $cmp, $next);
                $set[$i] = 0;
            }
            $set[$i] = $ep;
        }
        $ep = 0;
        for ($i = 0; $i < 30; $i++) {
            if ($set[$i] !== 0) {
                $ep = self::merge($ep, $set[$i], $cmp, $next);
            }
        }

        return $ep;
    }

    /* Find a good place to break "msg" so that its length is at least "min"
    ** but no more than "max".  Make the point as close to max as possible.
    */
    public static function findbreak($msg, $min, $max)
    {
        if ($min >= strlen($msg)) {
            return strlen($msg);
        }
        for ($i = $spot = $min; $i <= $max && $i < strlen($msg); $i++) {
            $c = $msg[$i];
            if ($c == '-' && $i < $max - 1) {
                $spot = $i + 1;
            }
            if ($c == ' ') {
                $spot = $i;
            }
        }

        return $spot;
    }

    public static function ErrorMsg($filename, $lineno, $format)
    {
        /* Prepare a prefix to be prepended to every output line */
        if ($lineno > 0) {
            $prefix = sprintf("%20s:%d: ", $filename, $lineno);
        } else {
            $prefix = sprintf("%20s: ", $filename);
        }
        $prefixsize = strlen($prefix);
        $availablewidth = 79 - $prefixsize;

        /* Generate the error message */
        $ap = func_get_args();
        array_shift($ap); // $filename
        array_shift($ap); // $lineno
        array_shift($ap); // $format
        $errmsg = vsprintf($format, $ap);
        $linewidth = strlen($errmsg);
        /* Remove trailing "\n"s from the error message. */
        while ($linewidth > 0 && in_array($errmsg[$linewidth-1], array("\n", "\r"), true)) {
            --$linewidth;
            $errmsg = substr($errmsg, 0, strlen($errmsg) - 1);
        }

        /* Print the error message */
        $base = 0;
        $errmsg = str_replace(array("\r", "\n", "\t"), array(' ', ' ', ' '), $errmsg);
        while (strlen($errmsg)) {
            $end = $restart = self::findbreak($errmsg, 0, $availablewidth);
            if (strlen($errmsg) <= 79 && $end < strlen($errmsg) && $end <= 79) {
                $end = $restart = strlen($errmsg);
            }
            while (isset($errmsg[$restart]) && $errmsg[$restart] == ' ') {
                $restart++;
            }
            printf("%s%.${end}s\n", $prefix, $errmsg);
            $errmsg = substr($errmsg, $restart);
        }
    }

    /**
     * Duplicate the input file without comments and without actions
     * on rules
     */
    public function Reprint()
    {
        printf("// Reprint of input file \"%s\".\n// Symbols:\n", $this->filename);
        $maxlen = 10;
        for ($i = 0; $i < $this->nsymbol; $i++) {
            $sp = $this->symbols[$i];
            $len = strlen($sp->name);
            if ($len > $maxlen) {
                $maxlen = $len;
            }
        }
        $ncolumns = 76 / ($maxlen + 5);
        if ($ncolumns < 1) {
            $ncolumns = 1;
        }
        $skip = ($this->nsymbol + $ncolumns - 1) / $ncolumns;
        for ($i = 0; $i < $skip; $i++) {
            print "//";
            for ($j = $i; $j < $this->nsymbol; $j += $skip) {
                $sp = $this->symbols[$j];
                //assert( sp->index==j );
                printf(" %3d %-${maxlen}.${maxlen}s", $j, $sp->name);
            }
            print "\n";
        }
        for ($rp = $this->rule; $rp; $rp = $rp->next) {
            printf("%s", $rp->lhs->name);
/*          if ($rp->lhsalias) {
                printf("(%s)", $rp->lhsalias);
            }*/
            print " ::=";
            for ($i = 0; $i < $rp->nrhs; $i++) {
                $sp = $rp->rhs[$i];
                printf(" %s", $sp->name);
                if ($sp->type == PHP_ParserGenerator_Symbol::MULTITERMINAL) {
                    for ($j = 1; $j < $sp->nsubsym; $j++) {
                        printf("|%s", $sp->subsym[$j]->name);
                    }
                }
/*              if ($rp->rhsalias[$i]) {
                    printf("(%s)", $rp->rhsalias[$i]);
                }*/
            }
            print ".";
            if ($rp->precsym) {
                printf(" [%s]", $rp->precsym->name);
            }
/*          if ($rp->code) {
                print "\n    " . $rp->code);
            }*/
            print "\n";
        }
    }
}
//$a = new PHP_ParserGenerator;
//$_SERVER['argv'] = array('lemon', '-s', '/development/lemon/PHP_Parser.y');
//$_SERVER['argv'] = array('lemon', '-s', '/development/File_ChessPGN/ChessPGN/Parser.y');
//$a->main();
