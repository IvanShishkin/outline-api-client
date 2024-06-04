<?php

namespace OutlineApiClient\Api;

use OutlineApiClient\Http\OutlineHttpClientInterface;

interface OutlineApiClientInterface
{
    public function __construct(string $serverUrl, OutlineHttpClientInterface $httpClient);
    public function getServer();
    public function changeHostname(string $newHostnameOrIp);
    public function renameServer(string $newName);
    public function getMetricsEnabled();
    public function changeMetricsEnabled(bool $isEnabled);
    public function changeDefaultPortForNewAccessKey(int $port);
    public function createNewAccessKey();
    public function getAccessKeys();
    public function createNewAccessKeyWithSpecificIdentifer(string $keyId, array $keyData);
    public function getDetailAccessKeys(string $keyId);
    public function deleteAccessKey(string $keyId);
    public function renameAccessKey(string $keyId, string $name);
    public function setDataLimitForAccessKeys(string $keyId, int $limit);
    public function deleteDataLimitForAccessKeys(string $keyId);
    public function getMetricsTransfer();
    public function setDataTransferLimitForAllAccessKeys(int $limit);
    public function deleteAccessKeyDataLimit();
    public function setDataLimitForAccessKey(string $keyId, int $limit);
    public function deleteDataLimitForAccessKey(string $keyId);
}
