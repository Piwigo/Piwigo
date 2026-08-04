<?php

namespace Smarty\Cacheresource;

use Smarty\Exception;
use Smarty\Smarty;
use Smarty\Template;
use Smarty\Template\Cached;

/**
 * Cache Handler API
 * @author     Rodney Rehm
 */
abstract class Base
{

    /**
     * populate Cached Object with metadata from Resource
     *
     * @param Cached  $cached    cached object
     * @param Template $_template template object
     *
     * @return void
     */
    abstract public function populate(Cached $cached, Template $_template);

    /**
     * populate Cached Object with timestamp and exists from Resource
     *
     * @param Cached $cached
     *
     * @return void
     */
    abstract public function populateTimestamp(Cached $cached);

	/**
	 * Read the cached template and process header
	 *
	 * @param Template $_template template object
	 * @param Cached|null $cached cached object
	 * @param boolean $update flag if called because cache update
	 *
	 * @return boolean true or false if the cached content does not exist
	 */
    abstract public function process(
	    Template $_template,
	    ?Cached  $cached = null,
	             $update = false
    );

    /**
     * Write the rendered template output to cache
     *
     * @param Template $_template template object
     * @param string                   $content   content to cache
     *
     * @return boolean success
     */
    abstract public function storeCachedContent(Template $_template, $content);

    /**
     * Read cached template from cache
     *
     * @param Template $_template template object
     *
     * @return string  content
     */
    abstract public function retrieveCachedContent(Template $_template);

    /**
     * Empty cache
     *
     * @param Smarty  $smarty   Smarty object
     * @param integer $exp_time expiration time (number of seconds, not timestamp)
     *
     * @return integer number of cache files deleted
     */
    abstract public function clearAll(Smarty $smarty, $exp_time = null);

    /**
     * Empty cache for a specific template
     *
     * @param Smarty  $smarty        Smarty object
     * @param string  $resource_name template name
     * @param string  $cache_id      cache id
     * @param string  $compile_id    compile id
     * @param integer $exp_time      expiration time (number of seconds, not timestamp)
     *
     * @return integer number of cache files deleted
     */
    abstract public function clear(Smarty $smarty, $resource_name, $cache_id, $compile_id, $exp_time);

    /**
     * @param Smarty                 $smarty
     * @param Cached $cached
     *
     * @return bool|null
     */
    public function locked(Smarty $smarty, Cached $cached)
    {
        // theoretically locking_timeout should be checked against time_limit (max_execution_time)
        $start = microtime(true);
        $hadLock = null;
        while ($this->hasLock($smarty, $cached)) {
            $hadLock = true;
            if (microtime(true) - $start > $smarty->locking_timeout) {
                // abort waiting for lock release
                return false;
            }
            sleep(1);
        }
        return $hadLock;
    }

    /**
     * Check is cache is locked for this template
     *
     * @param Smarty                 $smarty
     * @param Cached $cached
     *
     * @return bool
     */
    public function hasLock(Smarty $smarty, Cached $cached)
    {
        // check if lock exists
        return false;
    }

    /**
     * Lock cache for this template
     *
     * @param Smarty                 $smarty
     * @param Cached $cached
     *
     * @return bool
     */
    public function acquireLock(Smarty $smarty, Cached $cached)
    {
        // create lock
        return true;
    }

    /**
     * Unlock cache for this template
     *
     * @param Smarty                 $smarty
     * @param Cached $cached
     *
     * @return bool
     */
    public function releaseLock(Smarty $smarty, Cached $cached)
    {
        // release lock
        return true;
    }
}
