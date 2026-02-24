<?php

namespace Smarty;

/**
 * Smarty Internal Plugin Debug
 * Class to collect data for the Smarty Debugging Console
 *
 * @author     Uwe Tews
 */
class Debug extends Data
{
    /**
     * template data
     *
     * @var array
     */
    public $template_data = [];

    /**
     * List of uid's which shall be ignored
     *
     * @var array
     */
    public $ignore_uid = [];

    /**
     * Index of display() and fetch() calls
     *
     * @var int
     */
    public $index = 0;

    /**
     * Counter for window offset
     *
     * @var int
     */
    public $offset = 0;

    /**
     * Start logging template
     *
     * @param Template $template template
     * @param null                      $mode     true: display   false: fetch  null: subtemplate
     */
    public function start_template(Template $template, $mode = null)
    {
        if (isset($mode) && !$template->_isSubTpl()) {
            $this->index++;
            $this->offset++;
            $this->template_data[ $this->index ] = null;
        }
        $key = $this->get_key($template);
        $this->template_data[ $this->index ][ $key ][ 'start_template_time' ] = microtime(true);
    }

    /**
     * End logging of cache time
     *
     * @param Template $template cached template
     */
    public function end_template(Template $template)
    {
        $key = $this->get_key($template);
        $this->template_data[ $this->index ][ $key ][ 'total_time' ] +=
            microtime(true) - $this->template_data[ $this->index ][ $key ][ 'start_template_time' ];
    }

    /**
     * Start logging of compile time
     *
     * @param Template $template
     */
    public function start_compile(Template $template)
    {
        static $_is_stringy = array('string' => true, 'eval' => true);
        if (!empty($template->getCompiler()->trace_uid)) {
            $key = $template->getCompiler()->trace_uid;
            if (!isset($this->template_data[ $this->index ][ $key ])) {
                $this->saveTemplateData($_is_stringy, $template, $key);
            }
        } else {
            if (isset($this->ignore_uid[ $template->getSource()->uid ])) {
                return;
            }
            $key = $this->get_key($template);
        }
        $this->template_data[ $this->index ][ $key ][ 'start_time' ] = microtime(true);
    }

    /**
     * End logging of compile time
     *
     * @param Template $template
     */
    public function end_compile(Template $template)
    {
        if (!empty($template->getCompiler()->trace_uid)) {
            $key = $template->getCompiler()->trace_uid;
        } else {
            if (isset($this->ignore_uid[ $template->getSource()->uid ])) {
                return;
            }
            $key = $this->get_key($template);
        }
        $this->template_data[ $this->index ][ $key ][ 'compile_time' ] +=
            microtime(true) - $this->template_data[ $this->index ][ $key ][ 'start_time' ];
    }

    /**
     * Start logging of render time
     *
     * @param Template $template
     */
    public function start_render(Template $template)
    {
        $key = $this->get_key($template);
        $this->template_data[ $this->index ][ $key ][ 'start_time' ] = microtime(true);
    }

    /**
     * End logging of compile time
     *
     * @param Template $template
     */
    public function end_render(Template $template)
    {
        $key = $this->get_key($template);
        $this->template_data[ $this->index ][ $key ][ 'render_time' ] +=
            microtime(true) - $this->template_data[ $this->index ][ $key ][ 'start_time' ];
    }

    /**
     * Start logging of cache time
     *
     * @param Template $template cached template
     */
    public function start_cache(Template $template)
    {
        $key = $this->get_key($template);
        $this->template_data[ $this->index ][ $key ][ 'start_time' ] = microtime(true);
    }

    /**
     * End logging of cache time
     *
     * @param Template $template cached template
     */
    public function end_cache(Template $template)
    {
        $key = $this->get_key($template);
        $this->template_data[ $this->index ][ $key ][ 'cache_time' ] +=
            microtime(true) - $this->template_data[ $this->index ][ $key ][ 'start_time' ];
    }

    /**
     * Register template object
     *
     * @param Template $template cached template
     */
    public function register_template(Template $template)
    {
    }

    /**
     * Register data object
     *
     * @param Data $data data object
     */
    public static function register_data(Data $data)
    {
    }

    /**
     * Opens a window for the Smarty Debugging Console and display the data
     *
     * @param Template|Smarty $obj object to debug
     * @param bool                            $full
     *
     * @throws \Exception
     * @throws Exception
     */
    public function display_debug($obj, bool $full = false)
    {
        if (!$full) {
            $this->offset++;
            $savedIndex = $this->index;
            $this->index = 9999;
        }
        $smarty = $obj->getSmarty();
        // create fresh instance of smarty for displaying the debug console
        // to avoid problems if the application did overload the Smarty class
        $debObj = new Smarty();
        // copy the working dirs from application
        $debObj->setCompileDir($smarty->getCompileDir());
        $debObj->compile_check = Smarty::COMPILECHECK_ON;
        $debObj->security_policy = null;
        $debObj->debugging = false;
        $debObj->debugging_ctrl = 'NONE';
        $debObj->error_reporting = E_ALL & ~E_NOTICE;
        $debObj->debug_tpl = $smarty->debug_tpl ?? 'file:' . __DIR__ . '/debug.tpl';
        $debObj->registered_resources = array();
        $debObj->escape_html = true;
        $debObj->caching = Smarty::CACHING_OFF;
        // prepare information of assigned variables
        $ptr = $this->get_debug_vars($obj);
        $_assigned_vars = $ptr->tpl_vars;
        ksort($_assigned_vars);
        $_config_vars = $ptr->config_vars;
        ksort($_config_vars);
        $debugging = $smarty->debugging;
        $templateName = $obj->getSource()->type . ':' . $obj->getSource()->name;
        $displayMode = $debugging === 2 || !$full;
        $offset = $this->offset * 50;
        $_template = $debObj->doCreateTemplate($debObj->debug_tpl);
        if ($obj instanceof Template) {
            $_template->assign('template_name', $templateName);
        } elseif ($obj instanceof Smarty || $full) {
            $_template->assign('template_data', $this->template_data[$this->index]);
        } else {
            $_template->assign('template_data', null);
        }
        $_template->assign('assigned_vars', $_assigned_vars);
        $_template->assign('config_vars', $_config_vars);
        $_template->assign('execution_time', microtime(true) - $smarty->start_time);
        $_template->assign('targetWindow', $displayMode ? md5("$offset$templateName") : '__Smarty__');
        $_template->assign('offset', $offset);
        echo $_template->fetch();
        if (isset($full)) {
            $this->index--;
        }
        if (!$full) {
            $this->index = $savedIndex;
        }
    }

    /**
     * Recursively gets variables from all template/data scopes
     *
     * @param \Smarty\Data $obj object to debug
     *
     * @return \StdClass
     */
    private function get_debug_vars($obj)
    {
        $config_vars = array();
        foreach ($obj->config_vars as $key => $var) {
            $config_vars[$key]['value'] = $var;
	        $config_vars[$key]['scope'] = get_class($obj) . ':' . spl_object_id($obj);
        }
        $tpl_vars = array();
        foreach ($obj->tpl_vars as $key => $var) {
            foreach ($var as $varkey => $varvalue) {
                if ($varkey === 'value') {
                    $tpl_vars[ $key ][ $varkey ] = $varvalue;
                } else {
                    if ($varkey === 'nocache') {
                        if ($varvalue === true) {
                            $tpl_vars[ $key ][ $varkey ] = $varvalue;
                        }
                    } else {
                        if ($varkey !== 'scope' || $varvalue !== 0) {
                            $tpl_vars[ $key ][ 'attributes' ][ $varkey ] = $varvalue;
                        }
                    }
                }
            }
	        $tpl_vars[$key]['scope'] = get_class($obj) . ':' . spl_object_id($obj);
        }
        if (isset($obj->parent)) {
            $parent = $this->get_debug_vars($obj->parent);
            foreach ($parent->tpl_vars as $name => $pvar) {
                if (isset($tpl_vars[ $name ]) && $tpl_vars[ $name ][ 'value' ] === $pvar[ 'value' ]) {
                    $tpl_vars[ $name ][ 'scope' ] = $pvar[ 'scope' ];
                }
            }
            $tpl_vars = array_merge($parent->tpl_vars, $tpl_vars);
            foreach ($parent->config_vars as $name => $pvar) {
                if (isset($config_vars[ $name ]) && $config_vars[ $name ][ 'value' ] === $pvar[ 'value' ]) {
                    $config_vars[ $name ][ 'scope' ] = $pvar[ 'scope' ];
                }
            }
            $config_vars = array_merge($parent->config_vars, $config_vars);
        }
        return (object)array('tpl_vars' => $tpl_vars, 'config_vars' => $config_vars);
    }

    /**
     * Return key into $template_data for template
     *
     * @param Template $template template object
     *
     * @return string key into $template_data
     */
    private function get_key(Template $template)
    {
        static $_is_stringy = array('string' => true, 'eval' => true);

        $key = $template->getSource()->uid;
        if (isset($this->template_data[ $this->index ][ $key ])) {
            return $key;
        } else {
	        $this->saveTemplateData($_is_stringy, $template, $key);
	        $this->template_data[ $this->index ][ $key ][ 'total_time' ] = 0;
            return $key;
        }
    }

    /**
     * Ignore template
     *
     * @param Template $template
     */
    public function ignore(Template $template)
    {
        $this->ignore_uid[$template->getSource()->uid] = true;
    }

    /**
     * handle 'URL' debugging mode
     *
     * @param Smarty $smarty
     */
    public function debugUrl(Smarty $smarty)
    {
        if (isset($_SERVER[ 'QUERY_STRING' ])) {
            $_query_string = $_SERVER[ 'QUERY_STRING' ];
        } else {
            $_query_string = '';
        }
        if (false !== strpos($_query_string, $smarty->smarty_debug_id)) {
            if (false !== strpos($_query_string, $smarty->smarty_debug_id . '=on')) {
                // enable debugging for this browser session
                setcookie('SMARTY_DEBUG', true);
                $smarty->debugging = true;
            } elseif (false !== strpos($_query_string, $smarty->smarty_debug_id . '=off')) {
                // disable debugging for this browser session
                setcookie('SMARTY_DEBUG', false);
                $smarty->debugging = false;
            } else {
                // enable debugging for this page
                $smarty->debugging = true;
            }
        } else {
            if (isset($_COOKIE[ 'SMARTY_DEBUG' ])) {
                $smarty->debugging = true;
            }
        }
    }

	/**
	 * @param array $_is_stringy
	 * @param Template $template
	 * @param string $key
	 *
	 * @return void
	 */
	private function saveTemplateData(array $_is_stringy, Template $template, string $key): void {
		if (isset($_is_stringy[$template->getSource()->type])) {
			$this->template_data[$this->index][$key]['name'] =
				'\'' . substr($template->getSource()->name, 0, 25) . '...\'';
		} else {
			$this->template_data[$this->index][$key]['name'] = $template->getSource()->getResourceName();
		}
		$this->template_data[$this->index][$key]['compile_time'] = 0;
		$this->template_data[$this->index][$key]['render_time'] = 0;
		$this->template_data[$this->index][$key]['cache_time'] = 0;
	}
}
