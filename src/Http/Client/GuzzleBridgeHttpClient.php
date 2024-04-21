<?php

declare(strict_types=1);

namespace OutlineApiClient\Http\Client;

use GuzzleHttp\Exception\GuzzleException;
use OutlineApiClient\Exceptions\HttpClientException;
use OutlineApiClient\Http\OutlineHttpClientInterface;
use OutlineApiClient\Http\RequestInterface;
use OutlineApiClient\Http\Response;
use OutlineApiClient\Http\ResponseInterface;

class GuzzleBridgeHttpClient implements OutlineHttpClientInterface
{
    public function __construct(private \GuzzleHttp\ClientInterface $guzzleClient)
    {
    }

    public function send(RequestInterface $request): ResponseInterface
    {
        $options = [
            'headers' => $request->getHeaders(),
            'body' => $request->getBody()
        ];

        try {
            $response = $this->guzzleClient->request($request->getMethod(), $request->getUri(), $options);
        } catch (GuzzleException $e) {
            throw new HttpClientException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }

        return (new Response())
            ->setStatusCode($response->getStatusCode())
            ->setReasonPhrase($response->getReasonPhrase())
            ->setHeaders($response->getHeaders())
            ->setBody($response->getBody()->getContents());
    }
}
