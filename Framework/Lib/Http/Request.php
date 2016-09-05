<?php
namespace FES\Lib\Http;
class Request 
{
    private $_headers;
    private $_rawBody;
    private $_queryParams;
    private $_hostInfo;
    private $_baseUrl;
    private $_scriptUrl;
    private $_scriptFile;
    private $_pathInfo;
    private $_url;
    private $_port;
    private $_securePort;
    private $_contentTypes;
    
    /**
     * Returns the header collection.
     * The header collection contains incoming HTTP headers.
     * @return HeaderCollection the header collection
     */
    public function getHeaders()
    {
        if ($this->_headers === null) {
            foreach ($_SERVER as $name => $value) {
                if (strncmp($name, 'HTTP_', 5) === 0) {
                    $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                    $this->_headers[$name] = $value;
                }
            }
        }

        return $this->_headers;
    }

    /**
     * Returns the method of the current request (e.g. GET, POST, HEAD, PUT, PATCH, DELETE).
     * @return string request method, such as GET, POST, HEAD, PUT, PATCH, DELETE.
     * The value returned is turned into upper case.
     */
    public function getMethod()
    {
        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            return strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
        } else {
            return isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
        }
    }

    /**
     * Returns whether this is a GET request.
     * @return boolean whether this is a GET request.
     */
    public function isGet()
    {
        return $this->getMethod() === 'GET';
    }

    /**
     * Returns whether this is an OPTIONS request.
     * @return boolean whether this is a OPTIONS request.
     */
    public function isOptions()
    {
        return $this->getMethod() === 'OPTIONS';
    }

    /**
     * Returns whether this is a HEAD request.
     * @return boolean whether this is a HEAD request.
     */
    public function isHead()
    {
        return $this->getMethod() === 'HEAD';
    }

    /**
     * Returns whether this is a POST request.
     * @return boolean whether this is a POST request.
     */
    public function isPost()
    {
        return $this->getMethod() === 'POST';
    }

    /**
     * Returns whether this is a DELETE request.
     * @return boolean whether this is a DELETE request.
     */
    public function isDelete()
    {
        return $this->getMethod() === 'DELETE';
    }

    /**
     * Returns whether this is a PUT request.
     * @return boolean whether this is a PUT request.
     */
    public function isPut()
    {
        return $this->getMethod() === 'PUT';
    }

    /**
     * Returns whether this is a PATCH request.
     * @return boolean whether this is a PATCH request.
     */
    public function isPatch()
    {
        return $this->getMethod() === 'PATCH';
    }

    /**
     * Returns whether this is an AJAX (XMLHttpRequest) request.
     * @return boolean whether this is an AJAX (XMLHttpRequest) request.
     */
    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * Returns whether this is a PJAX request
     * @return boolean whether this is a PJAX request
     */
    public function isPjax()
    {
        return $this->isAjax() && !empty($_SERVER['HTTP_X_PJAX']);
    }

    /**
     * Returns whether this is an Adobe Flash or Flex request.
     * @return boolean whether this is an Adobe Flash or Adobe Flex request.
     */
    public function isFlash()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) &&
            (stripos($_SERVER['HTTP_USER_AGENT'], 'Shockwave') !== false || stripos($_SERVER['HTTP_USER_AGENT'], 'Flash') !== false);
    }

    /**
     * Returns the raw HTTP request body.
     * @return string the request body
     */
    public function getRawBody()
    {
        if ($this->_rawBody === null) {
            $this->_rawBody = file_get_contents('php://input');
        }

        return $this->_rawBody;
    }

    /**
     * Returns the request parameters given in the [[queryString]].
     *
     * This method will return the contents of `$_GET` if params where not explicitly set.
     * @return array the request GET parameter values.
     * @see setQueryParams()
     */
    public function getQueryParams()
    {
        if ($this->_queryParams === null) {
            return $_GET;
        }

        return $this->_queryParams;
    }

    /**
     * Returns the named GET parameter value.
     * If the GET parameter does not exist, the second parameter to this method will be returned.
     * @param string $name the GET parameter name. If not specified, whole $_GET is returned.
     * @param mixed $defaultValue the default parameter value if the GET parameter does not exist.
     * @return mixed the GET parameter value
     * @see getBodyParam()
     */
    public function getQueryParam($name, $defaultValue = null)
    {
        $params = $this->getQueryParams();
    
        return isset($params[$name]) ? $params[$name] : $defaultValue;
    }

    /**
     * Returns the schema and host part of the current request URL.
     * The returned URL does not have an ending slash.
     * By default this is determined based on the user request information.
     * You may explicitly specify it by setting the [[setHostInfo()|hostInfo]] property.
     * @return string schema and hostname part (with port number if needed) of the request URL (e.g. `http://www.yiiframework.com`)
     * @see setHostInfo()
     */
    public function getHostInfo()
    {
        if ($this->_hostInfo === null) {
            $secure = $this->isSecureConnection();
            $http = $secure ? 'https' : 'http';
            if (isset($_SERVER['HTTP_HOST'])) {
                $this->_hostInfo = $http . '://' . $_SERVER['HTTP_HOST'];
            } else {
                $this->_hostInfo = $http . '://' . $_SERVER['SERVER_NAME'];
                $port = $secure ? $this->getSecurePort() : $this->getPort();
                if (($port !== 80 && !$secure) || ($port !== 443 && $secure)) {
                    $this->_hostInfo .= ':' . $port;
                }
            }
        }

        return $this->_hostInfo;
    }

    /**
     * Returns the relative URL for the application.
     * This is similar to [[scriptUrl]] except that it does not include the script file name,
     * and the ending slashes are removed.
     * @return string the relative URL for the application
     * @see setScriptUrl()
     */
    public function getBaseUrl()
    {
        if ($this->_baseUrl === null) {
            $this->_baseUrl = rtrim(dirname($this->getScriptUrl()), '\\/');
        }

        return $this->_baseUrl;
    }

    /**
     * Returns the relative URL of the entry script.
     * The implementation of this method referenced Zend_Controller_Request_Http in Zend Framework.
     * @return string the relative URL of the entry script.
     * @throws \Exception if unable to determine the entry script URL
     */
    public function getScriptUrl()
    {
        if ($this->_scriptUrl === null) {
            $scriptFile = $this->getScriptFile();
            $scriptName = basename($scriptFile);
            if (basename($_SERVER['SCRIPT_NAME']) === $scriptName) {
                $this->_scriptUrl = $_SERVER['SCRIPT_NAME'];
            } elseif (basename($_SERVER['PHP_SELF']) === $scriptName) {
                $this->_scriptUrl = $_SERVER['PHP_SELF'];
            } elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName) {
                $this->_scriptUrl = $_SERVER['ORIG_SCRIPT_NAME'];
            } elseif (($pos = strpos($_SERVER['PHP_SELF'], '/' . $scriptName)) !== false) {
                $this->_scriptUrl = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $scriptName;
            } elseif (!empty($_SERVER['DOCUMENT_ROOT']) && strpos($scriptFile, $_SERVER['DOCUMENT_ROOT']) === 0) {
                $this->_scriptUrl = str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $scriptFile));
            } else {
                throw new \Exception('Unable to determine the entry script URL.');
            }
        }

        return $this->_scriptUrl;
    }

    /**
     * Returns the entry script file path.
     * The default implementation will simply return `$_SERVER['SCRIPT_FILENAME']`.
     * @return string the entry script file path
     */
    public function getScriptFile()
    {
        return isset($this->_scriptFile) ? $this->_scriptFile : $_SERVER['SCRIPT_FILENAME'];
    }

    /**
     * Returns the path info of the currently requested URL.
     * A path info refers to the part that is after the entry script and before the question mark (query string).
     * The starting and ending slashes are both removed.
     * @return string part of the request URL that is after the entry script and before the question mark.
     * Note, the returned path info is already URL-decoded.
     * @throws \Exception if the path info cannot be determined due to unexpected server configuration
     */
    public function getPathInfo()
    {
        if ($this->_pathInfo === null) {
            $this->_pathInfo = $this->resolvePathInfo();
        }

        return $this->_pathInfo;
    }

    /**
     * Resolves the path info part of the currently requested URL.
     * A path info refers to the part that is after the entry script and before the question mark (query string).
     * The starting slashes are both removed (ending slashes will be kept).
     * @return string part of the request URL that is after the entry script and before the question mark.
     * Note, the returned path info is decoded.
     * @throws \Exception if the path info cannot be determined due to unexpected server configuration
     */
    protected function resolvePathInfo()
    {
        $pathInfo = $this->getUrl();

        if (($pos = strpos($pathInfo, '?')) !== false) {
            $pathInfo = substr($pathInfo, 0, $pos);
        }

        $pathInfo = urldecode($pathInfo);

        // try to encode in UTF8 if not so
        // http://w3.org/International/questions/qa-forms-utf-8.html
        if (!preg_match('%^(?:
            [\x09\x0A\x0D\x20-\x7E]              # ASCII
            | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
            | \xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
            | \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
            | \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
            | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
            )*$%xs', $pathInfo)
        ) {
            $pathInfo = utf8_encode($pathInfo);
        }

        $scriptUrl = $this->getScriptUrl();
        $baseUrl = $this->getBaseUrl();
        if (strpos($pathInfo, $scriptUrl) === 0) {
            $pathInfo = substr($pathInfo, strlen($scriptUrl));
        } elseif ($baseUrl === '' || strpos($pathInfo, $baseUrl) === 0) {
            $pathInfo = substr($pathInfo, strlen($baseUrl));
        } elseif (isset($_SERVER['PHP_SELF']) && strpos($_SERVER['PHP_SELF'], $scriptUrl) === 0) {
            $pathInfo = substr($_SERVER['PHP_SELF'], strlen($scriptUrl));
        } else {
            throw new \Exception('Unable to determine the path info of the current request.');
        }

        if ($pathInfo[0] === '/') {
            $pathInfo = substr($pathInfo, 1);
        }

        return (string) $pathInfo;
    }

    /**
     * Returns the currently requested absolute URL.
     * This is a shortcut to the concatenation of [[hostInfo]] and [[url]].
     * @return string the currently requested absolute URL.
     */
    public function getAbsoluteUrl()
    {
        return $this->getHostInfo() . $this->getUrl();
    }

    /**
     * Returns the currently requested relative URL.
     * This refers to the portion of the URL that is after the [[hostInfo]] part.
     * It includes the [[queryString]] part if any.
     * @return string the currently requested relative URL. Note that the URI returned is URL-encoded.
     * @throws \Exception if the URL cannot be determined due to unusual server configuration
     */
    public function getUrl()
    {
        if ($this->_url === null) {
            $this->_url = $this->resolveRequestUri();
        }

        return $this->_url;
    }

    /**
     * Resolves the request URI portion for the currently requested URL.
     * This refers to the portion that is after the [[hostInfo]] part. It includes the [[queryString]] part if any.
     * The implementation of this method referenced Zend_Controller_Request_Http in Zend Framework.
     * @return string|boolean the request URI portion for the currently requested URL.
     * Note that the URI returned is URL-encoded.
     * @throws \Exception if the request URI cannot be determined due to unusual server configuration
     */
    protected function resolveRequestUri()
    {
        if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // IIS
            $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
            if ($requestUri !== '' && $requestUri[0] !== '/') {
                $requestUri = preg_replace('/^(http|https):\/\/[^\/]+/i', '', $requestUri);
            }
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0 CGI
            $requestUri = $_SERVER['ORIG_PATH_INFO'];
            if (!empty($_SERVER['QUERY_STRING'])) {
                $requestUri .= '?' . $_SERVER['QUERY_STRING'];
            }
        } else {
            throw new \Exception('Unable to determine the request URI.');
        }

        return $requestUri;
    }

    /**
     * Returns part of the request URL that is after the question mark.
     * @return string part of the request URL that is after the question mark
     */
    public function getQueryString()
    {
        return isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
    }

    /**
     * Return if the request is sent via secure channel (https).
     * @return boolean if the request is sent via secure channel (https)
     */
    public function isSecureConnection()
    {
        return isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1)
            || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0;
    }

    /**
     * Returns the server name.
     * @return string server name
     */
    public function getServerName()
    {
        return $_SERVER['SERVER_NAME'];
    }

    /**
     * Returns the server port number.
     * @return integer server port number
     */
    public function getServerPort()
    {
        return (int) $_SERVER['SERVER_PORT'];
    }

    /**
     * Returns the URL referrer, null if not present
     * @return string URL referrer, null if not present
     */
    public function getReferrer()
    {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
    }

    /**
     * Returns the user agent, null if not present.
     * @return string user agent, null if not present
     */
    public function getUserAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
    }

    /**
     * Returns the user IP address.
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    public function getUserIP($type = 0, $adv = false)
    {
        $type = $type ? 1 : 0;
        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos)
                    unset($arr[$pos]);
                $ip = trim($arr[0]);
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip = $long ? array($ip, $long) : array( '0.0.0.0', 0);
        return $ip[$type];
    }

    /**
     * Returns the user host name, null if it cannot be determined.
     * @return string user host name, null if cannot be determined
     */
    public function getUserHost()
    {
        return isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : null;
    }

    /**
     * @return string the username sent via HTTP authentication, null if the username is not given
     */
    public function getAuthUser()
    {
        return isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null;
    }

    /**
     * @return string the password sent via HTTP authentication, null if the password is not given
     */
    public function getAuthPassword()
    {
        return isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null;
    }

    /**
     * Returns the port to use for insecure requests.
     * Defaults to 80, or the port specified by the server if the current
     * request is insecure.
     * @return integer port number for insecure requests.
     * @see setPort()
     */
    public function getPort()
    {
        if ($this->_port === null) {
            $this->_port = !$this->isSecureConnection() && isset($_SERVER['SERVER_PORT']) ? (int) $_SERVER['SERVER_PORT'] : 80;
        }

        return $this->_port;
    }

    /**
     * Returns the port to use for secure requests.
     * Defaults to 443, or the port specified by the server if the current
     * request is secure.
     * @return integer port number for secure requests.
     * @see setSecurePort()
     */
    public function getSecurePort()
    {
        if ($this->_securePort === null) {
            $this->_securePort = $this->isSecureConnection() && isset($_SERVER['SERVER_PORT']) ? (int) $_SERVER['SERVER_PORT'] : 443;
        }

        return $this->_securePort;
    }

    /**
     * Returns request content-type
     * The Content-Type header field indicates the MIME type of the data
     * contained in [[getRawBody()]] or, in the case of the HEAD method, the
     * media type that would have been sent had the request been a GET.
     * For the MIME-types the user expects in response, see [[acceptableContentTypes]].
     * @return string request content-type. Null is returned if this information is not available.
     * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.17
     * HTTP 1.1 header field definitions
     */
    public function getContentType()
    {
        if (isset($_SERVER["CONTENT_TYPE"])) {
            return $_SERVER["CONTENT_TYPE"];
        } elseif (isset($_SERVER["HTTP_CONTENT_TYPE"])) {
            //fix bug https://bugs.php.net/bug.php?id=66606
            return $_SERVER["HTTP_CONTENT_TYPE"];
        }

        return null;
    }
}