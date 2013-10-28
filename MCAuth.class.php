<?php

/*
 * class: MCAuth
 * description: Intergrate Minecraft in your PHP projects.
 * author: Mattia Basone
 * version: 1.0
 * info/support: mattia.basone@gmail.com
 */
class MCAuth {
    
    const CLIENT_TOKEN = "808772fc24bc4d92ba2dc48bfecb375f";
    
    public $account = array();
    public $autherr;
    
    // Generic function for cURL requests
    private function curl_request($address) {
        $request = curl_init();
        curl_setopt($request, CURLOPT_HEADER, 0);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($request, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($request, CURLOPT_URL, $address);
        return curl_exec($request);
        curl_close($request);
    }
    
    // Check if username is premium
    public function check_premium($username) {
        return $this->curl_request('https://www.minecraft.net/haspaid.jsp?user='.$username);
    }
    
    public function authenticate($username, $password) {
        // json array for POST authentication
        $json = array();
        $json['agent']['name'] = 'Minecraft';
        $json['agent']['version'] = 1;
        $json['username'] = $username;
        $json['password'] = $password;
        $json['clientToken'] = self::CLIENT_TOKEN;
        $request = curl_init();
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_HTTPHEADER , array('Content-Type: application/json'));
        curl_setopt($request, CURLOPT_POST, TRUE);
        curl_setopt($request, CURLOPT_POSTFIELDS, json_encode($json));
        curl_setopt($request, CURLOPT_URL, 'https://authserver.mojang.com/authenticate');
        $response = curl_exec($request);
        $req_info = curl_getinfo($request);
        curl_close($request);
        $rjson = json_decode($response);
        if ($req_info['http_code'] == '200') {
            if (!isset($rjson->error) AND isset($rjson->selectedProfile->name)) {
                $this->account['id'] = $rjson->selectedProfile->id;
                $this->account['username'] = $rjson->selectedProfile->name;
                $this->account['token'] = $rjson->accessToken;
                $this->autherr = 'OK';
                return TRUE;
            } else {
                $this->autherr = $rjson->errorMessage;
                return FALSE;
            }
        } else {
            if (isset($req_info['http_code'])) {
                if (isset($rjson->error)) {
                    $this->autherr = $rjson->errorMessage;
                    return FALSE;
                } else {
                    $this->autherr = 'Server returned http code '.$req_info['http_code'];
                    return FALSE;
                }
            } else {
                $this->autherr = 'Server unreacheable';
                return FALSE;
            }
        }
    }
}