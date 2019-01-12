<?php

class instagram_login {
    private $config = [
        'access_token' => 'https://api.instagram.com/oauth/access_token',
        'authorize' => 'https://api.instagram.com/oauth/authorize/?',
    ];
    static $token_url = NULL, $user_info = NULL;

    function __construct($k, $s, $c) {
        if (isset($_GET['code'])) {
            $post_field = [
                'client_id' => $k,
                'redirect_uri' => $c,
                'client_secret' => $s,
                'code' => $_GET['code'],
                'grant_type' => 'authorization_code'
            ];
            $post_field = http_build_query($post_field);

            $curl_options = [
                CURLOPT_URL => $this->config['access_token'],
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $post_field
            ];
            $result = oauth::_curl($curl_options, 'json')['exec'];

            if (@isset($result['access_token'])) {
                instagram_login::$user_info = $result;
            } else {
                throw new Exception(print_r($result, true));
            }
        } else {
            $keys = [
                'client_id' => $k,
                'redirect_uri' => $c,
                'response_type' => 'code',
                'scope' => 'basic'
            ];
            $keys = http_build_query($keys);
            instagram_login::$token_url = $this->config['authorize'] . $keys;
        }
    }
}

?>