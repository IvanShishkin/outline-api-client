<?php

declare(strict_types=1);

namespace OutlineApiClient\Http\Client;

use OutlineApiClient\Exceptions\HttpClientException;
use OutlineApiClient\Http\OutlineHttpClientInterface;
use OutlineApiClient\Http\RequestInterface;
use OutlineApiClient\Http\Response;
use OutlineApiClient\Http\ResponseInterface;

class CurlClient implements OutlineHttpClientInterface
{
    const DEFAULT_TIMEOUT = 5; // seconds

    /**
     * @var int
     */
    protected int $timeout = self::DEFAULT_TIMEOUT;

    /**
     * @var resource
     */
    protected $curl;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (isset($config['timeout'])) {
            $this->timeout = (int) $config['timeout'];
        }

        $this->curl = \curl_init();
    }

    public function __destruct()
    {
        if ($this->curl) {
            \curl_close($this->curl);
        }
    }
    public function send(RequestInterface $request): ResponseInterface
    {
        \curl_setopt_array(
            $this->curl,
            [
                \CURLOPT_URL => $request->getUri(),
                \CURLOPT_TIMEOUT => $this->timeout,
                \CURLOPT_PROTOCOLS => \CURLPROTO_HTTP|\CURLPROTO_HTTPS,
                \CURLOPT_CUSTOMREQUEST => $request->getMethod(),
                \CURLOPT_RETURNTRANSFER => true,
                \CURLOPT_HTTPHEADER => $this->joinHeaders($request->getHeaders()),
                \CURLOPT_POSTFIELDS => $request->getBody(),
                \CURLOPT_FOLLOWLOCATION => false,
                \CURLOPT_HEADER => true,
            ]
        );

        $response = \curl_exec($this->curl);

        // For any response code curl_exec returns a header+body string
        // For networking errors curl_exec returns false, curl_errno and curl_error return the error

        // A networking error
        if ($response === false) {
            throw new HttpClientException(\curl_error($this->curl),\curl_errno($this->curl));
        }

        $statusCode = \curl_getinfo($this->curl, \CURLINFO_HTTP_CODE);
        $reasonPhrase = $this->getReasonPhrase($statusCode);

        $headerSize = \curl_getinfo($this->curl, \CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        $headersRaw = explode("\n", $header);
        $headersRaw = array_map('trim', $headersRaw); // remove possible \r's
        $headersRaw = array_filter($headersRaw); // remove newlines
        array_shift($headersRaw); // remove HTTP code line

        $headers = [];
        foreach ($headersRaw as $headerLine) {
            list($header, $value) = explode(':', $headerLine, 2);
            $headers[$header] = ltrim($value, ' ');
        }

        return (new Response())
            ->setStatusCode($statusCode)
            ->setReasonPhrase($reasonPhrase)
            ->setHeaders($headers)
            ->setBody($body ?: '');
    }

    private function joinHeaders(array $headers = []): array
    {
        $joinedHeaders = [];
        foreach ($headers as $key => $value) {
            $joinedHeaders[] = $key . ': ' . $value;
        }

        return $joinedHeaders;
    }

    private function getReasonPhrase(int $statusCode): string
    {
        static $phrase = [
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-status',
            208 => 'Already Reported',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => 'Switch Proxy',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',
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
            414 => 'Request-URI Too Large',
            415 => 'Unsupported Media Type',
            416 => 'Requested range not satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            425 => 'Unordered Collection',
            426 => 'Upgrade Required',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',
            451 => 'Unavailable For Legal Reasons',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Time-out',
            505 => 'HTTP Version not supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            508 => 'Loop Detected',
            510 => 'Not Extended',
            511 => 'Network Authentication Required',
        ];

        return isset($phrase[$statusCode]) ? $phrase[$statusCode] : '';
    }
}