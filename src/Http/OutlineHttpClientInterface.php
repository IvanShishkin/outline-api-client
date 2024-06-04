<?php

namespace OutlineApiClient\Http;

use OutlineApiClient\Exceptions\HttpClientException;

interface OutlineHttpClientInterface
{
    /**
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws HttpClientException
     */
    public function send(RequestInterface $request): ResponseInterface;
}