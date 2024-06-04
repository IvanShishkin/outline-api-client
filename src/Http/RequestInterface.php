<?php

namespace OutlineApiClient\Http;

interface RequestInterface
{
    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod(): string;

    /**
     * Retrieves the URI.
     *
     * @return string
     */
    public function getUri(): string;

    /**
     * Retrieves all message header values.
     *
     * @return string[] Returns an associative array of the message's headers.
     */
    public function getHeaders(): array;

    /**
     * Gets the body of the message.
     *
     * @return string Returns the body as a stream.
     */
    public function getBody(): string;
}