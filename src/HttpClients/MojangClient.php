<?php
namespace MCAuth\HttpClients;

use GuzzleHttp;
use MCAuth\Exceptions\ResponseException;
use MCAuth\Objects\Account;

/**
 * Class MojangClient
 * @package MCAuth\HttpClient
 */
class MojangClient {

    /**
     * Client token for authentication
     */
    const CLIENT_TOKEN  = '909772fc24bc4d92bf2dc48bfecb375f';

    /**
     * User Agent used for requests
     */
    const USER_AGENT = 'MCAuth 2.0 (https://github.com/mattiabasone/MCAuth)';

    /**
     *  Mojang authentication server URL
     */
    const AUTH_URL = 'https://authserver.mojang.com/authenticate';

    /**
     * Profile page
     */
    const PROFILE_USERNAME_URL = 'https://api.mojang.com/users/profiles/minecraft/';

    /**
     * UUID to Username
     */
    const PROFILE_UUID_URL = 'https://sessionserver.mojang.com/session/minecraft/profile/';

    /**
     * HTTP Client for requests
     *
     * @var GuzzleHttp\Client
     */
    private $HttpClient;

    /**
     * Method for Guzzle
     *
     * @var string
     */
    private $method = 'GET';

    /**
     * URL for Guzzle
     *
     * @var string
     */
    private $url = '';

    /**
     * Data array for Guzzle
     *
     * @var array
     */
    private $data = [];

    /**
     * Last API Response
     *
     * @var
     */
    private $lastResponse;

    /**
     * Last Error
     *
     * @var string
     */
    private $lastError = "";

    /**
     * Last error code
     *
     * @var int
     */
    private $lastErrorCode = 0;


    /**
     * MojangClient constructor.
     */
    public function __construct() {
        $this->HttpClient = new GuzzleHttp\Client(
            [
                'headers' => [
                    'User-Agent' => self::USER_AGENT
                ]
            ]
        );
    }

    /**
     * Set HTTP Method
     *
     * @param string $method
     */
    private function setMethod($method = '') {
        $this->method = $method;
    }

    /**
     * Set URL
     *
     * @param string $url
     */
    private function setURL($url = '') {
        $this->url = $url;
    }

    /**
     * Set data
     *
     * @param array $data
     */
    private function setData(array $data) {
        $this->data = $data;
    }

    /**
     * Last response from API
     *
     * @return mixed
     */
    public function getLastResponse() {
        return $this->lastResponse;
    }

    /**
     * Send new request
     *
     * @return bool
     */
    private function sendRequest() : bool {
        try {
            $response = $this->HttpClient->request($this->method, $this->url, $this->data);
            $this->lastResponse = GuzzleHttp\json_decode($response->getBody()->getContents(), TRUE);
            $this->lastErrorCode = 0;
            $this->lastError = "";
            return true;
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $this->lastResponse = GuzzleHttp\json_decode($e->getResponse()->getBody()->getContents(), TRUE);
            $this->lastErrorCode = $e->getResponse()->getStatusCode();
            $this->lastError = "Error";
            if (isset($this->lastResponse['errorMessage'])) {
                $this->lastError .= ": ".$this->lastResponse['errorMessage'];
            }
            return false;

        }
    }

    /**
     * Send auth request
     *
     * @param string $username
     * @param string $password
     * @return Account
     * @throws ResponseException
     */
    public function sendAuthRequest(string $username, string $password) : Account {
        $data = [
            'agent' => [
                'name' => 'Minecraft',
                'version' => 1
            ],
            'username' => $username,
            'password' => $password,
            'clientToken' => self::CLIENT_TOKEN
        ];
        $this->setMethod('POST');
        $this->setURL(self::AUTH_URL);
        $this->setData(['json' => $data]);
        if ($this->sendRequest()) {
            return new Account($this->lastResponse, 'auth');
        }
        throw new ResponseException($this->lastError, $this->lastErrorCode);
    }

    /**
     * Account info from username
     *
     * @param string $username
     * @return Account
     * @throws ResponseException
     */
    public function sendUsernameInfoRequest(string $username) : Account {
        $this->setMethod('GET');
        $this->setURL(self::PROFILE_USERNAME_URL.$username);
        if ($this->sendRequest()) {
            return new Account($this->lastResponse, 'profile');
        }
        throw new ResponseException($this->lastError, $this->lastErrorCode);
    }

    /**
     * Account info from UUID
     *
     * @param string $uuid
     * @return Account
     * @throws ResponseException
     */
    public function sendUuidInfoRequest(string $uuid) : Account {
        $this->setMethod('GET');
        $this->setURL(self::PROFILE_UUID_URL.$uuid);
        if ($this->sendRequest()) {
            return new Account($this->lastResponse, 'profile');
        }
        throw new ResponseException($this->lastError, $this->lastErrorCode);
    }
}