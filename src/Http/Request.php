<?php

declare(strict_types=1);

namespace OutlineApiClient\Http;

class Request implements RequestInterface
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var string[]
     */
    private $headers;

    /**
     * @var string
     */
    private $body;


    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return Request
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     * @return Request
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param string[] $headers
     * @return Request
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return Request
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }
}