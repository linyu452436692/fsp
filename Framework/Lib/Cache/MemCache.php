<?php
namespace FES\Lib\Cache;
use FES\Lib\Exception\ExtensionException;
use FES\Lib\Exception\InvalidArgumentException;
class MemCache implements Cache {
    // \Memcache|\Memcached the Memcache instance
    public $handler = NULL;
    /**
     * @var boolean whether to use memcached or memcache as the underlying caching extension.
     * If true, [memcached](http://pecl.php.net/package/memcached) will be used.
     * If false, [memcache](http://pecl.php.net/package/memcache) will be used.
     * Defaults to false.
     */
    public $useMemcached = NULL;
    
    /**
     * @param string $config['persistent_id']: an ID that identifies a Memcached instance. This property is used only when [[useMemcached]] is true.
     * By default the Memcached instances are destroyed at the end of the request. To create an instance that
     * persists between requests, you may specify a unique ID for the instance. All instances created with the
     * same ID will share the same connection.
     * @see http://ca2.php.net/manual/en/memcached.construct.php
     * @param array $confi['servers']:Memcached server list. 
     */
    public function __construct(array $config = []) {
        if (!extension_loaded('memcached')) throw new ExtensionException("MemCache requires PHP memcached extension to be loaded.");
        if (empty($config['servers'])) throw new InvalidArgumentException("Memcached server list cant be empty!");
        if (NULL === $this->handler) {
            $this->handler = empty($config['persistent_id']) ? new \Memcached : new \Memcached($config['persistent_id']);
            $this->handler->addServers($config['servers']);
            $this->handler->setOption(\Memcached::OPT_COMPRESSION, 0);
            $this->handler->setOption(\Memcached::OPT_CONNECT_TIMEOUT, 300);
            $this->handler->setOption(\Memcached::OPT_DISTRIBUTION, \Memcached::DISTRIBUTION_CONSISTENT);
        }
    }
    
    
    /**
     * @name 存储一个元素
     * @param string $key 用于存储值的键名
     * @param string $value 存储的值
     * @param number $expiration 到期时间，默认为 0
     * @param string $prefix 键值前缀
     * @return boolean true|false 成功时返回 TRUE， 或者在失败时返回 FALSEE
     */
    public function set($key, $value, $expiration = 0) {
        $key = $this->_getKey($key);
        return $this->handler->set($key, $value, time() + $expiration);
    }
    
    /**
     * @name 存储多个元素
     * @param array $keys 存放在服务器上的键／值对数组
     * @param number $expiration 到期时间，默认为 0
     * @param string $prefix 键值前缀
     * @return string|boolean string|false 返回检索到的元素的数组 或者在失败时返回 FALSE
     */
    public function setMulti($keys, $expiration = 0) {
        $items = [];
        foreach ($keys as $key => $value) {
            $items[C('APP_NAME') . ':' . $key] = $value;
        }
        return $this->handler->setMulti($items, time() + $expiration);
    }
    
    /**
     * @name 向一个已存在的元素前面追加数据
     * @param string $key 要向前追加数据的元素的key
     * @param string $value 要追加的字符串
     * @param string $prefix 键值前缀
     * @return boolean true|false 成功时返回 TRUE， 或者在失败时返回 FALSEE
     */
    public function prepend($key, $value) {
        $key = $this->_getKey($key);
        return $this->handler->prepend($key, $value);
    }
    
    /**
     * @name 向已存在元素后追加数据
     * @param string $key 用于存储值的键名
     * @param string $value 将要追加的值
     * @param string $prefix 键值前缀
     * @return boolean true|false
     */
    public function append($key, $value) {
        $key = $this->_getKey($key);
        return $this->handler->append($key, $value);
    }
    
    /**
     * @name 检索一个元素
     * @param string $key
     * @return string|boolean string|false返回存储在服务端的元素的值或者在其他情况下返回FALSE
     * @param string $prefix 键值前缀
     */
    public function get($key) {
        $key = $this->_getKey($key);
        return $this->handler->get($key);
    }
    
    /**
     * @name 检索多个元素
     * @param array $keys 要检索的key的数组
     * @return string|boolean string|false 返回检索到的元素的数组 或者在失败时返回 FALSE
     * @param string $prefix 键值前缀
     */
    public function getMulti($keys) {
        foreach ($keys as $key => $value) {
            $keys[$key] = C('APP_NAME') . ':' . $value;
        }
        return $this->handler->getMulti($keys);
    }
    
    /**
     * @name 替换已存在key下的元素
     * @param string $key 用于存储值的键名
     * @param string $value 存储的值
     * @param number $expiration 到期时间，默认为 0
     * @param string $prefix 键值前缀
     * @return boolean true|false 成功时返回 TRUE， 或者在失败时返回 FALSEE
     */
    public function replace($key, $value, $expiration = 0) {
        $key = $this->_getKey($key);
        return $this->handler->replace($key, $value, time() + $expiration);
    }
    
    /**
     * @name 删除一个元素
     * @param string $key 要删除的key
     * @param number $time 服务端等待删除该元素的总时间(或一个Unix时间戳表明的实际删除时间)
     * @param string $prefix 键值前缀
     * @return boolean true|false
     */
    public function delete($key, $delayTime = 0) {
        $key = $this->_getKey($key);
        return $this->handler->delete($key, $delayTime);
    }
    
    //全部清除数据
    public function flush() {
        return $this->handler->flush();
    }
    
    //生成组装后的键值
    private function _getKey($key) {
        if (is_string($key)) {
            $key = strlen($key) <= 32 ? $key : md5($key);
        } else {
            $key = md5(json_encode($key));
        }
        return C('APP_NAME') . ':' . $key;
    }
}