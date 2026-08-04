<?php

namespace Smarty\Lexer;

/**
* Smarty Internal Plugin ConfigfileLexer
*
* This is the lexer to break the config file source into tokens 
* @package Smarty
* @subpackage Config
* @author Uwe Tews 
*/
/**
* ConfigfileLexer
*
* This is the config file lexer.
* It is generated from the ConfigfileLexer.plex file
*
* @package Smarty
* @subpackage Compiler
* @author Uwe Tews
*/
class ConfigfileLexer
{
    /**
     * Source
     *
     * @var string
     */
    public $data;
      /**
      * Source length
      *
      * @var int
      */
     public $dataLength = null;
   /**
     * byte counter
     *
     * @var int
     */
    public $counter;
    /**
     * token number
     *
     * @var int
     */
    public $token;
    /**
     * token value
     *
     * @var string
     */
    public $value;
    /**
     * current line
     *
     * @var int
     */
    public $line;
    /**
     * state number
     *
     * @var int
     */
    public $state = 1;
    /**
     * Smarty object
     *
     * @var Smarty
     */
    public $smarty = null;
    /**
     * compiler object
     *
     * @var \Smarty\Compiler\Configfile
     */
    private $compiler = null;
    /**
     * copy of config_booleanize
     *
     * @var bool
     */
    private $configBooleanize = false;
    /**
     * trace file
     *
     * @var resource
     */
    public $yyTraceFILE;
    /**
     * trace prompt
     *
     * @var string
     */
    public $yyTracePrompt;
    /**
     * state names
     *
     * @var array
     */
    public $state_name = array(1 => 'START', 2 => 'VALUE', 3 => 'NAKED_STRING_VALUE', 4 => 'COMMENT', 5 => 'SECTION', 6 => 'TRIPPLE');

    /**
     * storage for assembled token patterns
     *
     * @var string
     */
    private $yy_global_pattern1 = null;
    private $yy_global_pattern2 = null;
    private $yy_global_pattern3 = null;
    private $yy_global_pattern4 = null;
    private $yy_global_pattern5 = null;
    private $yy_global_pattern6 = null;

    /**
     * token names
     *
     * @var array
     */
    public $smarty_token_names = array(        // Text for parser error messages
    );

    /**
     * constructor
     *
     * @param   string                             $data template source
     * @param \Smarty\Compiler\Configfile $compiler
     */
    public function __construct($data, \Smarty\Compiler\Configfile $compiler)
    {
        $this->data = $data . "\n"; //now all lines are \n-terminated
        $this->dataLength = strlen($data);
        $this->counter = 0;
        if (preg_match('/^\xEF\xBB\xBF/', $this->data, $match)) {
            $this->counter += strlen($match[0]);
        }
        $this->line = 1;
        $this->compiler = $compiler;
        $this->smarty = $compiler->smarty;
        $this->configBooleanize = $this->smarty->config_booleanize;
    }

   public function replace ($input) {
        return $input;
   }

    public function PrintTrace()
    {
        $this->yyTraceFILE = fopen('php://output', 'w');
        $this->yyTracePrompt = '<br>';
    }



    private $_yy_state = 1;
    private $_yy_stack = array();

    public function yylex()
    {
        return $this->{'yylex' . $this->_yy_state}();
    }

    public function yypushstate($state)
    {
        if ($this->yyTraceFILE) {
             fprintf($this->yyTraceFILE, "%sState push %s\n", $this->yyTracePrompt, isset($this->state_name[$this->_yy_state]) ? $this->state_name[$this->_yy_state] : $this->_yy_state);
        }
        array_push($this->_yy_stack, $this->_yy_state);
        $this->_yy_state = $state;
        if ($this->yyTraceFILE) {
             fprintf($this->yyTraceFILE, "%snew State %s\n", $this->yyTracePrompt, isset($this->state_name[$this->_yy_state]) ? $this->state_name[$this->_yy_state] : $this->_yy_state);
        }
    }

    public function yypopstate()
    {
       if ($this->yyTraceFILE) {
             fprintf($this->yyTraceFILE, "%sState pop %s\n", $this->yyTracePrompt,  isset($this->state_name[$this->_yy_state]) ? $this->state_name[$this->_yy_state] : $this->_yy_state);
        }
       $this->_yy_state = array_pop($this->_yy_stack);
        if ($this->yyTraceFILE) {
             fprintf($this->yyTraceFILE, "%snew State %s\n", $this->yyTracePrompt, isset($this->state_name[$this->_yy_state]) ? $this->state_name[$this->_yy_state] : $this->_yy_state);
        }

    }

    public function yybegin($state)
    {
       $this->_yy_state = $state;
        if ($this->yyTraceFILE) {
             fprintf($this->yyTraceFILE, "%sState set %s\n", $this->yyTracePrompt, isset($this->state_name[$this->_yy_state]) ? $this->state_name[$this->_yy_state] : $this->_yy_state);
        }
    }




    public function yylex1()
    {
        if (!isset($this->yy_global_pattern1)) {
            $this->yy_global_pattern1 = $this->replace("/\G(#|;)|\G(\\[)|\G(\\])|\G(=)|\G([ \t\r]+)|\G(\n)|\G([0-9]*[a-zA-Z_]\\w*)|\G([\S\s])/isS");
        }
        if (!isset($this->dataLength)) {
            $this->dataLength = strlen($this->data);
        }
        if ($this->counter >=  $this->dataLength) {
            return false; // end of input
        }
        
        do {
            if (preg_match($this->yy_global_pattern1,$this->data, $yymatches, 0, $this->counter)) {
                if (!isset($yymatches[ 0 ][1])) {
                   $yymatches = preg_grep("/(.|\s)+/", $yymatches);
                } else {
                    $yymatches = array_filter($yymatches);
                }
                if (empty($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' .
                        ' an empty string.  Input "' . substr($this->data,
                        $this->counter, 5) . '... state START');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r1_' . $this->token}();
                if ($r === null) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->counter >=  $this->dataLength) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                }            } else {
                throw new Exception('Unexpected input at line' . $this->line .
                    ': ' . $this->data[$this->counter]);
            }
            break;
        } while (true);

    } // end function


    const START = 1;
    public function yy_r1_1()
    {

    $this->token = \Smarty\Parser\ConfigfileParser::TPC_COMMENTSTART;
    $this->yypushstate(self::COMMENT);
    }
    public function yy_r1_2()
    {

    $this->token = \Smarty\Parser\ConfigfileParser::TPC_OPENB;
    $this->yypushstate(self::SECTION);
    }
    public function yy_r1_3()
    {

    $this->token = \Smarty\Parser\ConfigfileParser::TPC_CLOSEB;
    }
    public function yy_r1_4()
    {

    $this->token = \Smarty\Parser\ConfigfileParser::TPC_EQUAL;
    $this->yypushstate(self::VALUE);
    }
    public function yy_r1_5()
    {

    return false;
    }
    public function yy_r1_6()
    {

    $this->token = \Smarty\Parser\ConfigfileParser::TPC_NEWLINE;
    }
    public function yy_r1_7()
    {

    $this->token = \Smarty\Parser\ConfigfileParser::TPC_ID;
    }
    public function yy_r1_8()
    {

    $this->token = \Smarty\Parser\ConfigfileParser::TPC_OTHER;
    }



    public function yylex2()
    {
        if (!isset($this->yy_global_pattern2)) {
            $this->yy_global_pattern2 = $this->replace("/\G([ \t\r]+)|\G(\\d+\\.\\d+(?=[ \t\r]*[\n#;]))|\G(\\d+(?=[ \t\r]*[\n#;]))|\G(\"\"\")|\G('[^'\\\\]*(?:\\\\.[^'\\\\]*)*'(?=[ \t\r]*[\n#;]))|\G(\"[^\"\\\\]*(?:\\\\.[^\"\\\\]*)*\"(?=[ \t\r]*[\n#;]))|\G([a-zA-Z]+(?=[ \t\r]*[\n#;]))|\G([^\n]+?(?=[ \t\r]*\n))|\G(\n)/isS");
        }
        if (!isset($this->dataLength)) {
            $this->dataLength = strlen($this->data);
        }
        if ($this->counter >=  $this->dataLength) {
            return false; // end of input
        }
        
        do {
            if (preg_match($this->yy_global_pattern2,$this->data, $yymatches, 0, $this->counter)) {
                if (!isset($yymatches[ 0 ][1])) {
                   $yymatches = preg_grep("/(.|\s)+/", $yymatches);
                } else {
                    $yymatches = array_filter($yymatches);
                }
                if (empty($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' .
                        ' an empty string.  Input "' . substr($this->data,
                        $this->counter, 5) . '... state VALUE');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r2_' . $this->token}();
                if ($r === null) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->counter >=  $this->dataLength) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                }            } else {
                throw new Exception('Unexpected input at line' . $this->line .
                    ': ' . $this->data[$this->counter]);
            }
            break;
        } while (true);

    } // end function


    const VALUE = 2;
    public function yy_r2_1()
    {

    return false;
    }
    public function yy_r2_2()
    {

    $this->token = \Smarty\Parser\ConfigfileParser::TPC_FLOAT;
    $this->yypopstate();
    }
    public function yy_r2_3()
    {

    $this->token = \Smarty\Parser\ConfigfileParser::TPC_INT;
    $this->yypopstate();
    }
    public function yy_r2_4()
    {

    $this->token = \Smarty\Parser\ConfigfileParser::TPC_TRIPPLE_QUOTES;
    $this->yypushstate(self::TRIPPLE);
    }
    public function yy_r2_5()
    {

    $this->token = \Smarty\Parser\ConfigfileParser::TPC_SINGLE_QUOTED_STRING;
    $this->yypopstate();
    }
    public function yy_r2_6()
    {

    $this->token = \Smarty\Parser\ConfigfileParser::TPC_DOUBLE_QUOTED_STRING;
    $this->yypopstate();
    }
    public function yy_r2_7()
    {

    if (!$this->configBooleanize || !in_array(strtolower($this->value), array('true', 'false', 'on', 'off', 'yes', 'no')) ) {
        $this->yypopstate();
        $this->yypushstate(self::NAKED_STRING_VALUE);
        return true; //reprocess in new state
    } else {
        $this->token = \Smarty\Parser\ConfigfileParser::TPC_BOOL;
        $this->yypopstate();
    }
    }
    public function yy_r2_8()
    {

    $this->token = \Smarty\Parser\ConfigfileParser::TPC_NAKED_STRING;
    $this->yypopstate();
    }
    public function yy_r2_9()
    {

    $this->token = \Smarty\Parser\ConfigfileParser::TPC_NAKED_STRING;
    $this->value = '';
    $this->yypopstate();
    }



    public function yylex3()
    {
        if (!isset($this->yy_global_pattern3)) {
            $this->yy_global_pattern3 = $this->replace("/\G([^\n]+?(?=[ \t\r]*\n))/isS");
        }
        if (!isset($this->dataLength)) {
            $this->dataLength = strlen($this->data);
        }
        if ($this->counter >=  $this->dataLength) {
            return false; // end of input
        }
        
        do {
            if (preg_match($this->yy_global_pattern3,$this->data, $yymatches, 0, $this->counter)) {
                if (!isset($yymatches[ 0 ][1])) {
                   $yymatches = preg_grep("/(.|\s)+/", $yymatches);
                } else {
                    $yymatches = array_filter($yymatches);
                }
                if (empty($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' .
                        ' an empty string.  Input "' . substr($this->data,
                        $this->counter, 5) . '... state NAKED_STRING_VALUE');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r3_' . $this->token}();
                if ($r === null) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->counter >=  $this->dataLength) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                }            } else {
                throw new Exception('Unexpected input at line' . $this->line .
                    ': ' . $this->data[$this->counter]);
            }
            break;
        } while (true);

    } // end function


    const NAKED_STRING_VALUE = 3;
    public function yy_r3_1()
    {

    $this->token = \Smarty\Parser\ConfigfileParser::TPC_NAKED_STRING;
    $this->yypopstate();
    }



    public function yylex4()
    {
        if (!isset($this->yy_global_pattern4)) {
            $this->yy_global_pattern4 = $this->replace("/\G([ \t\r]+)|\G([^\n]+?(?=[ \t\r]*\n))|\G(\n)/isS");
        }
        if (!isset($this->dataLength)) {
            $this->dataLength = strlen($this->data);
        }
        if ($this->counter >=  $this->dataLength) {
            return false; // end of input
        }
        
        do {
            if (preg_match($this->yy_global_pattern4,$this->data, $yymatches, 0, $this->counter)) {
                if (!isset($yymatches[ 0 ][1])) {
                   $yymatches = preg_grep("/(.|\s)+/", $yymatches);
                } else {
                    $yymatches = array_filter($yymatches);
                }
                if (empty($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' .
                        ' an empty string.  Input "' . substr($this->data,
                        $this->counter, 5) . '... state COMMENT');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r4_' . $this->token}();
                if ($r === null) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->counter >=  $this->dataLength) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                }            } else {
                throw new Exception('Unexpected input at line' . $this->line .
                    ': ' . $this->data[$this->counter]);
            }
            break;
        } while (true);

    } // end function


    const COMMENT = 4;
    public function yy_r4_1()
    {

    return false;
    }
    public function yy_r4_2()
    {

    $this->token = \Smarty\Parser\ConfigfileParser::TPC_NAKED_STRING;
    }
    public function yy_r4_3()
    {

    $this->token = \Smarty\Parser\ConfigfileParser::TPC_NEWLINE;
    $this->yypopstate();
    }



    public function yylex5()
    {
        if (!isset($this->yy_global_pattern5)) {
            $this->yy_global_pattern5 = $this->replace("/\G(\\.)|\G(.*?(?=[\.=[\]\r\n]))/isS");
        }
        if (!isset($this->dataLength)) {
            $this->dataLength = strlen($this->data);
        }
        if ($this->counter >=  $this->dataLength) {
            return false; // end of input
        }
        
        do {
            if (preg_match($this->yy_global_pattern5,$this->data, $yymatches, 0, $this->counter)) {
                if (!isset($yymatches[ 0 ][1])) {
                   $yymatches = preg_grep("/(.|\s)+/", $yymatches);
                } else {
                    $yymatches = array_filter($yymatches);
                }
                if (empty($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' .
                        ' an empty string.  Input "' . substr($this->data,
                        $this->counter, 5) . '... state SECTION');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r5_' . $this->token}();
                if ($r === null) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->counter >=  $this->dataLength) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                }            } else {
                throw new Exception('Unexpected input at line' . $this->line .
                    ': ' . $this->data[$this->counter]);
            }
            break;
        } while (true);

    } // end function


    const SECTION = 5;
    public function yy_r5_1()
    {

    $this->token = \Smarty\Parser\ConfigfileParser::TPC_DOT;
    }
    public function yy_r5_2()
    {

    $this->token = \Smarty\Parser\ConfigfileParser::TPC_SECTION;
    $this->yypopstate();
    }


    public function yylex6()
    {
        if (!isset($this->yy_global_pattern6)) {
            $this->yy_global_pattern6 = $this->replace("/\G(\"\"\"(?=[ \t\r]*[\n#;]))|\G([\S\s])/isS");
        }
        if (!isset($this->dataLength)) {
            $this->dataLength = strlen($this->data);
        }
        if ($this->counter >=  $this->dataLength) {
            return false; // end of input
        }
        
        do {
            if (preg_match($this->yy_global_pattern6,$this->data, $yymatches, 0, $this->counter)) {
                if (!isset($yymatches[ 0 ][1])) {
                   $yymatches = preg_grep("/(.|\s)+/", $yymatches);
                } else {
                    $yymatches = array_filter($yymatches);
                }
                if (empty($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' .
                        ' an empty string.  Input "' . substr($this->data,
                        $this->counter, 5) . '... state TRIPPLE');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r6_' . $this->token}();
                if ($r === null) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->counter += strlen($this->value);
                    $this->line += substr_count($this->value, "\n");
                    if ($this->counter >=  $this->dataLength) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                }            } else {
                throw new Exception('Unexpected input at line' . $this->line .
                    ': ' . $this->data[$this->counter]);
            }
            break;
        } while (true);

    } // end function


    const TRIPPLE = 6;
    public function yy_r6_1()
    {

    $this->token = \Smarty\Parser\ConfigfileParser::TPC_TRIPPLE_QUOTES_END;
    $this->yypopstate();
    $this->yypushstate(self::START);
    }
    public function yy_r6_2()
    {

  $to = strlen($this->data);
  preg_match("/\"\"\"[ \t\r]*[\n#;]/",$this->data,$match,PREG_OFFSET_CAPTURE,$this->counter);
  if (isset($match[0][1])) {
    $to = $match[0][1];
  } else {
    $this->compiler->trigger_config_file_error ('missing or misspelled literal closing tag');
  }  
  $this->value = substr($this->data,$this->counter,$to-$this->counter);
  $this->token = \Smarty\Parser\ConfigfileParser::TPC_TRIPPLE_TEXT;
    }


}
