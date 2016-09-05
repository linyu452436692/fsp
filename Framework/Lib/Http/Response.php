<?php
namespace FES\Lib\Http;
/**
 * The web Response class represents an HTTP response
 */
class Response
{
    const FORMAT_RAW = 'raw';
    const FORMAT_HTML = 'html';
    const FORMAT_JSON = 'json';
    const FORMAT_JSONP = 'jsonp';
    const FORMAT_XML = 'xml';

    /**
     * @var string the response format. This determines how to convert [[data]] into [[content]]
     * when the latter is not set. The value of this property must be one of the keys declared in the [[formatters] array.
     * By default, the following formats are supported:
     *
     * - [[FORMAT_RAW]]: the data will be treated as the response content without any conversion.
     *   No extra HTTP header will be added.
     * - [[FORMAT_HTML]]: the data will be treated as the response content without any conversion.
     *   The "Content-Type" header will set as "text/html" if it is not set previously.
     * - [[FORMAT_JSON]]: the data will be converted into JSON format, and the "Content-Type"
     *   header will be set as "application/json".
     * - [[FORMAT_JSONP]]: the data will be converted into JSONP format, and the "Content-Type"
     *   header will be set as "text/javascript". Note that in this case `$data` must be an array
     *   with "data" and "callback" elements. The former refers to the actual data to be sent,
     *   while the latter refers to the name of the JavaScript callback.
     * - [[FORMAT_XML]]: the data will be converted into XML format. Please refer to [[XmlResponseFormatter]]
     *   for more details.
     *
     * You may customize the formatting process or support additional formats by configuring [[formatters]].
     * @see formatters
     */
    public $format = self::FORMAT_HTML;
    /**
     * @var array the formatters for converting data into the response content of the specified [[format]].
     * The array keys are the format names, and the array values are the corresponding configurations
     * for creating the formatter objects.
     * @see format
     */
    public $formatters = [
        self::FORMAT_JSON,
        self::FORMAT_JSONP,
        self::FORMAT_XML,
    ];
    /**
     * @var mixed the original response data. When this is not null, it will be converted into [[content]]
     * according to [[format]] when the response is being sent out.
     * @see content
     */
    public $data;
    /**
     * @var string the response content. When [[data]] is not null, it will be converted into [[content]]
     * according to [[format]] when the response is being sent out.
     * @see data
     */
    public $content;
    /**
     * @var resource|array the stream to be sent. This can be a stream handle or an array of stream handle,
     * the begin position and the end position. Note that when this property is set, the [[data]] and [[content]]
     * properties will be ignored by [[send()]].
     */
    public $stream;
    /**
     * @var string the charset of the text response. If not set, it will use
     * the value of [[Application::charset]].
     */
    public $charset = 'utf-8';
    /**
     * @var string the HTTP status description that comes together with the status code.
     * @see httpStatuses
     */
    public $statusText = 'OK';
    /**
     * @var string the version of the HTTP protocol to use. If not set, it will be determined via `$_SERVER['SERVER_PROTOCOL']`,
     * or '1.1' if that is not available.
     */
    public $version = '1.0';
    /**
     * @var array list of HTTP status codes and the corresponding texts
     */
    public static $httpStatuses = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        118 => 'Connection timed out',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        210 => 'Content Different',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        310 => 'Too many Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested range unsatisfiable',
        417 => 'Expectation failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable entity',
        423 => 'Locked',
        424 => 'Method failure',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway or Proxy Error',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        507 => 'Insufficient storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];

    /**
     * @var integer the HTTP status code to send with the response.
     */
    private $_statusCode = 200;
    /**
     * @var HeaderCollection
     */
    private $_headers;
    
    public function __construct() {
        $this->request = new Request();
        $this->_headers = $this->request->getHeaders();
    }

    /**
     * @return integer the HTTP status code to send with the response.
     */
    public function getStatusCode()
    {
        return $this->_statusCode;
    }

    /**
     * Sets the response status code.
     * This method will set the corresponding status text if `$text` is null.
     * @param integer $value the status code
     * @param string $text the status text. If not set, it will be set automatically based on the status code.
     * @throws \Exception if the status code is invalid.
     */
    public function setStatusCode($value, $text = null)
    {
        if ($value === null) {
            $value = 200;
        }
        $this->_statusCode = (int) $value;
        if ($text === null) {
            $this->statusText = isset(self::$httpStatuses[$this->_statusCode]) ? self::$httpStatuses[$this->_statusCode] : '';
        } else {
            $this->statusText = $text;
        }
    }
    
    /**
     * @desc set the HTTP headers
     * @param array $headers
     * @author steven 2015年8月17日
     */
    public function setHeaders(array $headers) {
        foreach ($headers as $name => $value) {
            $this->_headers[$name] = $value;
        }
    }

    /**
     * Sends the response to the client.
     */
    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();
    }

    /**
     * Sends the response headers to the client
     */
    protected function sendHeaders()
    {
        if (headers_sent()) {
            return;
        }
        $statusCode = $this->getStatusCode();
        header("HTTP/{$this->version} $statusCode {$this->statusText}");
        if (!empty($this->_headers)) {
            foreach ($this->_headers as $name => $value) {
                $name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
                // set replace for first occurrence of header but false afterwards to allow multiple
                header("$name: $value");
            }
        }
    }

    /**
     * Sends the response content to the client
     */
    protected function sendContent()
    {
        if ($this->stream === null) {
            echo $this->content;
            return;
        }

        set_time_limit(0); // Reset time limit for big files
        $chunkSize = 8 * 1024 * 1024; // 8MB per chunk

        if (is_array($this->stream)) {
            list ($handle, $begin, $end) = $this->stream;
            fseek($handle, $begin);
            while (!feof($handle) && ($pos = ftell($handle)) <= $end) {
                if ($pos + $chunkSize > $end) {
                    $chunkSize = $end - $pos + 1;
                }
                echo fread($handle, $chunkSize);
                flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
            }
            fclose($handle);
        } else {
            while (!feof($this->stream)) {
                echo fread($this->stream, $chunkSize);
                flush();
            }
            fclose($this->stream);
        }
    }

    /**
     * Redirects the browser to the specified URL.
     */
    public function redirect($url, $statusCode = 302, $checkAjax = false)
    {
        if ($checkAjax) {
            if ($this->request->getIsPjax()) {
                $this->setHeaders(['X-Pjax-Url' =>$url]);
            } elseif ($this->request->getIsAjax()) {
                $this->setHeaders(['X-Redirect' => $url]);
            } else {
                $this->setHeaders(['Location' => $url]);
            }
        } else {
            $this->setHeaders(['Location' => $url]);
        }
        $this->setStatusCode($statusCode);
        return $this;
    }

    /**
     * Refreshes the current page.
     */
    public function refresh($anchor = '')
    {
        return $this->redirect($this->request->getUrl() . $anchor);
    }
    
}
