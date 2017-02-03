<?php
namespace MCAuth;

use MCAuth\HttpClients\MojangClient;
use MCAuth\Objects\Account;
use MCAuth\Exceptions\InvalidFormatException;

class Api {

    /**
     * Client
     *
     * @var MojangClient
     */
    private $MojangClient;

    /**
     * Api constructor.
     */
    public function __construct() {
        $this->MojangClient = new MojangClient();
    }

    /**
     * Allowed characters for username
     *
     * @access private
     * @param $username
     * @return bool
     */
    private function isValidUsername(string $username) : bool {
        return (preg_match('/[^a-zA-Z0-9_]+/', $username) === 0);
    }

    /**
     * Check if a string is an email address
     *
     * @access private
     * @param $email
     * @return bool
     */
    private function isValidEmail(string $email) : bool {
        return (preg_match('/^[a-zA-Z0-9\.\_\%\+\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,8}$/', $email) === 1 );
    }

    /**
     * Check if given string is a valid UUID
     *
     * @param string $uuid
     * @return bool
     */
    private function isValidUuid(string $uuid) : bool {
        return (preg_match("/[^a-fA-F0-9]+/", $uuid) === 0);
    }

    /**
     * Authentication with given credentials
     *
     * @access public
     * @param $username
     * @param $password
     * @return Account
     * @throws InvalidFormatException
     */
    public function sendAuth(string $username, string $password) : Account {
        if ($this->isValidEmail($username) OR $this->isValidUsername($username)) {
            return $this->MojangClient->sendAuthRequest($username, $password);
        }
        throw new InvalidFormatException("Invalid email", 1);
    }


    /**
     * Utility: Convert username to UUID
     *
     * @access public
     * @param $username
     * @return string
     * @throws InvalidFormatException
     */
    public function usernameToUuid(string $username) : string {
        if ($this->IsValidUsername($username)) {
            $account = $this->MojangClient->sendUsernameInfoRequest($username);
            return $account->uuid;
        }
        throw new InvalidFormatException("Invalid username", 2);
    }

    /**
     * Convert UUID to Username
     *
     * @param string $uuid
     * @return string
     * @throws InvalidFormatException
     */
    public function uuidToUsername(string $uuid) : string {
        $uuid = strtolower($uuid);
        $uuid = str_replace("-", "", $uuid);
        if ($this->isValidUuid($uuid)) {
            $account = $this->MojangClient->sendUuidInfoRequest($uuid);
            return $account->username;
        }
        throw new InvalidFormatException("Invalid UUID", 3);
    }
}