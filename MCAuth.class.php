<?php

/*
 * class: MCAuth
 * description: Intergrate Minecraft within your own projects.
 * author: Mattia Basone
 * version: 1.0
 * info/support: mattia.basone@gmail.com
 */
class MCAuth
{

    public $account = array();
    private $autherr;

    public function authenticate($username, $password) {
        // json array for POST authentication
        $json = array();
        $json['agent']['name'] = 'Minecraft';
        $json['agent']['version'] = 1;
        $json['username'] = $username;
        $json['password'] = $password;
        $request = curl_init();
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_HTTPHEADER , array('Content-Type: application/json'));
        curl_setopt($request, CURLOPT_POST, TRUE);
        curl_setopt($request, CURLOPT_POSTFIELDS, json_encode($json));
        curl_setopt($request, CURLOPT_URL, 'https://authserver.mojang.com/authenticate');
        $response = curl_exec($request);
        curl_close($request);
        $rjson = json_decode($response);
       if (!isset($rjson->error)) {
           $this->account['id'] = $rjson->selectedProfile->id;
           $this->account['username'] = $rjson->selectedProfile->name;
           $this->account['token'] = $rjson->accessToken;
           return TRUE;
       } else {
           $this->autherr = $rjson->errorMessage;
           return FALSE;
       }
    }
}
