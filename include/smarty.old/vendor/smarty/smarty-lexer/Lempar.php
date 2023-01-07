<?php

class ParseyyStackEntry
{
    public $stateno;       /* The state-number */
    public $major;         /* The major token value.  This is the code
                     ** number for the token at this stack level */
    public $minor; /* The user-supplied minor token value.  This
                     ** is the value of the token  */
};

%%

%%
{
%%

%%

%%
%%
    public static $yyFallback = array(
%%
    );
    public function Trace($TraceFILE, $zTracePrompt)
    {
        if (!$TraceFILE) {
            $zTracePrompt = 0;
        } elseif (!$zTracePrompt) {
            $TraceFILE = 0;
        }
        $this->yyTraceFILE = $TraceFILE;
        $this->yyTracePrompt = $zTracePrompt;
    }

    public function PrintTrace()
    {
        $this->yyTraceFILE = fopen('php://output', 'w');
        $this->yyTracePrompt = '<br>';
    }

    public $yyTraceFILE;
    public $yyTracePrompt;
    public $yyidx;                    /* Index of top element in stack */
    public $yyerrcnt;                 /* Shifts left before out of the error */
    public $yystack = array();  /* The parser's stack */

    public $yyTokenName = array(
%%
    );

    public static $yyRuleName = array(
%%
    );

    public function tokenName($tokenType)
    {
        if ($tokenType === 0) {
            return 'End of Input';
        }
        if ($tokenType > 0 && $tokenType < count($this->yyTokenName)) {
            return $this->yyTokenName[$tokenType];
        } else {
            return 'Unknown';
        }
    }

    public static function yy_destructor($yymajor, $yypminor)
    {
        switch ($yymajor) {
%%
            default:  break;   /* If no destructor action specified: do nothing */
        }
    }

    public function yy_pop_parser_stack()
    {
        if (empty($this->yystack)) {
            return;
        }
        $yytos = array_pop($this->yystack);
        if ($this->yyTraceFILE && $this->yyidx >= 0) {
            fwrite($this->yyTraceFILE,
                $this->yyTracePrompt . 'Popping ' . $this->yyTokenName[$yytos->major] .
                    "\n");
        }
        $yymajor = $yytos->major;
        self::yy_destructor($yymajor, $yytos->minor);
        $this->yyidx--;

        return $yymajor;
    }

    public function __destruct()
    {
        while ($this->yystack !== Array()) {
            $this->yy_pop_parser_stack();
        }
        if (is_resource($this->yyTraceFILE)) {
            fclose($this->yyTraceFILE);
        }
    }

    public function yy_get_expected_tokens($token)
    {
        static $res3 = array();
        static $res4 = array();
        $state = $this->yystack[$this->yyidx]->stateno;
        $expected = self::$yyExpectedTokens[$state];
        if (isset($res3[$state][$token])) {
            if ($res3[$state][$token]) {
                return $expected;
            }
        } else {
            if ($res3[$state][$token] = in_array($token, self::$yyExpectedTokens[$state], true)) {
                return $expected;
            }
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done++ === 100) {
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
                $expected = array_merge($expected, self::$yyExpectedTokens[$nextstate]);
                        if (isset($res4[$nextstate][$token])) {
                            if ($res4[$nextstate][$token]) {
                                $this->yyidx = $yyidx;
                                $this->yystack = $stack;
                                return array_unique($expected);
                            }
                        } else {
                            if ($res4[$nextstate][$token] = in_array($token, self::$yyExpectedTokens[$nextstate], true)) {
                                $this->yyidx = $yyidx;
                                $this->yystack = $stack;
                                return array_unique($expected);
                            }
                        }
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx++;
                        $x = new ParseyyStackEntry;
                        $x->stateno = $nextstate;
                        $x->major = self::$yyRuleInfo[$yyruleno]['lhs'];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate === self::YYNSTATE + self::YYNRULE + 1) {
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
    $this->yyidx = $yyidx;
    $this->yystack = $stack;

        return array_unique($expected);
    }

    public function yy_is_expected_token($token)
    {
        static $res = array();
        static $res2 = array();
        if ($token === 0) {
            return true; // 0 is not part of this
        }
        $state = $this->yystack[$this->yyidx]->stateno;
        if (isset($res[$state][$token])) {
            if ($res[$state][$token]) {
                return true;
            }
        } else {
            if ($res[$state][$token] = in_array($token, self::$yyExpectedTokens[$state], true)) {
                return true;
            }
       }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done++ === 100) {
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
                    if (isset($res2[$nextstate][$token])) {
                        if ($res2[$nextstate][$token]) {
                            $this->yyidx = $yyidx;
                            $this->yystack = $stack;
                            return true;
                        }
                    } else {
                        if ($res2[$nextstate][$token] = (isset(self::$yyExpectedTokens[$nextstate]) && in_array($token, self::$yyExpectedTokens[$nextstate], true))) {
                            $this->yyidx = $yyidx;
                            $this->yystack = $stack;
                            return true;
                        }
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx++;
                        $x = new ParseyyStackEntry;
                        $x->stateno = $nextstate;
                        $x->major = self::$yyRuleInfo[$yyruleno]['lhs'];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate === self::YYNSTATE + self::YYNRULE + 1) {
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
        if ($iLookAhead === self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB ||
              self::$yy_lookahead[$i] != $iLookAhead) {
            if (count(self::$yyFallback) && $iLookAhead < count(self::$yyFallback)
                   && ($iFallback = self::$yyFallback[$iLookAhead]) != 0) {
                if ($this->yyTraceFILE) {
                    fwrite($this->yyTraceFILE, $this->yyTracePrompt . 'FALLBACK ' .
                        $this->yyTokenName[$iLookAhead] . ' => ' .
                        $this->yyTokenName[$iFallback] . "\n");
                }

                return $this->yy_find_shift_action($iFallback);
            }

            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    public function yy_find_reduce_action($stateno, $iLookAhead)
    {
        /* $stateno = $this->yystack[$this->yyidx]->stateno; */

        if (!isset(self::$yy_reduce_ofst[$stateno])) {
            return self::$yy_default[$stateno];
        }
        $i = self::$yy_reduce_ofst[$stateno];
        if ($i === self::YY_REDUCE_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead === self::YYNOCODE) {
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

    public function yy_shift($yyNewState, $yyMajor, $yypMinor)
    {
        $this->yyidx++;
        if ($this->yyidx >= self::YYSTACKDEPTH) {
            $this->yyidx--;
            if ($this->yyTraceFILE) {
                fprintf($this->yyTraceFILE, "%sStack Overflow!\n", $this->yyTracePrompt);
            }
            while ($this->yyidx >= 0) {
                $this->yy_pop_parser_stack();
            }
%%

            return;
        }
        $yytos = new ParseyyStackEntry;
        $yytos->stateno = $yyNewState;
        $yytos->major = $yyMajor;
        $yytos->minor = $yypMinor;
        $this->yystack[] = $yytos;
        if ($this->yyTraceFILE && $this->yyidx > 0) {
            fprintf($this->yyTraceFILE, "%sShift %d\n", $this->yyTracePrompt,
                $yyNewState);
            fprintf($this->yyTraceFILE, "%sStack:", $this->yyTracePrompt);
            for ($i = 1; $i <= $this->yyidx; $i++) {
                fprintf($this->yyTraceFILE, " %s",
                    $this->yyTokenName[$this->yystack[$i]->major]);
            }
            fwrite($this->yyTraceFILE,"\n");
        }
    }

    public static $yyRuleInfo = array(
%%
    );

    public static $yyReduceMap = array(
%%
    );
%%

    private $_retvalue;

    public function yy_reduce($yyruleno)
    {
        if ($this->yyTraceFILE && $yyruleno >= 0
              && $yyruleno < count(self::$yyRuleName)) {
            fprintf($this->yyTraceFILE, "%sReduce (%d) [%s].\n",
                $this->yyTracePrompt, $yyruleno,
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
            if (!$this->yyTraceFILE && $yysize) {
                $this->yyidx++;
                $x = new ParseyyStackEntry;
                $x->stateno = $yyact;
                $x->major = $yygoto;
                $x->minor = $yy_lefthand_side;
                $this->yystack[$this->yyidx] = $x;
            } else {
                $this->yy_shift($yyact, $yygoto, $yy_lefthand_side);
            }
        } elseif ($yyact === self::YYNSTATE + self::YYNRULE + 1) {
            $this->yy_accept();
        }
    }

    public function yy_parse_failed()
    {
        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%sFail!\n", $this->yyTracePrompt);
        } while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
%%
    }

    public function yy_syntax_error($yymajor, $TOKEN)
    {
%%
    }

    public function yy_accept()
    {
        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%sAccept!\n", $this->yyTracePrompt);
        } while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
%%
    }

    public function doParse($yymajor, $yytokenvalue)
    {
        $yyerrorhit = 0;   /* True if yymajor has invoked an error */

        if ($this->yyidx === null || $this->yyidx < 0) {
            $this->yyidx = 0;
            $this->yyerrcnt = -1;
            $x = new ParseyyStackEntry;
            $x->stateno = 0;
            $x->major = 0;
            $this->yystack = array();
            $this->yystack[] = $x;
        }
        $yyendofinput = ($yymajor==0);

        if ($this->yyTraceFILE) {
            fprintf($this->yyTraceFILE, "%sInput %s\n",
                $this->yyTracePrompt, $this->yyTokenName[$yymajor]);
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
            } elseif ($yyact === self::YY_ERROR_ACTION) {
                if ($this->yyTraceFILE) {
                    fprintf($this->yyTraceFILE, "%sSyntax Error!\n",
                        $this->yyTracePrompt);
                }
                if (self::YYERRORSYMBOL) {
                    if ($this->yyerrcnt < 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $yymx = $this->yystack[$this->yyidx]->major;
                    if ($yymx === self::YYERRORSYMBOL || $yyerrorhit) {
                        if ($this->yyTraceFILE) {
                            fprintf($this->yyTraceFILE, "%sDiscard input token %s\n",
                                $this->yyTracePrompt, $this->yyTokenName[$yymajor]);
                        }
                        $this->yy_destructor($yymajor, $yytokenvalue);
                        $yymajor = self::YYNOCODE;
                    } else {
                        while ($this->yyidx >= 0 &&
                                 $yymx !== self::YYERRORSYMBOL &&
        ($yyact = $this->yy_find_shift_action(self::YYERRORSYMBOL)) >= self::YYNSTATE
                              ){
                            $this->yy_pop_parser_stack();
                        }
                        if ($this->yyidx < 0 || $yymajor==0) {
                            $this->yy_destructor($yymajor, $yytokenvalue);
                            $this->yy_parse_failed();
                            $yymajor = self::YYNOCODE;
                        } elseif ($yymx !== self::YYERRORSYMBOL) {
                            $u2 = 0;
                            $this->yy_shift($yyact, self::YYERRORSYMBOL, $u2);
                        }
                    }
                    $this->yyerrcnt = 3;
                    $yyerrorhit = 1;
                } else {
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
        } while ($yymajor !== self::YYNOCODE && $this->yyidx >= 0);
    }
}
