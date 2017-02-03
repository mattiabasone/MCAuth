<?php
namespace MCAuth\Objects;

/**
 * Class Account
 * @package MCAuth\Objects
 */
class Account {

    /**
     * Username
     *
     * @var string
     */
    public $username = '';

    /**
     * UUID
     *
     * @var string
     */
    public $uuid = '';

    /**
     * Access Token
     *
     * @var string
     */
    public $accessToken = '';

    /**
     * Account constructor.
     * @param array $rawData
     * @param string $dataType auth|profile
     *
     * @throws \Exception
     */
    public function __construct(array $rawData, $dataType = 'none') {
        switch ($dataType) {
            case 'auth':
                if (isset($rawData['selectedProfile']['name'])) {
                    $this->uuid = $rawData['selectedProfile']['id'];
                    $this->username = $rawData['selectedProfile']['name'];
                    $this->accessToken = $rawData['accessToken'];
                }
                break;
            case 'profile':
                $this->uuid = $rawData['id'];
                $this->username = $rawData['name'];
                $this->accessToken = '';
                break;
            default:
                throw new \Exception("Invalid data type");
        }
    }
}