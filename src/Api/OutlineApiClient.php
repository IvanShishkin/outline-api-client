<?php

declare(strict_types=1);

namespace OutlineApiClient\Api;

use OutlineApiClient\Exceptions\HttpClientException;
use OutlineApiClient\Exceptions\OutlineApiException;
use OutlineApiClient\Http\OutlineHttpClientInterface;
use OutlineApiClient\Http\Request;
use OutlineApiClient\Http\RequestInterface;

class OutlineApiClient implements OutlineApiClientInterface
{
    public function __construct(
        private string $serverUrl,
        private OutlineHttpClientInterface $client
    ) {
    }

    /**
     * Returns information about the server
     * 
     * @return array
     * @throws OutlineApiException
     */
    public function getServer()
    {
        $request = $this->buildRequest('GET', "/server");
        $response = $this->send($request);

        return self::jsonDecode($response->getBody(), true);
    }

    /**
     * Changes the hostname for access keys. Must be a valid hostname or IP address.
     * If it's a hostname, DNS must be set up independently of this API.
     *
     * @param string $newHostnameOrIp
     * @return bool
     * @throws OutlineApiException
     */
    public function changeHostname(string $newHostnameOrIp): bool
    {
        $request = $this->buildRequest('PUT', "/server/hostname-for-access-keys", ['hostname' => $newHostnameOrIp]);
        $response = $this->send($request);

        return $response->getStatusCode() === 204;
    }

    /**
     * Renames the server
     *
     * @param string $newName
     * @return bool
     * @throws OutlineApiException
     */
    public function renameServer(string $newName): bool
    {
        $request = $this->buildRequest('PUT', "/name", ['name' => $newName]);
        $response = $this->send($request);

        return $response->getStatusCode() === 204;
    }

    /**
     * Returns whether metrics is being shared
     *
     * @return mixed
     * @throws OutlineApiException
     */
    public function getMetricsEnabled()
    {
        $request = $this->buildRequest('GET', "/metrics/enabled");
        $response = $this->send($request);

        return self::jsonDecode($response->getBody(), true);
    }

    /**
     * Enables or disables sharing of metrics
     *
     * @param bool $isEnabled
     * @return mixed
     * @throws OutlineApiException
     */
    public function changeMetricsEnabled(bool $isEnabled)
    {
        $request = $this->buildRequest('PUT', "/metrics/enabled", ['metricsEnabled' => $isEnabled]);
        $response = $this->send($request);

        return self::jsonDecode($response->getBody(), true);
    }

    /**
     * Changes the default port for newly created access
     *
     * @param int $port
     * @return bool
     * @throws OutlineApiException
     */
    public function changeDefaultPortForNewAccessKey(int $port)
    {
        $request = $this->buildRequest('PUT', "/server/port-for-new-access-keys", ['port' => $port]);
        $response = $this->send($request);

        return $response->getStatusCode() === 204;
    }

    /**
     * Creates a new access key
     *
     * @return false|mixed
     * @throws OutlineApiException
     */
    public function createNewAccessKey()
    {
        $request = $this->buildRequest('POST', "/access-keys");
        $response = $this->send($request);

        if ($response->getStatusCode() === 201) {
            return self::jsonDecode($response->getBody(), true);
        } else {
            return false;
        }
    }

    /**
     * Lists the access keys
     *
     * @return mixed
     * @throws OutlineApiException
     */
    public function getAccessKeys()
    {
        $request = $this->buildRequest('GET', "/access-keys");
        $response = $this->send($request);

        return self::jsonDecode($response->getBody(), true);
    }

    /**
     * Creates a new access key with a specific identifer
     * @param string $keyId
     * @param array $keyData
     * @return false|mixed
     * @throws OutlineApiException
     */
    public function createNewAccessKeyWithSpecificIdentifer(string $keyId, array $keyData)
    {
        $request = $this->buildRequest('POST', "/access-keys/{$keyId}", $keyData);
        $response = $this->send($request);

        if ($response->getStatusCode() === 201) {
            return self::jsonDecode($response->getBody(), true);
        } else {
            return false;
        }
    }

    /**
     * Get an access key
     *
     * @param string $keyId
     * @return array
     * @throws OutlineApiException
     */
    public function getDetailAccessKeys(string $keyId)
    {
        $request = $this->buildRequest('GET', "/access-keys/{$keyId}");
        $response = $this->send($request);

        return self::jsonDecode($response->getBody(), true);
    }

    /**
     * Deletes an access key
     *
     * @param string $keyId
     * @return bool
     * @throws OutlineApiException
     */
    public function deleteAccessKey(string $keyId): bool
    {
        $request = $this->buildRequest('DELETE', "/access-keys/{$keyId}");
        $response = $this->send($request);

        return $response->getStatusCode() === 204;
    }

    /**
     * Renames an access key
     *
     * @param string $keyId
     * @param string $name
     * @return bool
     * @throws OutlineApiException
     */
    public function renameAccessKey(string $keyId, string $name): bool
    {
        $request = $this->buildRequest('PUT', "/access-keys/{$keyId}/name", ['name' => $name]);
        $response = $this->send($request);

        return $response->getStatusCode() === 204;
    }

    /**
     * Sets a data limit for the given access key
     *
     * @param string $keyId
     * @param int $limit
     * @return bool
     * @throws OutlineApiException
     */
    public function setDataLimitForAccessKeys(string $keyId, int $limit)
    {
        $request = $this->buildRequest('PUT', "/access-keys/{$keyId}/data-limit", ['limit' => ['bytes' => $limit]]);
        $response = $this->send($request);

        return $response->getStatusCode() === 204;
    }

    /**
     * Removes the data limit on the given access key.
     *
     * @param string $keyId
     * @return bool
     * @throws OutlineApiException
     */
    public function deleteDataLimitForAccessKeys(string $keyId)
    {
        $request = $this->buildRequest('DELETE', "/access-keys/{$keyId}/data-limit");
        $response = $this->send($request);

        return $response->getStatusCode() === 204;
    }

    /**
     * Returns the data transferred per access key
     *
     * @return mixed
     * @throws OutlineApiException
     */
    public function getMetricsTransfer()
    {
        $request = $this->buildRequest('GET', "/metrics/transfer");
        $response = $this->send($request);

        return self::jsonDecode($response->getBody(), true);
    }

    /**
     * Sets a data transfer limit for all access keys
     *
     * @param int $limit
     * @return mixed
     * @throws OutlineApiException
     */
    public function setDataTransferLimitForAllAccessKeys(int $limit)
    {
        $request = $this->buildRequest('PUT', "/server/access-key-data-limit", ['limit' => ['bytes' => $limit]]);
        $response = $this->send($request);


        return self::jsonDecode($response->getBody(), true);
    }

    /**
     * Removes the access key data limit, lifting data transfer restrictions on all access keys.
     *
     * @return mixed
     * @throws OutlineApiException
     */
    public function deleteAccessKeyDataLimit()
    {
        $request = $this->buildRequest('DELETE', "/server/access-key-data-limit");
        $response = $this->send($request);

        return $response->getStatusCode() === 204;
    }

    /**
     * Sets a data limit for the given access key
     *
     * @param string $keyId
     * @param int $limit
     * @return mixed
     * @throws OutlineApiException
     */
    public function setDataLimitForAccessKey(string $keyId, int $limit)
    {
        $request = $this->buildRequest('PUT', "/access-keys/{$keyId}/data-limit", ['limit' => ['bytes' => $limit]]);
        $response = $this->send($request);

        return self::jsonDecode($response->getBody(), true);
    }

    /**
     * Removes the data limit on the given access key.
     *
     * @param string $keyId
     * @return bool
     * @throws OutlineApiException
     */
    public function deleteDataLimitForAccessKey(string $keyId): bool
    {
        $request = $this->buildRequest('DELETE', "/access-keys/{$keyId}/data-limit");
        $response = $this->send($request);

        return $response->getStatusCode() === 204;
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $body
     * @return \OutlineApiClient\Http\ResponseInterface
     * @throws OutlineApiException
     */
    protected function send(RequestInterface $request): \OutlineApiClient\Http\ResponseInterface
    {
        try {
            return $this->client->send($request);
        } catch (HttpClientException $exception) {
            throw new OutlineApiException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }

    /**
     * @param string $uri
     * @return string
     */
    protected function makeRequestUrl(string $uri): string
    {
        return $this->serverUrl . $uri;
    }

    protected function buildRequest(string $method, string $uri, array $body = [], array $headers = []): RequestInterface
    {
        $headers = array_merge($this->getDefaultHeaders(), $headers);

        return (new Request())
            ->setMethod($method)
            ->setUri($this->makeRequestUrl($uri))
            ->setHeaders($headers)
            ->setBody(self::jsonEncode($body));
    }

    /**
     * @param $value
     * @param int $options
     * @param int $depth
     * @return string
     * @throws \InvalidArgumentException
     */
    protected static function jsonEncode($value, int $options = 0, int $depth = 512): string
    {
        $json = \json_encode($value, $options, $depth);
        if (\JSON_ERROR_NONE !== \json_last_error()) {
            throw new \InvalidArgumentException('json_encode error: '.\json_last_error_msg());
        }

        /** @var string */
        return $json;
    }

    /**
     * @param string $json
     * @param bool $assoc
     * @param int $depth
     * @param int $options
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected static function jsonDecode(string $json, bool $assoc = false, int $depth = 512, int $options = 0)
    {
        $data = \json_decode($json, $assoc, $depth, $options);
        if (\JSON_ERROR_NONE !== \json_last_error()) {
            throw new \InvalidArgumentException('json_decode error: '.\json_last_error_msg());
        }

        return $data;
    }

    protected function getDefaultHeaders(): array
    {
        return [
            'Content type' => 'application/json',
        ];
    }
}
