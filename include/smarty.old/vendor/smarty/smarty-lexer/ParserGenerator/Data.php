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
 * @version    CVS: $Id: Data.php,v 1.2 2007/03/04 17:52:05 cellog Exp $
 * @since      File available since Release 0.1.0
 */
/**
/**
 * The state vector for the entire parser generator is recorded in
 * this class.
 *
 * @package    PHP_ParserGenerator
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2006 Gregory Beaver
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    0.1.5
 * @since      Class available since Release 0.1.0
 */

class PHP_ParserGenerator_Data
{
    /**
     * Used for terminal and non-terminal offsets into the action table
     * when their default should be used instead
     */
    const NO_OFFSET = -2147483647;
    /**
     * Table of states sorted by state number
     * @var array array of {@link PHP_ParserGenerator_State} objects
     */
    public $sorted;
    /**
     * List of all rules
     * @var PHP_ParserGenerator_Rule
     */
    public $rule;
    /**
     * Number of states
     * @var int
     */
    public $nstate;
    /**
     * Number of rules
     * @var int
     */
    public $nrule;
    /**
     * Number of terminal and nonterminal symbols
     * @var int
     */
    public $nsymbol;
    /**
     * Number of terminal symbols (tokens)
     * @var int
     */
    public $nterminal;
    /**
     * Sorted array of pointers to symbols
     * @var array array of {@link PHP_ParserGenerator_Symbol} objects
     */
    public $symbols = array();
    /**
     * Number of errors
     * @var int
     */
    public $errorcnt;
    /**
     * The error symbol
     * @var PHP_ParserGenerator_Symbol
     */
    public $errsym;
    /**
     * Name of the generated parser
     * @var string
     */
    public $name;
    /**
     * Unused relic from the C version
     *
     * Type of terminal symbols in the parser stack
     * @var string
     */
    public $tokentype;
    /**
     * Unused relic from the C version
     *
     * The default type of non-terminal symbols
     * @var string
     */
    public $vartype;
    /**
     * Name of the start symbol for the grammar
     * @var string
     */
    public $start;
    /**
     * Size of the parser stack
     *
     * This is 100 by default, but is set with the %stack_size directive
     * @var int
     */
    public $stacksize;
    /**
     * Code to put at the start of the parser file
     *
     * This is set by the %include directive
     * @var string
     */
    public $include_code;
    /**
     * Line number for start of include code
     * @var int
     */
    public $includeln;
    /**
     * Code to put in the parser class
     *
     * This is set by the %include_class directive
     * @var string
     */
    public $include_classcode;
    /**
     * Line number for start of include code
     * @var int
     */
    public $include_classln;
    /**
     * any extends/implements code
     *
     * This is set by the %declare_class directive
     * @var string
     */
    /**
     * Line number for class declaration code
     * @var int
     */
    public $declare_classcode;
    /**
     * Line number for start of class declaration code
     * @var int
     */
    public $declare_classln;
    /**
     * Code to execute when a syntax error is seen
     *
     * This is set by the %syntax_error directive
     * @var string
     */
    public $error;
    /**
     * Line number for start of error code
     * @var int
     */
    public $errorln;
    /**
     * Code to execute on a stack overflow
     *
     * This is set by the %stack_overflow directive
     */
    public $overflow;
    /**
     * Line number for start of overflow code
     * @var int
     */
    public $overflowln;
    /**
     * Code to execute on parser failure
     *
     * This is set by the %parse_failure directive
     * @var string
     */
    public $failure;
    /**
     * Line number for start of failure code
     * @var int
     */
    public $failureln;
    /**
     * Code to execute when the parser acccepts (completes parsing)
     *
     * This is set by the %parse_accept directive
     * @var string
     */
    public $accept;
    /**
     * Line number for the start of accept code
     * @var int
     */
    public $acceptln;
    /**
     * Code appended to the generated file
     *
     * This is set by the %code directive
     * @var string
     */
    public $extracode;
    /**
     * Line number for the start of the extra code
     * @var int
     */
    public $extracodeln;
    /**
     * Code to execute to destroy token data
     *
     * This is set by the %token_destructor directive
     * @var string
     */
    public $tokendest;
    /**
     * Line number for token destroyer code
     * @var int
     */
    public $tokendestln;
    /**
     * Code for the default non-terminal destructor
     *
     * This is set by the %default_destructor directive
     * @var string
     */
    public $vardest;
    /**
     * Line number for default non-terminal destructor code
     * @var int
     */
    public $vardestln;
    /**
     * Name of the input file
     * @var string
     */
    public $filename;
    /**
     * Name of the input file without its extension
     * @var string
     */
    public $filenosuffix;
    /**
     * Name of the current output file
     * @var string
     */
    public $outname;
    /**
     * A prefix added to token names
     * @var string
     */
    public $tokenprefix;
    /**
     * Number of parsing conflicts
     * @var int
     */
    public $nconflict;
    /**
     * Size of the parse tables
     * @var int
     */
    public $tablesize;
    /**
     * Public only basis configurations
     */
    public $basisflag;
    /**
     * True if any %fallback is seen in the grammer
     * @var boolean
     */
    public $has_fallback;
    /**
     * Name of the program
     * @var string
     */
    public $argv0;

    /* Find a precedence symbol of every rule in the grammar.
     *
     * Those rules which have a precedence symbol coded in the input
     * grammar using the "[symbol]" construct will already have the
     * $rp->precsym field filled.  Other rules take as their precedence
     * symbol the first RHS symbol with a defined precedence.  If there
     * are not RHS symbols with a defined precedence, the precedence
     * symbol field is left blank.
     */
    public function FindRulePrecedences()
    {
        for ($rp = $this->rule; $rp; $rp = $rp->next) {
            if ($rp->precsym === 0) {
                for ($i = 0; $i < $rp->nrhs && $rp->precsym === 0; $i++) {
                    $sp = $rp->rhs[$i];
                    if ($sp->type == PHP_ParserGenerator_Symbol::MULTITERMINAL) {
                        for ($j = 0; $j < $sp->nsubsym; $j++) {
                            if ($sp->subsym[$j]->prec >= 0) {
                                $rp->precsym = $sp->subsym[$j];
                                break;
                            }
                        }
                    } elseif ($sp->prec >= 0) {
                        $rp->precsym = $rp->rhs[$i];
                    }
                }
            }
        }
    }

    /**
     * Find all nonterminals which will generate the empty string.
     * Then go back and compute the first sets of every nonterminal.
     * The first set is the set of all terminal symbols which can begin
     * a string generated by that nonterminal.
     */
    public function FindFirstSets()
    {
        for ($i = 0; $i < $this->nsymbol; $i++) {
            $this->symbols[$i]->lambda = false;
        }
        for ($i = $this->nterminal; $i < $this->nsymbol; $i++) {
            $this->symbols[$i]->firstset = array();
        }

        /* First compute all lambdas */
        do {
            $progress = 0;
            for ($rp = $this->rule; $rp; $rp = $rp->next) {
                if ($rp->lhs->lambda) {
                    continue;
                }
                for ($i = 0; $i < $rp->nrhs; $i++) {
                    $sp = $rp->rhs[$i];
                    if ($sp->type != PHP_ParserGenerator_Symbol::TERMINAL || $sp->lambda === false) {
                        break;
                    }
                }
                if ($i === $rp->nrhs) {
                    $rp->lhs->lambda = true;
                    $progress = 1;
                }
            }
        } while ($progress);

        /* Now compute all first sets */
        do {
            $progress = 0;
            for ($rp = $this->rule; $rp; $rp = $rp->next) {
                $s1 = $rp->lhs;
                for ($i = 0; $i < $rp->nrhs; $i++) {
                    $s2 = $rp->rhs[$i];
                    if ($s2->type == PHP_ParserGenerator_Symbol::TERMINAL) {
                        //progress += SetAdd(s1->firstset,s2->index);
                        $progress += isset($s1->firstset[$s2->index]) ? 0 : 1;
                        $s1->firstset[$s2->index] = 1;
                        break;
                    } elseif ($s2->type == PHP_ParserGenerator_Symbol::MULTITERMINAL) {
                        for ($j = 0; $j < $s2->nsubsym; $j++) {
                            //progress += SetAdd(s1->firstset,s2->subsym[j]->index);
                            $progress += isset($s1->firstset[$s2->subsym[$j]->index]) ? 0 : 1;
                            $s1->firstset[$s2->subsym[$j]->index] = 1;
                        }
                        break;
                    } elseif ($s1 === $s2) {
                        if ($s1->lambda === false) {
                            break;
                        }
                    } else {
                        //progress += SetUnion(s1->firstset,s2->firstset);
                        $test = array_diff_key($s2->firstset, $s1->firstset);
                        if (count($test)) {
                            $progress++;
                            $s1->firstset += $test;
                        }
                        if ($s2->lambda === false) {
                            break;
                        }
                    }
                }
            }
        } while ($progress);
    }

    /**
     * Compute all LR(0) states for the grammar.  Links
     * are added to between some states so that the LR(1) follow sets
     * can be computed later.
     */
    public function FindStates()
    {
        PHP_ParserGenerator_Config::Configlist_init();

        /* Find the start symbol */
        if ($this->start) {
            $sp = PHP_ParserGenerator_Symbol::Symbol_find($this->start);
            if ($sp == 0) {
                PHP_ParserGenerator::ErrorMsg($this->filename, 0,
                    "The specified start symbol \"%s\" is not " .
                    "in a nonterminal of the grammar.  \"%s\" will be used as the start " .
                    "symbol instead.", $this->start, $this->rule->lhs->name);
                $this->errorcnt++;
                $sp = $this->rule->lhs;
            }
        } else {
            $sp = $this->rule->lhs;
        }

        /* Make sure the start symbol doesn't occur on the right-hand side of
        ** any rule.  Report an error if it does.  (YACC would generate a new
        ** start symbol in this case.) */
        for ($rp = $this->rule; $rp; $rp = $rp->next) {
            for ($i = 0; $i < $rp->nrhs; $i++) {
                if ($rp->rhs[$i]->type == PHP_ParserGenerator_Symbol::MULTITERMINAL) {
                    foreach ($rp->rhs[$i]->subsym as $subsp) {
                        if ($subsp === $sp) {
                            PHP_ParserGenerator::ErrorMsg($this->filename, 0,
                                "The start symbol \"%s\" occurs on the " .
                                "right-hand side of a rule. This will result in a parser which " .
                                "does not work properly.", $sp->name);
                            $this->errorcnt++;
                        }
                    }
                } elseif ($rp->rhs[$i] === $sp) {
                    PHP_ParserGenerator::ErrorMsg($this->filename, 0,
                        "The start symbol \"%s\" occurs on the " .
                        "right-hand side of a rule. This will result in a parser which " .
                        "does not work properly.", $sp->name);
                    $this->errorcnt++;
                }
            }
        }

        /* The basis configuration set for the first state
        ** is all rules which have the start symbol as their
        ** left-hand side */
        for ($rp = $sp->rule; $rp; $rp = $rp->nextlhs) {
            $newcfp = PHP_ParserGenerator_Config::Configlist_addbasis($rp, 0);
            $newcfp->fws[0] = 1;
        }

        /* Compute the first state.  All other states will be
        ** computed automatically during the computation of the first one.
        ** The returned pointer to the first state is not used. */
        $newstp = array();
        $newstp = $this->getstate();
        if (is_array($newstp)) {
            $this->buildshifts($newstp[0]); /* Recursively compute successor states */
        }
    }

    /**
     * @return PHP_ParserGenerator_State
     */
    private function getstate()
    {
        /* Extract the sorted basis of the new state.  The basis was constructed
        ** by prior calls to "Configlist_addbasis()". */
        PHP_ParserGenerator_Config::Configlist_sortbasis();
        $bp = PHP_ParserGenerator_Config::Configlist_basis();

        /* Get a state with the same basis */
        $stp = PHP_ParserGenerator_State::State_find($bp);
        if ($stp) {
            /* A state with the same basis already exists!  Copy all the follow-set
            ** propagation links from the state under construction into the
            ** preexisting state, then return a pointer to the preexisting state */
            for ($x = $bp, $y = $stp->bp; $x && $y; $x = $x->bp, $y = $y->bp) {
                PHP_ParserGenerator_PropagationLink::Plink_copy($y->bplp, $x->bplp);
                PHP_ParserGenerator_PropagationLink::Plink_delete($x->fplp);
                $x->fplp = $x->bplp = 0;
            }
            $cfp = PHP_ParserGenerator_Config::Configlist_return();
            PHP_ParserGenerator_Config::Configlist_eat($cfp);
        } else {
            /* This really is a new state.  Construct all the details */
            PHP_ParserGenerator_Config::Configlist_closure($this);    /* Compute the configuration closure */
            PHP_ParserGenerator_Config::Configlist_sort();           /* Sort the configuration closure */
            $cfp = PHP_ParserGenerator_Config::Configlist_return();   /* Get a pointer to the config list */
            $stp = new PHP_ParserGenerator_State;           /* A new state structure */
            $stp->bp = $bp;                /* Remember the configuration basis */
            $stp->cfp = $cfp;              /* Remember the configuration closure */
            $stp->statenum = $this->nstate++; /* Every state gets a sequence number */
            $stp->ap = 0;                 /* No actions, yet. */
            PHP_ParserGenerator_State::State_insert($stp, $stp->bp);   /* Add to the state table */
            // this can't work, recursion is too deep, move it into FindStates()
            //$this->buildshifts($stp);       /* Recursively compute successor states */
            return array($stp);
        }

        return $stp;
    }

    /**
     * Construct all successor states to the given state.  A "successor"
     * state is any state which can be reached by a shift action.
     * @param PHP_ParserGenerator_Data
     * @param PHP_ParserGenerator_State The state from which successors are computed
     */
    private function buildshifts(PHP_ParserGenerator_State $stp)
    {
//    struct config *cfp;  /* For looping thru the config closure of "stp" */
//    struct config *bcfp; /* For the inner loop on config closure of "stp" */
//    struct config *new;  /* */
//    struct symbol *sp;   /* Symbol following the dot in configuration "cfp" */
//    struct symbol *bsp;  /* Symbol following the dot in configuration "bcfp" */
//    struct state *newstp; /* A pointer to a successor state */

        /* Each configuration becomes complete after it contibutes to a successor
        ** state.  Initially, all configurations are incomplete */
        $cfp = $stp->cfp;
        for ($cfp = $stp->cfp; $cfp; $cfp = $cfp->next) {
            $cfp->status = PHP_ParserGenerator_Config::INCOMPLETE;
        }

        /* Loop through all configurations of the state "stp" */
        for ($cfp = $stp->cfp; $cfp; $cfp = $cfp->next) {
            if ($cfp->status == PHP_ParserGenerator_Config::COMPLETE) {
                continue;    /* Already used by inner loop */
            }
            if ($cfp->dot >= $cfp->rp->nrhs) {
                continue;  /* Can't shift this config */
            }
            PHP_ParserGenerator_Config::Configlist_reset();                      /* Reset the new config set */
            $sp = $cfp->rp->rhs[$cfp->dot];             /* Symbol after the dot */

            /* For every configuration in the state "stp" which has the symbol "sp"
            ** following its dot, add the same configuration to the basis set under
            ** construction but with the dot shifted one symbol to the right. */
            $bcfp = $cfp;
            for ($bcfp = $cfp; $bcfp; $bcfp = $bcfp->next) {
                if ($bcfp->status == PHP_ParserGenerator_Config::COMPLETE) {
                    continue;    /* Already used */
                }
                if ($bcfp->dot >= $bcfp->rp->nrhs) {
                    continue; /* Can't shift this one */
                }
                $bsp = $bcfp->rp->rhs[$bcfp->dot];           /* Get symbol after dot */
                if (!PHP_ParserGenerator_Symbol::same_symbol($bsp, $sp)) {
                    continue;      /* Must be same as for "cfp" */
                }
                $bcfp->status = PHP_ParserGenerator_Config::COMPLETE;             /* Mark this config as used */
                $new = PHP_ParserGenerator_Config::Configlist_addbasis($bcfp->rp, $bcfp->dot + 1);
                PHP_ParserGenerator_PropagationLink::Plink_add($new->bplp, $bcfp);
            }

            /* Get a pointer to the state described by the basis configuration set
            ** constructed in the preceding loop */
            $newstp = $this->getstate();
            if (is_array($newstp)) {
                $this->buildshifts($newstp[0]); /* Recursively compute successor states */
                $newstp = $newstp[0];
            }

            /* The state "newstp" is reached from the state "stp" by a shift action
            ** on the symbol "sp" */
            if ($sp->type == PHP_ParserGenerator_Symbol::MULTITERMINAL) {
                for ($i = 0; $i < $sp->nsubsym; $i++) {
                    PHP_ParserGenerator_Action::Action_add($stp->ap, PHP_ParserGenerator_Action::SHIFT, $sp->subsym[$i],
                                            $newstp);
                }
            } else {
                PHP_ParserGenerator_Action::Action_add($stp->ap, PHP_ParserGenerator_Action::SHIFT, $sp, $newstp);
            }
        }
    }

    /**
     * Construct the propagation links
     */
    public function FindLinks()
    {
        /* Housekeeping detail:
        ** Add to every propagate link a pointer back to the state to
        ** which the link is attached. */
        foreach ($this->sorted as $info) {
            $info->key->stp = $info->data;
        }

        /* Convert all backlinks into forward links.  Only the forward
        ** links are used in the follow-set computation. */
        for ($i = 0; $i < $this->nstate; $i++) {
            $stp = $this->sorted[$i];
            for ($cfp = $stp->data->cfp; $cfp; $cfp = $cfp->next) {
                for ($plp = $cfp->bplp; $plp; $plp = $plp->next) {
                    $other = $plp->cfp;
                    PHP_ParserGenerator_PropagationLink::Plink_add($other->fplp, $cfp);
                }
            }
        }
    }

    /**
     * Compute the reduce actions, and resolve conflicts.
     */
    public function FindActions()
    {
        /* Add all of the reduce actions
        ** A reduce action is added for each element of the followset of
        ** a configuration which has its dot at the extreme right.
        */
        for ($i = 0; $i < $this->nstate; $i++) {   /* Loop over all states */
            $stp = $this->sorted[$i]->data;
            for ($cfp = $stp->cfp; $cfp; $cfp = $cfp->next) {
                /* Loop over all configurations */
                if ($cfp->rp->nrhs == $cfp->dot) {        /* Is dot at extreme right? */
                    for ($j = 0; $j < $this->nterminal; $j++) {
                        if (isset($cfp->fws[$j])) {
                            /* Add a reduce action to the state "stp" which will reduce by the
                            ** rule "cfp->rp" if the lookahead symbol is "$this->symbols[j]" */
                            PHP_ParserGenerator_Action::Action_add($stp->ap, PHP_ParserGenerator_Action::REDUCE,
                                                    $this->symbols[$j], $cfp->rp);
                        }
                    }
                }
            }
        }

        /* Add the accepting token */
        if ($this->start instanceof PHP_ParserGenerator_Symbol) {
            $sp = PHP_ParserGenerator_Symbol::Symbol_find($this->start);
            if ($sp === 0) {
                $sp = $this->rule->lhs;
            }
        } else {
            $sp = $this->rule->lhs;
        }
        /* Add to the first state (which is always the starting state of the
        ** finite state machine) an action to ACCEPT if the lookahead is the
        ** start nonterminal.  */
        PHP_ParserGenerator_Action::Action_add($this->sorted[0]->data->ap, PHP_ParserGenerator_Action::ACCEPT, $sp, 0);

        /* Resolve conflicts */
        for ($i = 0; $i < $this->nstate; $i++) {
    //    struct action *ap, *nap;
    //    struct state *stp;
            $stp = $this->sorted[$i]->data;
            if (!$stp->ap) {
                throw new Exception('state has no actions associated');
            }
            echo 'processing state ' . $stp->statenum . " actions:\n";
            for ($ap = $stp->ap; $ap !== 0 && $ap->next !== 0; $ap = $ap->next) {
                echo '  Action ';
                $ap->display(true);
            }
            $stp->ap = PHP_ParserGenerator_Action::Action_sort($stp->ap);
            for ($ap = $stp->ap; $ap !== 0 && $ap->next !== 0; $ap = $ap->next) {
                for ($nap = $ap->next; $nap !== 0 && $nap->sp === $ap->sp ; $nap = $nap->next) {
                    /* The two actions "ap" and "nap" have the same lookahead.
                    ** Figure out which one should be used */
                    $this->nconflict += $this->resolve_conflict($ap, $nap, $this->errsym);
                }
            }
        }

        /* Report an error for each rule that can never be reduced. */
        for ($rp = $this->rule; $rp; $rp = $rp->next) {
            $rp->canReduce = false;
        }
        for ($i = 0; $i < $this->nstate; $i++) {
            for ($ap = $this->sorted[$i]->data->ap; $ap !== 0; $ap = $ap->next) {
                if ($ap->type == PHP_ParserGenerator_Action::REDUCE) {
                    $ap->x->canReduce = true;
                }
            }
        }
        for ($rp = $this->rule; $rp !== 0; $rp = $rp->next) {
            if ($rp->canReduce) {
                continue;
            }
            PHP_ParserGenerator::ErrorMsg($this->filename, $rp->ruleline, "This rule can not be reduced (is not connected to the start symbol).\n");
            $this->errorcnt++;
        }
    }

    /** Resolve a conflict between the two given actions.  If the
     * conflict can't be resolve, return non-zero.
     *
     * NO LONGER TRUE:
     *   To resolve a conflict, first look to see if either action
     *   is on an error rule.  In that case, take the action which
     *   is not associated with the error rule.  If neither or both
     *   actions are associated with an error rule, then try to
     *   use precedence to resolve the conflict.
     *
     * If either action is a SHIFT, then it must be apx.  This
     * function won't work if apx->type==REDUCE and apy->type==SHIFT.
     * @param PHP_ParserGenerator_Action
     * @param PHP_ParserGenerator_Action
     * @param PHP_ParserGenerator_Symbol|null The error symbol (if defined.  NULL otherwise)
     */
    public function resolve_conflict($apx, $apy, $errsym)
    {
        $errcnt = 0;
        if ($apx->sp !== $apy->sp) {
            throw new Exception('no conflict but resolve_conflict called');
        }
        if ($apx->type == PHP_ParserGenerator_Action::SHIFT && $apy->type == PHP_ParserGenerator_Action::REDUCE) {
            $spx = $apx->sp;
            $spy = $apy->x->precsym;
            if ($spy === 0 || $spx->prec < 0 || $spy->prec < 0) {
                /* Not enough precedence information. */
                $apy->type = PHP_ParserGenerator_Action::CONFLICT;
                $errcnt++;
            } elseif ($spx->prec > $spy->prec) {    /* Lower precedence wins */
                $apy->type = PHP_ParserGenerator_Action::RD_RESOLVED;
            } elseif ($spx->prec < $spy->prec) {
                $apx->type = PHP_ParserGenerator_Action::SH_RESOLVED;
            } elseif ($spx->prec === $spy->prec && $spx->assoc == PHP_ParserGenerator_Symbol::RIGHT) {
                /* Use operator */
                $apy->type = PHP_ParserGenerator_Action::RD_RESOLVED;                       /* associativity */
            } elseif ($spx->prec === $spy->prec && $spx->assoc == PHP_ParserGenerator_Symbol::LEFT) {
                /* to break tie */
                $apx->type = PHP_ParserGenerator_Action::SH_RESOLVED;
            } else {
                if ($spx->prec !== $spy->prec || $spx->assoc !== PHP_ParserGenerator_Symbol::NONE) {
                    throw new Exception('$spx->prec !== $spy->prec || $spx->assoc !== PHP_ParserGenerator_Symbol::NONE');
                }
                $apy->type = PHP_ParserGenerator_Action::CONFLICT;
                $errcnt++;
            }
        } elseif ($apx->type == PHP_ParserGenerator_Action::REDUCE && $apy->type == PHP_ParserGenerator_Action::REDUCE) {
            $spx = $apx->x->precsym;
            $spy = $apy->x->precsym;
            if ($spx === 0 || $spy === 0 || $spx->prec < 0 ||
                  $spy->prec < 0 || $spx->prec === $spy->prec) {
                $apy->type = PHP_ParserGenerator_Action::CONFLICT;
                $errcnt++;
            } elseif ($spx->prec > $spy->prec) {
                $apy->type = PHP_ParserGenerator_Action::RD_RESOLVED;
            } elseif ($spx->prec < $spy->prec) {
                $apx->type = PHP_ParserGenerator_Action::RD_RESOLVED;
            }
        } else {
            if ($apx->type!== PHP_ParserGenerator_Action::SH_RESOLVED &&
                $apx->type!== PHP_ParserGenerator_Action::RD_RESOLVED &&
                $apx->type!== PHP_ParserGenerator_Action::CONFLICT &&
                $apy->type!== PHP_ParserGenerator_Action::SH_RESOLVED &&
                $apy->type!== PHP_ParserGenerator_Action::RD_RESOLVED &&
                $apy->type!== PHP_ParserGenerator_Action::CONFLICT) {
                throw new Exception('$apx->type!== PHP_ParserGenerator_Action::SH_RESOLVED &&
                $apx->type!== PHP_ParserGenerator_Action::RD_RESOLVED &&
                $apx->type!== PHP_ParserGenerator_Action::CONFLICT &&
                $apy->type!== PHP_ParserGenerator_Action::SH_RESOLVED &&
                $apy->type!== PHP_ParserGenerator_Action::RD_RESOLVED &&
                $apy->type!== PHP_ParserGenerator_Action::CONFLICT');
            }
            /* The REDUCE/SHIFT case cannot happen because SHIFTs come before
            ** REDUCEs on the list.  If we reach this point it must be because
            ** the parser conflict had already been resolved. */
        }

        return $errcnt;
    }

    /**
     * Reduce the size of the action tables, if possible, by making use
     * of defaults.
     *
     * In this version, we take the most frequent REDUCE action and make
     * it the default.
     */
    public function CompressTables()
    {
        for ($i = 0; $i < $this->nstate; $i++) {
            $stp = $this->sorted[$i]->data;
            $nbest = 0;
            $rbest = 0;

            for ($ap = $stp->ap; $ap; $ap = $ap->next) {
                if ($ap->type != PHP_ParserGenerator_Action::REDUCE) {
                    continue;
                }
                $rp = $ap->x;
                if ($rp === $rbest) {
                    continue;
                }
                $n = 1;
                for ($ap2 = $ap->next; $ap2; $ap2 = $ap2->next) {
                    if ($ap2->type != PHP_ParserGenerator_Action::REDUCE) {
                        continue;
                    }
                    $rp2 = $ap2->x;
                    if ($rp2 === $rbest) {
                        continue;
                    }
                    if ($rp2 === $rp) {
                        $n++;
                    }
                }
                if ($n > $nbest) {
                    $nbest = $n;
                    $rbest = $rp;
                }
            }

            /* Do not make a default if the number of rules to default
            ** is not at least 1 */
            if ($nbest < 1) {
                continue;
            }

            /* Combine matching REDUCE actions into a single default */
            for ($ap = $stp->ap; $ap; $ap = $ap->next) {
                if ($ap->type == PHP_ParserGenerator_Action::REDUCE && $ap->x === $rbest) {
                    break;
                }
            }
            if ($ap === 0) {
                throw new Exception('$ap is not an object');
            }
            $ap->sp = PHP_ParserGenerator_Symbol::Symbol_new("{default}");
            for ($ap = $ap->next; $ap; $ap = $ap->next) {
                if ($ap->type == PHP_ParserGenerator_Action::REDUCE && $ap->x === $rbest) {
                    $ap->type = PHP_ParserGenerator_Action::NOT_USED;
                }
            }
            $stp->ap = PHP_ParserGenerator_Action::Action_sort($stp->ap);
        }
    }

    /**
     * Renumber and resort states so that states with fewer choices
     * occur at the end.  Except, keep state 0 as the first state.
     */
    public function ResortStates()
    {
        for ($i = 0; $i < $this->nstate; $i++) {
            $stp = $this->sorted[$i]->data;
            $stp->nTknAct = $stp->nNtAct = 0;
            $stp->iDflt = $this->nstate + $this->nrule;
            $stp->iTknOfst = PHP_ParserGenerator_Data::NO_OFFSET;
            $stp->iNtOfst = PHP_ParserGenerator_Data::NO_OFFSET;
            for ($ap = $stp->ap; $ap; $ap = $ap->next) {
                if ($this->compute_action($ap) >= 0) {
                    if ($ap->sp->index < $this->nterminal) {
                        $stp->nTknAct++;
                    } elseif ($ap->sp->index < $this->nsymbol) {
                        $stp->nNtAct++;
                    } else {
                        $stp->iDflt = $this->compute_action($ap);
                    }
                }
            }
            $this->sorted[$i] = $stp;
        }
        $save = $this->sorted[0];
        unset($this->sorted[0]);
        usort($this->sorted, array('PHP_ParserGenerator_State', 'stateResortCompare'));
        array_unshift($this->sorted, $save);
        for ($i = 0; $i < $this->nstate; $i++) {
            $this->sorted[$i]->statenum = $i;
        }
    }

    /**
     * Given an action, compute the integer value for that action
     * which is to be put in the action table of the generated machine.
     * Return negative if no action should be generated.
     * @param PHP_ParserGenerator_Action
     */
    public function compute_action($ap)
    {
        switch ($ap->type) {
            case PHP_ParserGenerator_Action::SHIFT:
                $act = $ap->x->statenum;
                break;
            case PHP_ParserGenerator_Action::REDUCE:
                $act = $ap->x->index + $this->nstate;
                break;
            case PHP_ParserGenerator_Action::ERROR:
                $act = $this->nstate + $this->nrule;
                break;
            case PHP_ParserGenerator_Action::ACCEPT:
                $act = $this->nstate + $this->nrule + 1;
                break;
            default:
                $act = -1;
                break;
        }

        return $act;
    }

    /**
     * Generate the "Parse.out" log file
     */
    public function ReportOutput()
    {
        $fp = fopen($this->filenosuffix . ".out", "wb");
        if (!$fp) {
            return;
        }
        for ($i = 0; $i < $this->nstate; $i++) {
            $stp = $this->sorted[$i];
            fprintf($fp, "State %d:\n", $stp->statenum);
            if ($this->basisflag) {
                $cfp = $stp->bp;
            } else {
                $cfp = $stp->cfp;
            } while ($cfp) {
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
                if ($this->basisflag) {
                    $cfp = $cfp->bp;
                } else {
                    $cfp = $cfp->next;
                }
            }
            fwrite($fp, "\n");
            for ($ap = $stp->ap; $ap; $ap = $ap->next) {
                if ($ap->PrintAction($fp, 30)) {
                    fprintf($fp,"\n");
                }
            }
            fwrite($fp,"\n");
        }
        fclose($fp);
    }

    /**
     * The next function finds the template file and opens it, returning
     * a pointer to the opened file.
     * @return resource
     */
    private function tplt_open()
    {
        $templatename = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . "Lempar.php";
        $buf = $this->filenosuffix . '.lt';
        if (file_exists($buf) && is_readable($buf)) {
            $tpltname = $buf;
        } elseif (file_exists($templatename) && is_readable($templatename)) {
            $tpltname = $templatename;
        } elseif ($fp = @fopen($templatename, 'rb', true)) {
            return $fp;
        }
        if (!isset($tpltname)) {
            echo "Can't find the parser driver template file \"%s\".\n",
                $templatename;
            $this->errorcnt++;

            return 0;
        }
        $in = @fopen($tpltname,"rb");
        if (!$in) {
            printf("Can't open the template file \"%s\".\n", $tpltname);
            $this->errorcnt++;

            return 0;
        }

        return $in;
    }

#define LINESIZE 1000
    /**#@+
     * The next cluster of routines are for reading the template file
     * and writing the results to the generated parser
     */
    /**
     * The first function transfers data from "in" to "out" until
     * a line is seen which begins with "%%".  The line number is
     * tracked.
     *
     * if name!=0, then any word that begin with "Parse" is changed to
     * begin with *name instead.
     */
    private function tplt_xfer($name, $in, $out, &$lineno)
    {
        while (($line = fgets($in, 1024)) && ($line[0] != '%' || $line[1] != '%')) {
            $lineno++;
            $iStart = 0;
            if ($name) {
                for ($i = 0; $i < strlen($line); $i++) {
                    if ($line[$i] == 'P' && substr($line, $i, 5) == "Parse"
                          && ($i === 0 || preg_match('/[^a-zA-Z]/', $line[$i - 1]))) {
                        if ($i > $iStart) {
                            fwrite($out, substr($line, $iStart, $i - $iStart));
                        }
                        fwrite($out, $name);
                        $i += 4;
                        $iStart = $i + 1;
                    }
                }
            }
            fwrite($out, substr($line, $iStart));
        }
    }

    /**
     * Print a #line directive line to the output file.
     */
    private function tplt_linedir($out, $lineno, $filename)
    {
        fwrite($out, '// line ' . intval($lineno) . ' "' . $filename . "\"\n");
    }

    /**
     * Print a string to the file and keep the linenumber up to date
     */
    private function tplt_print($out, $str, $strln, &$lineno)
    {
        if ($str == '' || $str == 0) {
            return;
        }
        $this->tplt_linedir($out, $strln, $this->filename);
        $lineno++;
        fwrite($out, $str);
        $lineno += count(explode("\n", $str)) - 1;
        //$this->tplt_linedir($out, $lineno + 2, $this->outname);
        $lineno += 2;
    }
    /**#@-*/

    /**
     * Compute all followsets.
     *
     * A followset is the set of all symbols which can come immediately
     * after a configuration.
     */
    public function FindFollowSets()
    {
        for ($i = 0; $i < $this->nstate; $i++) {
            for ($cfp = $this->sorted[$i]->data->cfp; $cfp; $cfp = $cfp->next) {
                $cfp->status = PHP_ParserGenerator_Config::INCOMPLETE;
            }
        }

        do {
            $progress = 0;
            for ($i = 0; $i < $this->nstate; $i++) {
                for ($cfp = $this->sorted[$i]->data->cfp; $cfp; $cfp = $cfp->next) {
                    if ($cfp->status == PHP_ParserGenerator_Config::COMPLETE) {
                        continue;
                    }
                    for ($plp = $cfp->fplp; $plp; $plp = $plp->next) {
                        $a = array_diff_key($cfp->fws, $plp->cfp->fws);
                        if (count($a)) {
                            $plp->cfp->fws += $a;
                            $plp->cfp->status = PHP_ParserGenerator_Config::INCOMPLETE;
                            $progress = 1;
                        }
                    }
                    $cfp->status = PHP_ParserGenerator_Config::COMPLETE;
                }
            }
        } while ($progress);
    }

    /**
     * Generate C source code for the parser
     * @param int Output in makeheaders format if true
     */
    public function ReportTable($mhflag)
    {
//        FILE *out, *in;
//        char line[LINESIZE];
//        int  lineno;
//        struct state *stp;
//        struct action *ap;
//        struct rule *rp;
//        struct acttab *pActtab;
//        int i, j, n;
//        char *name;
//        int mnTknOfst, mxTknOfst;
//        int mnNtOfst, mxNtOfst;
//        struct axset *ax;

        $in = $this->tplt_open();
        if (!$in) {
            return;
        }
        $out = fopen($this->filenosuffix . ".php", "wb");
        if (!$out) {
            fclose($in);

            return;
        }
        $this->outname = $this->filenosuffix . ".php";
        $lineno = 1;
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate the include code, if any */
        $this->tplt_print($out, $this->include_code, $this->includeln, $lineno);
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate the class declaration code */
        $this->tplt_print($out, $this->declare_classcode, $this->declare_classln,
            $lineno);
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate the internal parser class include code, if any */
        $this->tplt_print($out, $this->include_classcode, $this->include_classln,
            $lineno);
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate #defines for all tokens */
        //if ($mhflag) {
            //fprintf($out, "#if INTERFACE\n");
            $lineno++;
            if ($this->tokenprefix) {
                $prefix = $this->tokenprefix;
            } else {
                $prefix = '';
            }
            for ($i = 1; $i < $this->nterminal; $i++) {
                fprintf($out, "    const %s%-30s = %2d;\n", $prefix, $this->symbols[$i]->name, $i);
                $lineno++;
            }
            //fwrite($out, "#endif\n");
            $lineno++;
        //}
        fwrite($out, "    const YY_NO_ACTION = " .
            ($this->nstate + $this->nrule + 2) . ";\n");
        $lineno++;
        fwrite($out, "    const YY_ACCEPT_ACTION = " .
            ($this->nstate + $this->nrule + 1) . ";\n");
        $lineno++;
        fwrite($out, "    const YY_ERROR_ACTION = " .
            ($this->nstate + $this->nrule) . ";\n");
        $lineno++;
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate the action table and its associates:
        **
        **  yy_action[]        A single table containing all actions.
        **  yy_lookahead[]     A table containing the lookahead for each entry in
        **                     yy_action.  Used to detect hash collisions.
        **  yy_shift_ofst[]    For each state, the offset into yy_action for
        **                     shifting terminals.
        **  yy_reduce_ofst[]   For each state, the offset into yy_action for
        **                     shifting non-terminals after a reduce.
        **  yy_default[]       Default action for each state.
        */

        /* Compute the actions on all states and count them up */

        $ax = array();
        for ($i = 0; $i < $this->nstate; $i++) {
            $stp = $this->sorted[$i];
            $ax[$i * 2] = array();
            $ax[$i * 2]['stp'] = $stp;
            $ax[$i * 2]['isTkn'] = 1;
            $ax[$i * 2]['nAction'] = $stp->nTknAct;
            $ax[$i * 2 + 1] = array();
            $ax[$i * 2 + 1]['stp'] = $stp;
            $ax[$i * 2 + 1]['isTkn'] = 0;
            $ax[$i * 2 + 1]['nAction'] = $stp->nNtAct;
        }
        $mxTknOfst = $mnTknOfst = 0;
        $mxNtOfst = $mnNtOfst = 0;

        /* Compute the action table.  In order to try to keep the size of the
        ** action table to a minimum, the heuristic of placing the largest action
        ** sets first is used.
        */

        usort($ax, array('PHP_ParserGenerator_Data', 'axset_compare'));
        $pActtab = new PHP_ParserGenerator_ActionTable;
        for ($i = 0; $i < $this->nstate * 2 && $ax[$i]['nAction'] > 0; $i++) {
            $stp = $ax[$i]['stp'];
            if ($ax[$i]['isTkn']) {
                for ($ap = $stp->ap; $ap; $ap = $ap->next) {
                    if ($ap->sp->index >= $this->nterminal) {
                        continue;
                    }
                    $action = $this->compute_action($ap);
                    if ($action < 0) {
                        continue;
                    }
                    $pActtab->acttab_action($ap->sp->index, $action);
                }
                $stp->iTknOfst = $pActtab->acttab_insert();
                if ($stp->iTknOfst < $mnTknOfst) {
                    $mnTknOfst = $stp->iTknOfst;
                }
                if ($stp->iTknOfst > $mxTknOfst) {
                    $mxTknOfst = $stp->iTknOfst;
                }
            } else {
                for ($ap = $stp->ap; $ap; $ap = $ap->next) {
                    if ($ap->sp->index < $this->nterminal) {
                        continue;
                    }
                    if ($ap->sp->index == $this->nsymbol) {
                        continue;
                    }
                    $action = $this->compute_action($ap);
                    if ($action < 0) {
                        continue;
                    }
                    $pActtab->acttab_action($ap->sp->index, $action);
                }
                $stp->iNtOfst = $pActtab->acttab_insert();
                if ($stp->iNtOfst < $mnNtOfst) {
                    $mnNtOfst = $stp->iNtOfst;
                }
                if ($stp->iNtOfst > $mxNtOfst) {
                    $mxNtOfst = $stp->iNtOfst;
                }
            }
        }
        /* Output the yy_action table */

        fprintf($out, "    const YY_SZ_ACTTAB = %d;\n", $pActtab->nAction);
        $lineno++;
        fwrite($out, "public static \$yy_action = array(\n");
        $lineno++;
        $n = $pActtab->nAction;
        for ($i = $j = 0; $i < $n; $i++) {
            $action = $pActtab->aAction[$i]['action'];
            if ($action < 0) {
                $action = $this->nsymbol + $this->nrule + 2;
            }
            // change next line
            if ($j === 0) {
                fprintf($out, " /* %5d */ ", $i);
            }
            fprintf($out, " %4d,", $action);
            if ($j == 9 || $i == $n - 1) {
                fwrite($out, "\n");
                $lineno++;
                $j = 0;
            } else {
                $j++;
            }
        }
        fwrite($out, "    );\n"); $lineno++;

        /* Output the yy_lookahead table */

        fwrite($out, "    public static \$yy_lookahead = array(\n");
        $lineno++;
        for ($i = $j = 0; $i < $n; $i++) {
            $la = $pActtab->aAction[$i]['lookahead'];
            if ($la < 0) {
                $la = $this->nsymbol;
            }
            // change next line
            if ($j === 0) {
                fprintf($out, " /* %5d */ ", $i);
            }
            fprintf($out, " %4d,", $la);
            if ($j == 9 || $i == $n - 1) {
                fwrite($out, "\n");
                $lineno++;
                $j = 0;
            } else {
                $j++;
            }
        }
        fwrite($out, ");\n");
        $lineno++;

        /* Output the yy_shift_ofst[] table */
        fprintf($out, "    const YY_SHIFT_USE_DFLT = %d;\n", $mnTknOfst - 1);
        $lineno++;
        $n = $this->nstate;
        while ($n > 0 && $this->sorted[$n - 1]->iTknOfst == PHP_ParserGenerator_Data::NO_OFFSET) {
            $n--;
        }
        fprintf($out, "    const YY_SHIFT_MAX = %d;\n", $n - 1);
        $lineno++;
        fwrite($out, "    public static \$yy_shift_ofst = array(\n");
        $lineno++;
        for ($i = $j = 0; $i < $n; $i++) {
            $stp = $this->sorted[$i];
            $ofst = $stp->iTknOfst;
            if ($ofst === PHP_ParserGenerator_Data::NO_OFFSET) {
                $ofst = $mnTknOfst - 1;
            }
            // change next line
            if ($j === 0) {
                fprintf($out, " /* %5d */ ", $i);
            }
            fprintf($out, " %4d,", $ofst);
            if ($j == 9 || $i == $n - 1) {
                fwrite($out, "\n");
                $lineno++;
                $j = 0;
            } else {
                $j++;
            }
        }
        fwrite($out, ");\n");
        $lineno++;

        /* Output the yy_reduce_ofst[] table */

        fprintf($out, "    const YY_REDUCE_USE_DFLT = %d;\n", $mnNtOfst - 1);
        $lineno++;
        $n = $this->nstate;
        while ($n > 0 && $this->sorted[$n - 1]->iNtOfst == PHP_ParserGenerator_Data::NO_OFFSET) {
            $n--;
        }
        fprintf($out, "    const YY_REDUCE_MAX = %d;\n", $n - 1);
        $lineno++;
        fwrite($out, "    public static \$yy_reduce_ofst = array(\n");
        $lineno++;
        for ($i = $j = 0; $i < $n; $i++) {
            $stp = $this->sorted[$i];
            $ofst = $stp->iNtOfst;
            if ($ofst == PHP_ParserGenerator_Data::NO_OFFSET) {
                $ofst = $mnNtOfst - 1;
            }
            // change next line
            if ($j == 0) {
                fprintf($out, " /* %5d */ ", $i);
            }
            fprintf($out, " %4d,", $ofst);
            if ($j == 9 || $i == $n - 1) {
                fwrite($out, "\n");
                $lineno++;
                $j = 0;
            } else {
                $j++;
            }
        }
        fwrite($out, ");\n");
        $lineno++;

        /* Output the expected tokens table */

        fwrite($out, "    public static \$yyExpectedTokens = array(\n");
        $lineno++;
        for ($i = 0; $i < $this->nstate; $i++) {
            $stp = $this->sorted[$i];
            fwrite($out, "        /* $i */ array(");
            for ($ap = $stp->ap; $ap; $ap = $ap->next) {
                if ($ap->sp->index < $this->nterminal) {
                    if ($ap->type == PHP_ParserGenerator_Action::SHIFT ||
                          $ap->type == PHP_ParserGenerator_Action::REDUCE) {
                        fwrite($out, $ap->sp->index . ', ');
                    }
                }
            }
            fwrite($out, "),\n");
            $lineno++;
        }
        fwrite($out, ");\n");
        $lineno++;

        /* Output the default action table */

        fwrite($out, "    public static \$yy_default = array(\n");
        $lineno++;
        $n = $this->nstate;
        for ($i = $j = 0; $i < $n; $i++) {
            $stp = $this->sorted[$i];
            // change next line
            if ($j == 0) {
                fprintf($out, " /* %5d */ ", $i);
            }
            fprintf($out, " %4d,", $stp->iDflt);
            if ($j == 9 || $i == $n - 1) {
                fprintf($out, "\n"); $lineno++;
                $j = 0;
            } else {
                $j++;
            }
        }
        fwrite($out, ");\n");
        $lineno++;
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate the defines */
        fprintf($out, "    const YYNOCODE = %d;\n", $this->nsymbol + 1);
        $lineno++;
        if ($this->stacksize) {
            if ($this->stacksize <= 0) {
                PHP_ParserGenerator::ErrorMsg($this->filename, 0,
                    "Illegal stack size: [%s].  The stack size should be an integer constant.",
                    $this->stacksize);
                $this->errorcnt++;
                $this->stacksize = "100";
            }
            fprintf($out, "    const YYSTACKDEPTH = %s;\n", $this->stacksize);
            $lineno++;
        } else {
            fwrite($out,"    const YYSTACKDEPTH = 100;\n");
            $lineno++;
        }
        fprintf($out, "    const YYNSTATE = %d;\n", $this->nstate);
        $lineno++;
        fprintf($out, "    const YYNRULE = %d;\n", $this->nrule);
        $lineno++;
        fprintf($out, "    const YYERRORSYMBOL = %d;\n", $this->errsym->index);
        $lineno++;
        fprintf($out, "    const YYERRSYMDT = 'yy%d';\n", $this->errsym->dtnum);
        $lineno++;
        if ($this->has_fallback) {
            fwrite($out, "    const YYFALLBACK = 1;\n");
        } else {
            fwrite($out, "    const YYFALLBACK = 0;\n");
        }
        $lineno++;
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate the table of fallback tokens.
        */

        if ($this->has_fallback) {
            for ($i = 0; $i < $this->nterminal; $i++) {
                $p = $this->symbols[$i];
                if ($p->fallback === 0) {
                    // change next line
                    fprintf($out, "    0,  /* %10s => nothing */\n", $p->name);
                } else {
                    // change next line
                    fprintf($out, "  %3d,  /* %10s => %s */\n",
                        $p->fallback->index, $p->name, $p->fallback->name);
                }
                $lineno++;
            }
        }
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate a table containing the symbolic name of every symbol
            ($yyTokenName)
        */

        for ($i = 0; $i < $this->nsymbol; $i++) {
            fprintf($out,"  %-15s", "'" . $this->symbols[$i]->name . "',");
            if (($i & 3) == 3) {
                fwrite($out,"\n");
                $lineno++;
            }
        }
        if (($i & 3) != 0) {
            fwrite($out, "\n");
            $lineno++;
        }
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate a table containing a text string that describes every
        ** rule in the rule set of the grammer.  This information is used
        ** when tracing REDUCE actions.
        */

        for ($i = 0, $rp = $this->rule; $rp; $rp = $rp->next, $i++) {
            if ($rp->index !== $i) {
                throw new Exception('rp->index != i and should be');
            }
            // change next line
            fprintf($out, " /* %3d */ '%s ::=", $i, $rp->lhs->name);
            for ($j = 0; $j < $rp->nrhs; $j++) {
                $sp = $rp->rhs[$j];
                fwrite($out,' ' . $sp->name);
                if ($sp->type == PHP_ParserGenerator_Symbol::MULTITERMINAL) {
                    for ($k = 1; $k < $sp->nsubsym; $k++) {
                        fwrite($out, '|' . $sp->subsym[$k]->name);
                    }
                }
            }
            fwrite($out, "',\n");
            $lineno++;
        }
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate code which executes every time a symbol is popped from
        ** the stack while processing errors or while destroying the parser.
        ** (In other words, generate the %destructor actions)
        */

        if ($this->tokendest) {
            for ($i = 0; $i < $this->nsymbol; $i++) {
                $sp = $this->symbols[$i];
                if ($sp === 0 || $sp->type != PHP_ParserGenerator_Symbol::TERMINAL) {
                    continue;
                }
                fprintf($out, "    case %d:\n", $sp->index);
                $lineno++;
            }
            for ($i = 0; $i < $this->nsymbol &&
                         $this->symbols[$i]->type != PHP_ParserGenerator_Symbol::TERMINAL; $i++);
            if ($i < $this->nsymbol) {
                $this->emit_destructor_code($out, $this->symbols[$i], $lineno);
                fprintf($out, "      break;\n");
                $lineno++;
            }
        }
        if ($this->vardest) {
            $dflt_sp = 0;
            for ($i = 0; $i < $this->nsymbol; $i++) {
                $sp = $this->symbols[$i];
                if ($sp === 0 || $sp->type == PHP_ParserGenerator_Symbol::TERMINAL ||
                      $sp->index <= 0 || $sp->destructor != 0) {
                    continue;
                }
                fprintf($out, "    case %d:\n", $sp->index);
                $lineno++;
                $dflt_sp = $sp;
            }
            if ($dflt_sp != 0) {
                $this->emit_destructor_code($out, $dflt_sp, $lineno);
                fwrite($out, "      break;\n");
                $lineno++;
            }
        }
        for ($i = 0; $i < $this->nsymbol; $i++) {
            $sp = $this->symbols[$i];
            if ($sp === 0 || $sp->type == PHP_ParserGenerator_Symbol::TERMINAL ||
                  $sp->destructor === 0) {
                continue;
            }
            fprintf($out, "    case %d:\n", $sp->index);
            $lineno++;

            /* Combine duplicate destructors into a single case */

            for ($j = $i + 1; $j < $this->nsymbol; $j++) {
                $sp2 = $this->symbols[$j];
                if ($sp2 && $sp2->type != PHP_ParserGenerator_Symbol::TERMINAL && $sp2->destructor
                      && $sp2->dtnum == $sp->dtnum
                      && $sp->destructor == $sp2->destructor) {
                    fprintf($out, "    case %d:\n", $sp2->index);
                    $lineno++;
                    $sp2->destructor = 0;
                }
            }

            $this->emit_destructor_code($out, $this->symbols[$i], $lineno);
            fprintf($out, "      break;\n");
            $lineno++;
        }
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate code which executes whenever the parser stack overflows */

        $this->tplt_print($out, $this->overflow, $this->overflowln, $lineno);
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate the table of rule information
        **
        ** Note: This code depends on the fact that rules are number
        ** sequentually beginning with 0.
        */

        for ($rp = $this->rule; $rp; $rp = $rp->next) {
            fprintf($out, "  array( 'lhs' => %d, 'rhs' => %d ),\n",
                $rp->lhs->index, $rp->nrhs);
            $lineno++;
        }
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate code which executes during each REDUCE action */

        for ($rp = $this->rule; $rp; $rp = $rp->next) {
            if ($rp->code) {
                $this->translate_code($rp);
            }
        }

        /* Generate the method map for each REDUCE action */

        for ($rp = $this->rule; $rp; $rp = $rp->next) {
            if ($rp->code === 0) {
                continue;
            }
            fwrite($out, '        ' . $rp->index . ' => ' . $rp->index . ",\n");
            $lineno++;
            for ($rp2 = $rp->next; $rp2; $rp2 = $rp2->next) {
                if ($rp2->code === $rp->code) {
                    fwrite($out, '        ' . $rp2->index . ' => ' .
                        $rp->index . ",\n");
                    $lineno++;
                    $rp2->code = 0;
                }
            }
        }
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        for ($rp = $this->rule; $rp; $rp = $rp->next) {
            if ($rp->code === 0) {
                continue;
            }
            $this->emit_code($out, $rp, $lineno);
        }
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate code which executes if a parse fails */

        $this->tplt_print($out, $this->failure, $this->failureln, $lineno);
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate code which executes when a syntax error occurs */

        $this->tplt_print($out, $this->error, $this->errorln, $lineno);
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate code which executes when the parser accepts its input */

        $this->tplt_print($out, $this->accept, $this->acceptln, $lineno);
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Append any addition code the user desires */

        $this->tplt_print($out, $this->extracode, $this->extracodeln, $lineno);

        fclose($in);
        fwrite($out, "\n");
        fclose($out);
    }

    /**
     * Generate code which executes when the rule "rp" is reduced.  Write
     * the code to "out".  Make sure lineno stays up-to-date.
     */
    public function emit_code($out, PHP_ParserGenerator_Rule $rp, &$lineno)
    {
        $linecnt = 0;

        /* Generate code to do the reduce action */
        if ($rp->code) {
            $this->tplt_linedir($out, $rp->line, $this->filename);
            fwrite($out, "    public function yy_r$rp->index()".'{' . $rp->code);
            $linecnt += count(explode("\n", $rp->code)) - 1;
            $lineno += 3 + $linecnt;
            fwrite($out, "    }\n");
            //$this->tplt_linedir($out, $lineno, $this->outname);
        } /* End if( rp->code ) */
    }

    /**
     * Append text to a dynamically allocated string.  If zText is 0 then
     * reset the string to be empty again.  Always return the complete text
     * of the string (which is overwritten with each call).
     *
     * n bytes of zText are stored.  If n==0 then all of zText is stored.
     *
     * If n==-1, then the previous character is overwritten.
     * @param string
     * @param int
     */
    public function append_str($zText, $n)
    {
        static $z = '';
        $zInt = '';

        if ($zText === '') {
            $ret = $z;
            $z = '';

            return $ret;
        }
        if ($n <= 0) {
            if ($n < 0) {
                if (!strlen($z)) {
                    throw new Exception('z is zero-length');
                }
                $z = substr($z, 0, strlen($z) - 1);
                if (!$z) {
                    $z = '';
                }
            }
            $n = strlen($zText);
        }
        $i = 0;
        $z .= substr($zText, 0, $n);

        return $z;
    }

    /**
     * zCode is a string that is the action associated with a rule.  Expand
     * the symbols in this string so that the refer to elements of the parser
     * stack.
     */
    public function translate_code(PHP_ParserGenerator_Rule $rp)
    {
        $lhsused = 0;    /* True if the LHS element has been used */
        $used = array();   /* True for each RHS element which is used */

        for ($i = 0; $i < $rp->nrhs; $i++) {
            $used[$i] = 0;
        }

        $this->append_str('', 0);
        for ($i = 0; $i < strlen($rp->code); $i++) {
            $cp = $rp->code[$i];
            if (preg_match('/[A-Za-z]/', $cp) &&
                 ($i === 0 || (!preg_match('/[A-Za-z0-9_]/', $rp->code[$i - 1])))) {
                //*xp = 0;
                // previous line is in essence a temporary substr, so
                // we will simulate it
                $test = substr($rp->code, $i);
                preg_match('/[A-Za-z0-9_]+/', $test, $matches);
                $tempcp = $matches[0];
                $j = strlen($tempcp) + $i;
                if ($rp->lhsalias && $tempcp == $rp->lhsalias) {
                    $this->append_str("\$this->_retvalue", 0);
                    $cp = $rp->code[$j];
                    $i = $j;
                    $lhsused = 1;
                } else {
                    for ($ii = 0; $ii < $rp->nrhs; $ii++) {
                        if ($rp->rhsalias[$ii] && $tempcp == $rp->rhsalias[$ii]) {
                            if ($ii !== 0 && $rp->code[$ii - 1] == '@') {
                                /* If the argument is of the form @X then substitute
                                ** the token number of X, not the value of X */
                                $this->append_str("\$this->yystack[\$this->yyidx + " .
                                    ($ii - $rp->nrhs + 1) . "]->major", -1);
                            } else {
                                $sp = $rp->rhs[$ii];
                                if ($sp->type == PHP_ParserGenerator_Symbol::MULTITERMINAL) {
                                    $dtnum = $sp->subsym[0]->dtnum;
                                } else {
                                    $dtnum = $sp->dtnum;
                                }
                                $this->append_str("\$this->yystack[\$this->yyidx + " .
                                    ($ii - $rp->nrhs + 1) . "]->minor", 0);
                            }
                            $cp = $rp->code[$j];
                            $i = $j;
                            $used[$ii] = 1;
                            break;
                        }
                    }
                }
            }
            $this->append_str($cp, 1);
        } /* End loop */

        /* Check to make sure the LHS has been used */
        if ($rp->lhsalias && !$lhsused) {
            PHP_ParserGenerator::ErrorMsg($this->filename, $rp->ruleline,
                "Label \"%s\" for \"%s(%s)\" is never used.",
                $rp->lhsalias, $rp->lhs->name, $rp->lhsalias);
                $this->errorcnt++;
        }

        /* Generate destructor code for RHS symbols which are not used in the
        ** reduce code */
        for ($i = 0; $i < $rp->nrhs; $i++) {
            if ($rp->rhsalias[$i] && !isset($used[$i])) {
                PHP_ParserGenerator::ErrorMsg($this->filename, $rp->ruleline,
                    "Label %s for \"%s(%s)\" is never used.",
                    $rp->rhsalias[$i], $rp->rhs[$i]->name, $rp->rhsalias[$i]);
                $this->errorcnt++;
            } elseif ($rp->rhsalias[$i] == 0) {
                if ($rp->rhs[$i]->type == PHP_ParserGenerator_Symbol::TERMINAL) {
                    $hasdestructor = $this->tokendest != 0;
                } else {
                    $hasdestructor = $this->vardest !== 0 || $rp->rhs[$i]->destructor !== 0;
                }
                if ($hasdestructor) {
                    $this->append_str("  \$this->yy_destructor(" .
                        ($rp->rhs[$i]->index) . ", \$this->yystack[\$this->yyidx + " .
                        ($i - $rp->nrhs + 1) . "]->minor);\n", 0);
                } else {
                    /* No destructor defined for this term */
                }
            }
        }
        $cp = $this->append_str('', 0);
        $rp->code = $cp;
    }

    /**
     * The following routine emits code for the destructor for the
     * symbol sp
     */
    public function emit_destructor_code($out, PHP_ParserGenerator_Symbol $sp, &$lineno)
//    FILE *out;
//    struct symbol *sp;
//    struct lemon *lemp;
//    int *lineno;
    {
        $cp = 0;

        $linecnt = 0;
        if ($sp->type == PHP_ParserGenerator_Symbol::TERMINAL) {
            $cp = $this->tokendest;
            if ($cp === 0) {
                return;
            }
            $this->tplt_linedir($out, $this->tokendestln, $this->filename);
            fwrite($out, "{");
        } elseif ($sp->destructor) {
            $cp = $sp->destructor;
            $this->tplt_linedir($out, $sp->destructorln, $this->filename);
            fwrite($out, "{");
        } elseif ($this->vardest) {
            $cp = $this->vardest;
            if ($cp === 0) {
                return;
            }
            $this->tplt_linedir($out, $this->vardestln, $this->filename);
            fwrite($out, "{");
        } else {
            throw new Exception('emit_destructor'); /* Cannot happen */
        }
        for ($i = 0; $i < strlen($cp); $i++) {
            if ($cp[$i]=='$' && $cp[$i + 1]=='$') {
                fprintf($out, "(yypminor->yy%d)", $sp->dtnum);
                $i++;
                continue;
            }
            if ($cp[$i] == "\n") {
                $linecnt++;
            }
            fwrite($out, $cp[$i]);
        }
        $lineno += 3 + $linecnt;
        fwrite($out, "}\n");
        //$this->tplt_linedir($out, $lineno, $this->outname);
    }

    /**
     * Compare to axset structures for sorting purposes
     */
    public static function axset_compare($a, $b)
    {
        return $b['nAction'] - $a['nAction'];
    }
}
