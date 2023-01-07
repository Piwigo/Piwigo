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
 * @version    CVS: $Id: PropagationLink.php,v 1.1 2006/07/18 00:53:10 cellog Exp $
 * @since      File available since Release 0.1.0
 */
/**
 * A followset propagation link indicates that the contents of one
 * configuration followset should be propagated to another whenever
 * the first changes.
 *
 * @package    PHP_ParserGenerator
 * @author     Gregory Beaver <cellog@php.net>
 * @copyright  2006 Gregory Beaver
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    0.1.5
 * @since      Class available since Release 0.1.0
 */

class PHP_ParserGenerator_PropagationLink
{
    /**
     * The configuration that defines this propagation link
     * @var PHP_ParserGenerator_Config
     */
    public $cfp;
    /**
     * The next propagation link
     * @var PHP_ParserGenerator_PropagationLink|0
     */
    public $next = 0;

    /**
     * Add a propagation link to the current list
     *
     * This prepends the configuration passed in to the first parameter
     * which is either 0 or a PHP_ParserGenerator_PropagationLink defining
     * an existing list.
     * @param PHP_ParserGenerator_PropagationLink|null
     * @param PHP_ParserGenerator_Config
     */
    public static function Plink_add(&$plpp, PHP_ParserGenerator_Config $cfp)
    {
        $new = new PHP_ParserGenerator_PropagationLink;
        $new->next = $plpp;
        $plpp = $new;
        $new->cfp = $cfp;
    }

    /**
     * Transfer every propagation link on the list "from" to the list "to"
     */
    public static function Plink_copy(PHP_ParserGenerator_PropagationLink &$to,
                               PHP_ParserGenerator_PropagationLink $from)
    {
        while ($from) {
            $nextpl = $from->next;
            $from->next = $to;
            $to = $from;
            $from = $nextpl;
        }
    }

    /**
     * Delete every propagation link on the list
     * @param PHP_ParserGenerator_PropagationLink|0
     */
    public static function Plink_delete($plp)
    {
        while ($plp) {
            $nextpl = $plp->next;
            $plp->next = 0;
            $plp = $nextpl;
        }
    }
}
