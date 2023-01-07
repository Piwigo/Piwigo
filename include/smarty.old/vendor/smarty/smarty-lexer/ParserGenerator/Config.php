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
 * @version    CVS: $Id: Config.php,v 1.1 2006/07/18 00:53:10 cellog Exp $
 * @since      File available since Release 0.1.0
 */
/**
/** A configuration is a production rule of the grammar together with
 * a mark (dot) showing how much of that rule has been processed so far.
 *
 * Configurations also contain a follow-set which is a list of terminal
 * symbols which are allowed to immediately follow the end of the rule.
 * Every configuration is recorded as an instance of the following class.
 *
 * @package    PHP_ParserGenerator
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2006 Gregory Beaver
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    0.1.5
 * @since      Class available since Release 0.1.0
 */
class PHP_ParserGenerator_Config
{
    const COMPLETE = 1;
    const INCOMPLETE = 2;
    /**
     * The parser rule upon with the configuration is based.
     *
     * A parser rule is something like:
     * <pre>
     * blah ::= FOO bar.
     * </pre>
     * @var PHP_ParserGenerator_Rule
     */
    public $rp;
    /**
     * The parse point.
     *
     * This is the index into the right-hand side of a rule that is
     * represented by this configuration.  In other words, possible
     * dots for this rule:
     *
     * <pre>
     * blah ::= FOO bar.
     * </pre>
     *
     * are (represented by "[here]"):
     *
     * <pre>
     * blah ::= [here] FOO bar.
     * blah ::= FOO [here] bar.
     * blah ::= FOO bar [here].
     * </pre>
     * @var int
     */
    public $dot;
    /**
     * Follow-set for this configuration only
     *
     * This is the list of terminals and non-terminals that
     * can follow this configuration.
     * @var array
     */
    public $fws;
    /**
     * Follow-set forward propagation links.
     * @var PHP_ParserGenerator_PropagationLink
     */
    public $fplp;
    /**
     * Follow-set backwards propagation links
     * @var PHP_ParserGenerator_PropagationLink
     */
    public $bplp;
    /**
     * State that contains this configuration
     * @var PHP_ParserGenerator_State
     */
    public $stp;
  /* enum {
    COMPLETE,              /* The status is used during followset and
    INCOMPLETE             /*    shift computations
  } */
    /**
     * Status during followset and shift computations.
     *
     * One of PHP_ParserGenerator_Config::COMPLETE or
     * PHP_ParserGenerator_Config::INCOMPLETE.
     * @var int
     */
    public $status;
    /**
     * Next configuration in the state.
     *
     * Index of next PHP_ParserGenerator_Config object.
     * @var int
     */
    public $next;
    /**
     * Index of the next basis configuration PHP_ParserGenerator_Config object
     * @var int
     */
    public $bp;

    /**
     * Top of the list of configurations for the current state.
     * @var PHP_ParserGenerator_Config
     */
    public static $current;
    /**
     * Last on the list of configurations for the current state.
     * @var PHP_ParserGenerator_Config
     */
    public static $currentend;

    /**
     * Top of the list of basis configurations for the current state.
     * @var PHP_ParserGenerator_Config
     */
    public static $basis;
    /**
     * Last on the list of basis configurations for the current state.
     * @var PHP_ParserGenerator_Config
     */
    public static $basisend;

    /**
     * Associative array representation of the linked list of configurations
     * found in {@link $current}
     *
     * @var array
     */
    public static $x4a = array();

    /**
     * Return a pointer to a new configuration
     * @return PHP_ParserGenerator_Config
     */
    private static function newconfig()
    {
        return new PHP_ParserGenerator_Config;
    }

    /**
     * Display the current configuration for the .out file
     *
     * @param PHP_ParserGenerator_Config $cfp
     * @see PHP_ParserGenerator_Data::ReportOutput()
     */
    public static function Configshow(PHP_ParserGenerator_Config $cfp)
    {
        $fp = fopen('php://output', 'w');
        while ($cfp) {
            if ($cfp->dot == $cfp->rp->nrhs) {
                $buf = sprintf('(%d)', $cfp->rp->index);
                fprintf($fp, '    %5s ', $buf);
            } else {
                fwrite($fp,'          ');
            }
            $cfp->ConfigPrint($fp);
            fwrite($fp, "\n");
            if (0) {
                //SetPrint(fp,cfp->fws,$this);
                //PlinkPrint(fp,cfp->fplp,"To  ");
                //PlinkPrint(fp,cfp->bplp,"From");
            }
            $cfp = $cfp->next;
        }
        fwrite($fp, "\n");
        fclose($fp);
    }

    /**
     * Initialize the configuration list builder for a new state.
     */
    public static function Configlist_init()
    {
        self::$current = 0;
        self::$currentend = &self::$current;
        self::$basis = 0;
        self::$basisend = &self::$basis;
        self::$x4a = array();
    }

    /**
     * Remove all data from the table.
     *
     * Pass each data to the function $f as it is removed if
     * $f is a valid callback.
     * @param callback|null
     * @see Configtable_clear()
     */
    public static function Configtable_reset($f)
    {
        self::$current = 0;
        self::$currentend = &self::$current;
        self::$basis = 0;
        self::$basisend = &self::$basis;
        self::Configtable_clear(0);
    }

    /**
     * Remove all data from the associative array representation
     * of configurations.
     *
     * Pass each data to the function $f as it is removed if
     * $f is a valid callback.
     * @param callback|null
     */
    public static function Configtable_clear($f)
    {
        if (!count(self::$x4a)) {
            return;
        }
        if ($f) {
            for ($i = 0; $i < count(self::$x4a); $i++) {
                call_user_func($f, self::$x4a[$i]->data);
            }
        }
        self::$x4a = array();
    }

    /**
     * Reset the configuration list builder for a new state.
     * @see Configtable_clear()
     */
    public static function Configlist_reset()
    {
        self::Configtable_clear(0);
    }

    /**
     * Add another configuration to the configuration list for this parser state.
     * @param PHP_ParserGenerator_Rule the rule
     * @param int Index into the right-hand side of the rule where the dot goes
     * @return PHP_ParserGenerator_Config
     */
    public static function Configlist_add($rp, $dot)
    {
        $model = new PHP_ParserGenerator_Config;
        $model->rp = $rp;
        $model->dot = $dot;
        $cfp = self::Configtable_find($model);
        if ($cfp === 0) {
            $cfp = self::newconfig();
            $cfp->rp = $rp;
            $cfp->dot = $dot;
            $cfp->fws = array();
            $cfp->stp = 0;
            $cfp->fplp = $cfp->bplp = 0;
            $cfp->next = 0;
            $cfp->bp = 0;
            self::$currentend = $cfp;
            self::$currentend = &$cfp->next;
            self::Configtable_insert($cfp);
        }

        return $cfp;
    }

    /**
     * Add a basis configuration to the configuration list for this parser state.
     *
     * Basis configurations are the root for a configuration.  This method also
     * inserts the configuration into the regular list of configurations for this
     * reason.
     * @param PHP_ParserGenerator_Rule the rule
     * @param int Index into the right-hand side of the rule where the dot goes
     * @return PHP_ParserGenerator_Config
     */
    public static function Configlist_addbasis($rp, $dot)
    {
        $model = new PHP_ParserGenerator_Config;
        $model->rp = $rp;
        $model->dot = $dot;
        $cfp = self::Configtable_find($model);
        if ($cfp === 0) {
            $cfp = self::newconfig();
            $cfp->rp = $rp;
            $cfp->dot = $dot;
            $cfp->fws = array();
            $cfp->stp = 0;
            $cfp->fplp = $cfp->bplp = 0;
            $cfp->next = 0;
            $cfp->bp = 0;
            self::$currentend = $cfp;
            self::$currentend = &$cfp->next;
            self::$basisend = $cfp;
            self::$basisend = &$cfp->bp;
            self::Configtable_insert($cfp);
        }

        return $cfp;
    }

    /**
     * Compute the closure of the configuration list.
     *
     * This calculates all of the possible continuations of
     * each configuration, ensuring that each state accounts
     * for every configuration that could arrive at that state.
     */
    public static function Configlist_closure(PHP_ParserGenerator_Data $lemp)
    {
        for ($cfp = self::$current; $cfp; $cfp = $cfp->next) {
            $rp = $cfp->rp;
            $dot = $cfp->dot;
            if ($dot >= $rp->nrhs) {
                continue;
            }
            $sp = $rp->rhs[$dot];
            if ($sp->type == PHP_ParserGenerator_Symbol::NONTERMINAL) {
                if ($sp->rule === 0 && $sp !== $lemp->errsym) {
                    PHP_ParserGenerator::ErrorMsg($lemp->filename, $rp->line,
                        "Nonterminal \"%s\" has no rules.", $sp->name);
                    $lemp->errorcnt++;
                }
                for ($newrp = $sp->rule; $newrp; $newrp = $newrp->nextlhs) {
                    $newcfp = self::Configlist_add($newrp, 0);
                    for ($i = $dot + 1; $i < $rp->nrhs; $i++) {
                        $xsp = $rp->rhs[$i];
                        if ($xsp->type == PHP_ParserGenerator_Symbol::TERMINAL) {
                            $newcfp->fws[$xsp->index] = 1;
                            break;
                        } elseif ($xsp->type == PHP_ParserGenerator_Symbol::MULTITERMINAL) {
                            for ($k = 0; $k < $xsp->nsubsym; $k++) {
                                $newcfp->fws[$xsp->subsym[$k]->index] = 1;
                            }
                            break;
                        } else {
                            $a = array_diff_key($xsp->firstset, $newcfp->fws);
                            $newcfp->fws += $a;
                            if ($xsp->lambda === false) {
                                break;
                            }
                        }
                    }
                    if ($i == $rp->nrhs) {
                        PHP_ParserGenerator_PropagationLink::Plink_add($cfp->fplp, $newcfp);
                    }
                }
            }
        }
    }

    /**
     * Sort the configuration list
     * @uses Configcmp()
     */
    public static function Configlist_sort()
    {
        $a = 0;
        //self::Configshow(self::$current);
        self::$current = PHP_ParserGenerator::msort(self::$current,'next', array('PHP_ParserGenerator_Config', 'Configcmp'));
        //self::Configshow(self::$current);
        self::$currentend = &$a;
        self::$currentend = 0;
    }

    /**
     * Sort the configuration list
     * @uses Configcmp
     */
    public static function Configlist_sortbasis()
    {
        $a = 0;
        self::$basis = PHP_ParserGenerator::msort(self::$current,'bp', array('PHP_ParserGenerator_Config', 'Configcmp'));
        self::$basisend = &$a;
        self::$basisend = 0;
    }

    /**
     * Return a pointer to the head of the configuration list and
     * reset the list
     * @see $current
     * @return PHP_ParserGenerator_Config
     */
    public static function Configlist_return()
    {
        $old = self::$current;
        self::$current = 0;
        self::$currentend = &self::$current;

        return $old;
    }

    /**
     * Return a pointer to the head of the basis list and
     * reset the list
     * @see $basis
     * @return PHP_ParserGenerator_Config
     */
    public static function Configlist_basis()
    {
        $old = self::$basis;
        self::$basis = 0;
        self::$basisend = &self::$basis;

        return $old;
    }

    /**
     * Free all elements of the given configuration list
     * @param PHP_ParserGenerator_Config
     */
    public static function Configlist_eat($cfp)
    {
    $nextcfp = null;
    for (; $cfp; $cfp = $nextcfp) {
            $nextcfp = $cfp->next;
            if ($cfp->fplp !=0) {
                throw new Exception('fplp of configuration non-zero?');
            }
            if ($cfp->bplp !=0) {
                throw new Exception('bplp of configuration non-zero?');
            }
            if ($cfp->fws) {
                $cfp->fws = array();
            }
        }
    }

    /**
     * Compare two configurations for sorting purposes.
     *
     * Configurations based on higher precedence rules
     * (those earlier in the file) are chosen first.  Two
     * configurations that are the same rule are sorted by
     * dot (see {@link $dot}), and those configurations
     * with a dot closer to the left-hand side are chosen first.
     * @param  unknown_type $a
     * @param  unknown_type $b
     * @return unknown
     */
    public static function Configcmp($a, $b)
    {
        $x = $a->rp->index - $b->rp->index;
        if (!$x) {
            $x = $a->dot - $b->dot;
        }

        return $x;
    }

    /**
     * Print out information on this configuration.
     *
     * @param resource $fp
     * @see PHP_ParserGenerator_Data::ReportOutput()
     */
    public function ConfigPrint($fp)
    {
        $rp = $this->rp;
        fprintf($fp, "%s ::=", $rp->lhs->name);
        for ($i = 0; $i <= $rp->nrhs; $i++) {
            if ($i === $this->dot) {
                fwrite($fp,' *');
            }
            if ($i === $rp->nrhs) {
                break;
            }
            $sp = $rp->rhs[$i];
            fprintf($fp,' %s', $sp->name);
            if ($sp->type == PHP_ParserGenerator_Symbol::MULTITERMINAL) {
                for ($j = 1; $j < $sp->nsubsym; $j++) {
                    fprintf($fp, '|%s', $sp->subsym[$j]->name);
                }
            }
        }
    }

    /**
     * Hash a configuration for the associative array {@link $x4a}
     */
    private static function confighash(PHP_ParserGenerator_Config $a)
    {
        $h = 0;
        $h = $h * 571 + $a->rp->index * 37 + $a->dot;

        return $h;
    }

    /**
     * Insert a new record into the array.  Return TRUE if successful.
     * Prior data with the same key is NOT overwritten
     */
    public static function Configtable_insert(PHP_ParserGenerator_Config $data)
    {
        $h = self::confighash($data);
        if (isset(self::$x4a[$h])) {
            $np = self::$x4a[$h];
        } else {
            $np = 0;
        }
        while ($np) {
            if (self::Configcmp($np->data, $data) == 0) {
                /* An existing entry with the same key is found. */
                /* Fail because overwrite is not allows. */

                return 0;
            }
            $np = $np->next;
        }
        /* Insert the new data */
        $np = array('data' => $data, 'next' => 0, 'from' => 0);
        $np = new PHP_ParserGenerator_StateNode;
        $np->data = $data;
        if (isset(self::$x4a[$h])) {
            self::$x4a[$h]->from = $np->next;
            $np->next = self::$x4a[$h];
        }
        $np->from = $np;
        self::$x4a[$h] = $np;

        return 1;
    }

    /**
     * Return a pointer to data assigned to the given key.  Return NULL
     * if no such key.
     * @return PHP_ParserGenerator_Config|0
     */
    public static function Configtable_find(PHP_ParserGenerator_Config $key)
    {
        $h = self::confighash($key);
        if (!isset(self::$x4a[$h])) {
            return 0;
        }
        $np = self::$x4a[$h];
        while ($np) {
            if (self::Configcmp($np->data, $key) == 0) {
                break;
            }
            $np = $np->next;
        }

        return $np ? $np->data : 0;
    }
}
