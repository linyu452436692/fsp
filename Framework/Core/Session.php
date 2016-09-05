<?php
namespace FES\Core;
use FES\Lib\Exception\BadClassCallException;
class Session {
    private $_config = [];
    /**
     * Class constructor
     * @param array $config
     * @return void
     */
    public function __construct(array $config = []) {
        $this->_config = $config;
        $this->_setConfig();
        $this->_setHandler();
        session_start();
    }
    
    private function _setHandler() {
        if (!empty($this->_config['handler'])) {
            $handler = $this->_config['handler'];
            if (!class_exists($this->_config['handler'])) throw new BadClassCallException('class ' . $handler . ' not exists!');
            $class = new $handler($this->_config);
            if ($class instanceof \SessionHandlerInterface) {
                session_set_save_handler($class, TRUE);
            } else {
                throw new BadClassCallException("Session: Handler '" . $handler . "' doesn't implement SessionHandlerInterface. Aborting.");
            }
        }
    }
    
    /**
     * Configuration Handle input parameters and configuration defaults
     * @param array &$this->_config Input parameters
     * @return void
     */
    protected function _setConfig() {
        if (empty($this->_config['cookie_lifetime'])) $this->_config['cookie_lifetime'] = 0;
        if (empty($this->_config['cookie_path'])) $this->_config['cookie_path'] = NULL;
        if (empty($this->_config['cookie_domain'])) $this->_config['cookie_domain'] = NULL;
        if (empty($this->_config['cookie_secure'])) $this->_config['cookie_secure'] = NULL;
        session_set_cookie_params($this->_config['cookie_lifetime'], $this->_config['cookie_path'], $this->_config['cookie_domain'], $this->_config['cookie_secure'], TRUE);
        
        if (!empty($this->_config['sess_cookie_name'])) ini_set('session.name', $this->_config['sess_cookie_name']);
        if (!empty($this->_config['sess_expiration'])) ini_set('session.gc_maxlifetime', (int) $this->_config['sess_expiration']);
        if (!empty($this->_config['sess_save_path'])) ini_set('session.sess_save_path', $this->_config['sess_save_path']);
        ini_set('session.serialize_handler', 'php_serialize');
        
        // Security is king
        ini_set('session.use_trans_sid', 0);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.use_cookies', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.hash_function', 1);
        ini_set('session.hash_bits_per_character', 4);
        
        //钩子
        Hooks::call(C('hooks.init_session'));
    }
    
    /**
     * __get()
     * @param string $key a session data key
     * @return mixed
     */
    public function __get($key) {
        // Note: Keep this order the same, just in case somebody wants to
        // use 'session_id' as a session data key, for whatever reason
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } elseif ($key === 'session_id') {
            return session_id();
        }
        return NULL;
    }
    
    /**
     * __set()
     * @param string $key key
     * @param mixed $value value
     * @return void
     */
    public function __set($key, $value) {
        $_SESSION[$key] = $value;
    }
}
