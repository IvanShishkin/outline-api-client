<?php
namespace OutlineApiClient;

use OutlineApiClient\Api\OutlineApiClientInterface;
use OutlineApiClient\Exceptions\OutlineApiException;
use OutlineApiClient\Exceptions\OutlineKeyException;
use OutlineApiClient\Exceptions\OutlineKeyNotFoundException;

class OutlineKey
{
    protected array $data = [
        'id' => -1,
        'name' => '',
        'password' => '',
        'port' => -1,
        'method' => '',
        'accessUrl' => ''
    ];

    protected bool $isLoaded = false;

    public function __construct(private OutlineApiClientInterface $api)
    {
    }

    protected function setData(array $setData)
    {
        $this->data = array_merge($this->data, $setData);
    }

    public function getData(): array
    {
        return $this->data;
    }

    protected function isLoaded(): bool
    {
        return $this->isLoaded;
    }

    /**
     * @throws OutlineKeyException
     * @throws OutlineKeyNotFoundException|OutlineApiException
     */
    public function get($keyId, $searchKey = 'id'): array
    {
        $getKeyList = $this->api->getAccessKeys();
        $findKeyData = [];

        if (empty($getKeyList)) {
            throw new OutlineKeyException('Not transferred keys list');
        }

        $list = $getKeyList['accessKeys'];
        foreach ($list as $item) {
            if ($keyId == $item[$searchKey]) {
                $findKeyData = $item;
                break;
            }
        }

        if (empty($findKeyData)) {
            throw new OutlineKeyNotFoundException('Key not found. You may create new key');
        }

        return $findKeyData;
    }

    /**
     * @throws OutlineKeyNotFoundException
     * @throws OutlineKeyException
     * @throws OutlineApiException
     */
    public function getByName(string $name): array
    {
        return $this->get($name, 'name');
    }

    /**
     * @throws OutlineKeyException
     * @throws OutlineKeyNotFoundException
     * @throws OutlineApiException
     */
    public function load($keyId): OutlineKey
    {
        $data = $this->get($keyId);
        $this->setData($data);
        $this->isLoaded = true;

        return $this;
    }

    public function getId()
    {
        return (string) $this->data['id'];
    }

    public function getName()
    {
        return $this->data['name'];
    }

    /**
     * @throws OutlineApiException
     */
    public function getTransfer()
    {
        $transfer = 0;

        $transferData = $this->api->getMetricsTransfer();

        if (
            isset($transferData['bytesTransferredByUserId'])
            && array_key_exists($this->getId(), $transferData['bytesTransferredByUserId'])
        ) {
            $transfer = $transferData['bytesTransferredByUserId'][$this->getId()];
        }

        return $transfer;
    }

    public function getLimit()
    {
        return $this->data['dataLimit']['bytes'];
    }

    public function getAccessUrl()
    {
        return $this->data['accessUrl'];
    }

    /**
     * @throws OutlineKeyException
     * @throws OutlineApiException
     */
    public function rename(string $newName)
    {
        if (!$this->isLoaded()) {
            throw new OutlineKeyException('Failed rename key. Need load data key');
        }

        $setName = $this->api->renameAccessKey($this->getId(), $newName);
        if (!$setName) {
            throw new OutlineKeyException('Error rename. Please contact administrator');
        } else {
            $this->setData(['name' => $newName]);
        }
    }

    /**
     * @throws OutlineKeyException
     * @throws OutlineApiException
     */
    public function limit(int $limitValue)
    {
        if ($this->isLoaded()) {
            $setLimit = $this->api->setDataLimitForAccessKeys($this->getId(), $limitValue);

            if (!$setLimit) {
                throw new OutlineKeyException('Error set limit. Please contact administrator');
            } else {
                $this->setData([
                    'dataLimit' => [
                        'bytes' => $limitValue
                    ]
                ]);
            }
        } else {
            throw new OutlineKeyException('Failed set limit for key. Need load data key');
        }
    }

    /**
     * @throws OutlineKeyException
     * @throws OutlineApiException
     */
    public function deleteLimit(): void
    {
        if ($this->isLoaded()) {
            $deleteLimit = $this->api->deleteDataLimitForAccessKey($this->getId());

            if (!$deleteLimit) {
                throw new OutlineKeyException('Error delete key limit');
            }

            $this->setData([
                'dataLimit' => []
            ]);
        } else {
            throw new OutlineKeyException('Failed delete limit for key. Need load data key');
        }
    }

    /**
     * @throws OutlineKeyException
     * @throws OutlineApiException
     */
    public function create(string $name, int $limit = 0): OutlineKey
    {
        $create = $this->api->createNewAccessKey();

        if (!empty($create)) {
            $this->setData($create);

            $setName = $this->api->renameAccessKey($create['id'], $name);

            if ($setName) {
                $this->setData(['name' => $name]);

                if ($limit > 0) {
                    $setLimit = $this->api->setDataLimitForAccessKeys($create['id'], $limit);

                    if ($setLimit) {
                        $this->setData([
                            'dataLimit' => [
                                'bytes' => $limit
                            ]
                        ]);
                    } else {
                        throw new OutlineKeyException('Error set limit key');
                    }
                }
            } else {
                throw new OutlineKeyException('Error set key name');
            }
        } else {
            throw new OutlineKeyException('Error create key');
        }

        return $this;
    }

    /**
     * @throws OutlineKeyException
     * @throws OutlineApiException
     */
    public function delete(): bool
    {
        if ($this->api->deleteAccessKey($this->getId())) {
            return true;
        } else {
            throw new OutlineKeyException('Error delete key id=' . $this->getId());
        }
    }
}
