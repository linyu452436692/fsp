<?php
namespace FES\Lib\Cache;
interface Cache {

    public function set($key, $value, $expiration);

    public function get($key);

    public function delete($key, $delayTime);

    public function flush();
}
