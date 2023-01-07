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
 * @version    CVS: $Id: ActionTable.php,v 1.1 2006/07/18 00:53:10 cellog Exp $
 * @since      File available since Release 0.1.0
 */
/**
 * The state of the yy_action table under construction is an instance of
 * the following structure
 * @package    PHP_ParserGenerator
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2006 Gregory Beaver
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    0.1.5
 * @since      Class available since Release 0.1.0
 */
class PHP_ParserGenerator_ActionTable
{
    /**
     * Number of used slots in {@link $aAction}
     * @var int
     */
    public $nAction = 0;
    /**
     * The $yy_action table under construction.
     *
     * Each entry is of format:
     * <code>
     *  array(
     *      'lookahead' => -1, // Value of the lookahead token (symbol index)
     *      'action' => -1     // Action to take on the given lookahead (action index)
     *  );
     * </code>
     * @see PHP_ParserGenerator_Data::compute_action()
     * @var array
     */
    public $aAction =
        array(array(
            'lookahead' => -1,
            'action' => -1
        ));
    /**
     * A single new transaction set.
     *
     * @see $aAction format of the internal array is described here
     * @var array
     */
    public $aLookahead =
        array(array(
            'lookahead' => 0,
            'action' => 0
        ));
    /**
     * The smallest (minimum) value of any lookahead token in {@link $aLookahead}
     *
     * The lowest non-terminal is always introduced earlier in the parser file,
     * and is therefore a more significant token.
     * @var int
     */
    public $mnLookahead = 0;
    /**
     * The action associated with the smallest lookahead token.
     * @see $mnLookahead
     * @var int
     */
    public $mnAction = 0;
    /**
     * The largest (maximum) value of any lookahead token in {@link $aLookahead}
     * @var int
     */
    public $mxLookahead = 0;
    /**
     * The number of slots used in {@link $aLookahead}.
     *
     * This is the same as count($aLookahead), but there was no pressing reason
     * to change this when porting from C.
     * @see $mnLookahead
     * @var int
     */
    public $nLookahead = 0;

    /**
     * Add a new action to the current transaction set
     * @param int
     * @param int
     */
    public function acttab_action($lookahead, $action)
    {
        if ($this->nLookahead === 0) {
            $this->aLookahead = array();
            $this->mxLookahead = $lookahead;
            $this->mnLookahead = $lookahead;
            $this->mnAction = $action;
        } else {
            if ($this->mxLookahead < $lookahead) {
                $this->mxLookahead = $lookahead;
            }
            if ($this->mnLookahead > $lookahead) {
                $this->mnLookahead = $lookahead;
                $this->mnAction = $action;
            }
        }
        $this->aLookahead[$this->nLookahead] = array(
            'lookahead' => $lookahead,
            'action' => $action);
        $this->nLookahead++;
    }

    /**
     * Add the transaction set built up with prior calls to acttab_action()
     * into the current action table.  Then reset the transaction set back
     * to an empty set in preparation for a new round of acttab_action() calls.
     *
     * Return the offset into the action table of the new transaction.
     * @return int Return the offset that should be added to the lookahead in
     * order to get the index into $yy_action of the action.  This will be used
     * in generation of $yy_ofst tables (reduce and shift)
     * @throws Exception
     */
    public function acttab_insert()
    {
        if ($this->nLookahead <= 0) {
            throw new Exception('nLookahead is not set up?');
        }

        /* Scan the existing action table looking for an offset where we can
        ** insert the current transaction set.  Fall out of the loop when that
        ** offset is found.  In the worst case, we fall out of the loop when
        ** i reaches $this->nAction, which means we append the new transaction set.
        **
        ** i is the index in $this->aAction[] where $this->mnLookahead is inserted.
        */
        for ($i = 0; $i < $this->nAction + $this->mnLookahead; $i++) {
            if (!isset($this->aAction[$i])) {
                $this->aAction[$i] = array(
                    'lookahead' => -1,
                    'action' => -1,
                );
            }
            if ($this->aAction[$i]['lookahead'] < 0) {
                for ($j = 0; $j < $this->nLookahead; $j++) {
                    if (!isset($this->aLookahead[$j])) {
                        $this->aLookahead[$j] = array(
                            'lookahead' => 0,
                            'action' => 0,
                        );
                    }
                    $k = $this->aLookahead[$j]['lookahead'] -
                        $this->mnLookahead + $i;
                    if ($k < 0) {
                        break;
                    }
                    if (!isset($this->aAction[$k])) {
                        $this->aAction[$k] = array(
                            'lookahead' => -1,
                            'action' => -1,
                        );
                    }
                    if ($this->aAction[$k]['lookahead'] >= 0) {
                        break;
                    }
                }
                if ($j < $this->nLookahead) {
                    continue;
                }
                for ($j = 0; $j < $this->nAction; $j++) {
                    if (!isset($this->aAction[$j])) {
                        $this->aAction[$j] = array(
                            'lookahead' => -1,
                            'action' => -1,
                        );
                    }
                    if ($this->aAction[$j]['lookahead'] == $j +
                          $this->mnLookahead - $i) {
                        break;
                    }
                }
                if ($j == $this->nAction) {
                    break;  /* Fits in empty slots */
                }
            } elseif ($this->aAction[$i]['lookahead'] == $this->mnLookahead) {
                if ($this->aAction[$i]['action'] != $this->mnAction) {
                    continue;
                }
                for ($j = 0; $j < $this->nLookahead; $j++) {
                    $k = $this->aLookahead[$j]['lookahead'] -
                        $this->mnLookahead + $i;
                    if ($k < 0 || $k >= $this->nAction) {
                        break;
                    }
                    if (!isset($this->aAction[$k])) {
                        $this->aAction[$k] = array(
                            'lookahead' => -1,
                            'action' => -1,
                        );
                    }
                    if ($this->aLookahead[$j]['lookahead'] !=
                          $this->aAction[$k]['lookahead']) {
                        break;
                    }
                    if ($this->aLookahead[$j]['action'] !=
                          $this->aAction[$k]['action']) {
                        break;
                    }
                }
                if ($j < $this->nLookahead) {
                    continue;
                }
                $n = 0;
                for ($j = 0; $j < $this->nAction; $j++) {
                    if (!isset($this->aAction[$j])) {
                        $this->aAction[$j] = array(
                            'lookahead' => -1,
                            'action' => -1,
                        );
                    }
                    if ($this->aAction[$j]['lookahead'] < 0) {
                        continue;
                    }
                    if ($this->aAction[$j]['lookahead'] == $j +
                          $this->mnLookahead - $i) {
                        $n++;
                    }
                }
                if ($n == $this->nLookahead) {
                    break;  /* Same as a prior transaction set */
                }
            }
        }
        /* Insert transaction set at index i. */
        for ($j = 0; $j < $this->nLookahead; $j++) {
            if (!isset($this->aLookahead[$j])) {
                $this->aLookahead[$j] = array(
                    'lookahead' => 0,
                    'action' => 0,
                );
            }
            $k = $this->aLookahead[$j]['lookahead'] - $this->mnLookahead + $i;
            $this->aAction[$k] = $this->aLookahead[$j];
            if ($k >= $this->nAction) {
                $this->nAction = $k + 1;
            }
        }
        $this->nLookahead = 0;
        $this->aLookahead = array();

        /* Return the offset that is added to the lookahead in order to get the
        ** index into yy_action of the action */

        return $i - $this->mnLookahead;
    }
}
