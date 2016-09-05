<?php
namespace FES\Core;
use FES\Lib\Cache\MemCache;
/**
 * @desc CacheSession as a session handler by using cache as storage medium.
 * @author linyu@273.cn
 * @since 2015年11月23日
 */
class CacheSession implements \SessionHandlerInterface {

    // Memcached instance
    public $cache = NULL;
    
    // Key prefix
    public $prefix = 'session:';
    private $_config = [];
    
    /**
     * Class constructor
     *
     * @param	array	$params	Configuration parameters
     * @return	void
     */
    public function __construct($params)
    {
        $this->_config = $params;
    }
    
    /**
     * Open
     *
     * Sanitizes save_path and initializes connections.
     *
     * @param	string	$savePath	Server path(s)
     * @param	string	$name		Session cookie name, unused
     * @return	bool
     */
    public function open($savePath, $name)
    {
        $this->cache = new MemCache(C('cache.memcached'));
        return TRUE;
    }
    
    /**
     * Read
     *
     * Reads session data and acquires a lock
     *
     * @param	string	$sessionId	Session ID
     * @return	string	Serialized session data
     */
    public function read($sessionId)
    {
        if (!empty($this->cache))
        {
            return $this->cache->get($sessionId);
        }
        return FALSE;
    }
    
    /**
     * Write
     *
     * Writes (create / update) session data
     *
     * @param	string	$sessionId	Session ID
     * @param	string	$sessionData	Serialized session data
     * @return	bool
     */
    public function write($sessionId, $sessionData)
    {
        if (!empty($this->cache))
        {
            $expiration = empty($this->_config['sess_expiration']) ? ini_get('session.gc_maxlifetime') : $this->_config['sess_expiration'];
            return $this->cache->set($sessionId, $sessionData, $expiration);
        }
        return FALSE;
    }
    
    /**
     * Close
     *
     * Releases locks and closes connection.
     *
     * @return	bool
     */
    public function close()
    {
        if (!empty($this->cache))
        {
            $this->cache = NULL;
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * Destroy
     *
     * Destroys the current session.
     *
     * @param	string	$sessionId	Session ID
     * @return	bool
     */
    public function destroy($sessionId)
    {
        if (!empty($this->cache))
        {
            $this->_memcached->delete($sessionId);
            return $this->_cookieDestroy();
        }
        return FALSE;
    }
    
    /**
     * Garbage Collector
     *
     * Deletes expired sessions
     *
     * @param	int 	$maxlifetime	Maximum lifetime of sessions
     * @return	bool
     */
    public function gc($maxlifetime)
    {
        // Not necessary, Memcached takes care of that.
        return TRUE;
    }
    
    // 
    protected function _cookieDestroy()
    {
        return setcookie(
                $this->_config['cookie_name'],
                NULL,
                1,
                $this->_config['cookie_path'],
                $this->_config['cookie_domain'],
                $this->_config['cookie_secure'],
                TRUE
        );
    }
}

