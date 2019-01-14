<?php

class google_login {
    private $config = [
        'access_token' => 'https://accounts.google.com/o/oauth2/token',
        'user_info' => 'https://www.googleapis.com/plus/v1/people/me',
        'oauth_url' => 'https://accounts.google.com/o/oauth2/v2/auth?'
    ];
    static $token_url = NULL, $user_info = NULL;

    function __construct($k, $s, $c) {
        if (isset($_GET['code']) && isset($_GET['scope'])) {
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
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_POSTFIELDS => $post_field
            ];
            $result = oauth::_curl($curl_options, 'json')['exec'];

            if (@isset($result['access_token'])) {
                google_login::user_info($result['access_token']);
            } else {
                throw new Exception(print_r($result, true));
            }
        } else {
                $oauth_url = [
                    'scope' => 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/plus.me',
                    'redirect_uri' => $c,
                    'response_type' => 'code',
                    'client_id' => $k,
                    'access_type' => 'online'
                ];
                google_login::$token_url = $this->config['oauth_url'] . http_build_query($oauth_url);
        }
    }

    function user_info($access_token) {
        $curl_options = [
            CURLOPT_URL => $this->config['user_info'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer '. $access_token]
        ];
        google_login::$user_info = oauth::_curl($curl_options, 'json')['exec'];
    }
}

?>