<?php
/**
 * Class MCAuth
 *
 * @description Intergrate Minecraft in your PHP projects.
 * @author Mattia Basone (mattia.basone@gmail.com)
 * @package MCAuth
 * @version 1.3
 * @copyright 2013-2015 Mattia Basone
 * @link https://github.com/mattiabasone/MCAuth
 */
class MCAuth {


    const CLIENT_TOKEN  = '808772fc24bc4d92ba2dc48bfecb375f';           // Client token for authentication
    const AUTH_URL      = 'https://authserver.mojang.com/authenticate'; // Mojang authentication server URL
    const PROFILE_URL   = 'https://api.mojang.com/users/profiles/minecraft/';     // Profile page
    const HASPAID_URL   = 'https://www.minecraft.net/haspaid.jsp?user='; // Old but gold, check if user is premium
    const USER_AGENT    = 'MCAuth 1.3 (https://github.com/mattiabasone/MCAuth)'; // User Agent used for requests

    public $autherr, $account = array();
    private $curlresp, $curlinfo, $curlerror;

    /**
     * Generic function for cURL requests
     *
     * @access private
     * @param string $url
     * @return bool
     */
    private function curl_request($url) {
        $request = curl_init();
        curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($request, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($request, CURLOPT_USERAGENT, self::USER_AGENT);
        curl_setopt($request, CURLOPT_URL, $url);
        $response = curl_exec($request);
        $this->curlinfo = curl_getinfo($request);
        $this->curlerror = curl_error($request);
        $this->curlresp = (string) $response;
        curl_close($request);
        if ($this->curlinfo['http_code'] == '200') {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Execute a POST request with JSON data
     *
     * @access private
     * @param $url
     * @param $array
     * @return bool
     */
    private function curl_json($url, $array) {
        $request = curl_init();
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($request, CURLOPT_USERAGENT, self::USER_AGENT);
        curl_setopt($request, CURLOPT_HTTPHEADER , array('Content-Type: application/json'));
        curl_setopt($request, CURLOPT_POST, TRUE);
        curl_setopt($request, CURLOPT_POSTFIELDS, json_encode($array));
        curl_setopt($request, CURLOPT_URL, $url);
        $response = curl_exec($request);
        $this->curlinfo = curl_getinfo($request);
        $this->curlerror = curl_error($request);
        $this->curlresp = json_decode($response);
        curl_close($request);
        if ($this->curlinfo['http_code'] == '200') {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Allowed characters for username
     *
     * @access private
     * @param $username
     * @return bool
     */
    private function valid_username($username) {
        if (preg_match('#[^a-zA-Z0-9_]+#', $username)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Check if a string is an email address
     *
     * @access private
     * @param $email
     * @return bool
     */
    public function valid_email($email) {
        if ( preg_match('#^[a-zA-Z0-9\.\_\%\+\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,8}$#', $email) == 1 ) {
            return false;
        }
        return true;
    }

    /**
     * Check if username is premium
     *
     * @access public
     * @param $username
     * @return bool
     */
    public function check_pemium($username) {
        if ($this->curl_request(self::HASPAID_URL.$username)) {
            if ($this->curlresp == 'true') {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Authentication with given credentials
     *
     * @access public
     * @param $username
     * @param $password
     * @return bool
     */
    public function authenticate($username, $password) {
        // json array for POST authentication
        $json = array();
        $json['agent']['name'] = 'Minecraft';
        $json['agent']['version'] = 1;
        $json['username'] = $username;
        $json['password'] = $password;
        $json['clientToken'] = self::CLIENT_TOKEN;
        if ($this->curl_json(self::AUTH_URL, $json)) {
            if (!isset($this->curlresp->error) AND isset($this->curlresp->selectedProfile->name)) {
                $this->account['id'] = $this->curlresp->selectedProfile->id;
                $this->account['username'] = $this->curlresp->selectedProfile->name;
                $this->account['token'] = $this->curlresp->accessToken;
                $this->autherr = 'OK';
                return TRUE;
            } else {
                $this->autherr = $this->curlresp->errorMessage;
            }
        } else {
            if (isset($this->curlresp->error)) {
                $this->autherr = $this->curlresp->errorMessage;
            } else {
                if (isset($this->curlerror)) {
                    $this->autherr = $this->curlerror;
                } else {
                    $this->autherr = 'Server unreacheable';
                }
            }
        }
        return FALSE;
    }

    /**
     * Utility: Get correct username and minecraft id from username (NOT email, case insensitive)
     *
     * @access public
     * @param $username
     * @return bool
     */
    public function get_user_info($username) {
        if ($this->valid_username($username)) {
            if ($this->curl_request(self::PROFILE_URL.urlencode($username))) {
                $response = json_decode($this->curlresp, true);
                if (isset($response['id']) && isset($response['name'])) {
                    $this->account['id'] = $response['id'];
                    $this->account['username'] = $response['username'];
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Utility: Convert username to UUID
     *
     * @access public
     * @param $username
     * @return bool
     */
    public function username2uuid($username) {
        if ($this->get_user_info($username)) {
            return $this->account['id'];
        }
        return false;
    }
}