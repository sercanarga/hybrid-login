<?php
class github_login {
    private $config = [
        'access_token' => 'https://github.com/login/oauth/access_token',
        'authorize' => 'https://github.com/login/oauth/authorize?',
    ];
    static $token_url = NULL, $user_info = NULL;

    function random($length = 10) {
        return substr(str_shuffle(str_repeat(
            $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            ceil($length / strlen($chars))
        )), 1, $length);
    }

    function __construct($k, $s, $c) {
        if (isset($_GET['code']) && isset($_GET['state'])) {
            $post_field = [
                'client_id' => $k,
                'client_secret' => $s,
                'code' => $_GET['code']
            ];
            $post_field = http_build_query($post_field);

            $curl_options = [
                CURLOPT_URL => $this->config['access_token'],
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $post_field
            ];

            $result = oauth::_curl($curl_options)['exec'];

            if (@isset($result['access_token'])) {
                github_login::user_info($result);
            } else {
                throw new Exception(print_r($result, true));
            }
        } else {
            $keys = [
                'client_id' => $k,
                'redirect_uri' => $c,
                'response_type' => 'code',
                'scope' => 'user',
                'state' => $this->random()
            ];
            $keys = http_build_query($keys);

            github_login::$token_url = $this->config['authorize'] . $keys;
        }
    }

    function user_info($code) {
        $result = 'https://api.github.com/user?'.http_build_query($code);
        $curl_options = [
            CURLOPT_URL => $result,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT']
        ];
        $result = oauth::_curl($curl_options, 'json')['exec'];
        github_login::$user_info = $result;
    }
}

?>