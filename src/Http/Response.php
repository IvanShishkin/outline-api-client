<?php

declare(strict_types=1);

namespace OutlineApiClient\Http;

class Response implements ResponseInterface
{
    private int $statusCode;
    private string $reasonPhrase;
    private string $body;
    private array $headers;

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    public function setReasonPhrase(string $reasonPhrase)
    {
        $this->reasonPhrase = $reasonPhrase;
        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body)
    {
        $this->body = $body;
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }
}
