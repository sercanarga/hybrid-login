<?php
class twitter_oauth {
    private $config = [
        'request_token' => 'https://twitter.com/oauth/request_token',
        'access_token' => 'https://api.twitter.com/oauth/access_token',
    ];
    static $token_url = NULL;

    function __construct($k, $s, $c) {
        if (!isset($_GET['oauth_token']) || !isset($_GET['oauth_verifier'])) {
            $oauth_hash = [
                'oauth_callback' => $c,
                'oauth_consumer_key' => $k,
                'oauth_nonce' => time(),
                'oauth_timestamp' => time(),
                'oauth_version' => '1.0',
                'oauth_signature_method' => 'HMAC-SHA1'
            ];
            uksort($oauth_hash, 'strcmp');
            $param_pairs =
                implode('&', array_map(
                    function ($v, $k) {
                        return sprintf('%s=%s', $k, $v);
                    }, $oauth_hash, array_keys($oauth_hash)
                ));

            $base = [
                'POST',
                rawurlencode($this->config['request_token']),
                rawurlencode($param_pairs)
            ];
            $base = implode('&', $base);

            $oauth_hash['oauth_signature'] = rawurlencode(base64_encode(hash_hmac('sha1', $base, $s.'&', true)));
            uksort($oauth_hash, 'strcmp');

            $param_header =
                implode(',', array_map(
                    function ($v, $k) {
                        return sprintf('%s=%s', $k, $v);
                    }, $oauth_hash, array_keys($oauth_hash)
                ));

            $curl_options = [
                CURLOPT_URL => $this->config['request_token'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => ['Authorization: OAuth ' . $param_header]
            ];
            $token = @oauth::_curl($curl_options)['exec']['oauth_token'];
            if ($token != NULL) {
                twitter_oauth::$token_url = 'https://api.twitter.com/oauth/authenticate?oauth_token='.$token;
            } else {
                throw new Exception(print_r(oauth::_curl($curl_options, 'json')['exec'], true));
            }
        } else {
            $oauth_hash = [
                'oauth_consumer_key' => $k,
                'oauth_nonce' => time(),
                'oauth_timestamp' => time(),
                'oauth_version' => '1.0',
                'oauth_signature_method' => 'HMAC-SHA1',
                'oauth_token' => $_GET['oauth_token']
            ];
            uksort($oauth_hash, 'strcmp');

            $param_pairs =
                implode('&', array_map(
                    function ($v, $k) {
                        return sprintf('%s=%s', $k, $v);
                    }, $oauth_hash, array_keys($oauth_hash)
                ));

            $base = [
                'POST',
                rawurlencode($this->config['request_token']),
                rawurlencode($param_pairs)
            ];
            $base = implode('&', $base);

            $oauth_signature = rawurlencode(base64_encode(hash_hmac('sha1', $base, $s.'&', true)));

            $oauth_hash['oauth_signature'] = $oauth_signature;
            uksort($oauth_hash, 'strcmp');

            $param_header =
                implode(',', array_map(
                    function ($v, $k) {
                        return sprintf('%s=%s', $k, $v);
                    }, $oauth_hash, array_keys($oauth_hash)
                ));

            $post_field = "oauth_verifier={$_GET['oauth_verifier']}";

            $c_header = [
                'Authorization:OAuth' => $param_header,
                'Content-Length:' => strlen($post_field),
                'Content-Type:' => 'application/x-www-form-urlencoded'
            ];

            $c_header =
            implode(',', array_map(
                function ($v, $k) {
                    return sprintf('%s %s', $k, $v);
                }, $c_header, array_keys($c_header)
            ));

            $curl_options = [
                CURLOPT_URL => $this->config['access_token'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $post_field,
                CURLOPT_HTTPHEADER => [$c_header]
            ];
            $result = oauth::_curl($curl_options)['exec'];

            new twitter_login($k, $s, @$result['oauth_token'], @$result['oauth_token_secret']);
        }
    }
}

class twitter_login {
    private $config = [
        'api_url' => 'https://api.twitter.com/1.1/',
        'verify_credentials' => 'account/verify_credentials.json',
    ];
    static $user_info = NULL;

    function __construct($k, $s, $u_k, $u_s) {
        $oauth_hash = [
            'oauth_consumer_key' => $k,
            'oauth_nonce' => time(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_token' => $u_k,
            'oauth_version' => '1.0'
        ];
        $oauth_hash = http_build_query($oauth_hash);

        $base = [
            'GET',
            rawurlencode($this->config['api_url'].$this->config['verify_credentials']),
            rawurlencode($oauth_hash)
        ];
        $base = implode('&', $base);

        $key = [
            rawurlencode($s),
            rawurlencode($u_s)
        ];
        $key = implode('&', $key);

        $signature = rawurlencode(base64_encode(hash_hmac('sha1', $base, $key, true)));
        $oauth_header = [
            'oauth_consumer_key' => $k,
            'oauth_nonce' => time(),
            'oauth_signature' => $signature,
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_token' => $u_k,
            'oauth_version' => '1.0'
        ];
        $oauth_header =
                    implode(',', array_map(
                        function ($v, $k) {
                            return sprintf('%s="%s"', $k, $v);
                        },
                        $oauth_header,
                        array_keys($oauth_header)
                    ));
        $c_header = array("Authorization: Oauth {$oauth_header}", 'Expect:');

        $curl_options = [
            CURLOPT_URL => $this->config['api_url'].$this->config['verify_credentials'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => $c_header,
            CURLOPT_HEADER => false
        ];
        $result = oauth::_curl($curl_options, 'json')['exec'];

        if (@isset($result['id'])) {
            twitter_login::$user_info = $result;
        } else {
            throw new Exception(print_r($result, true));
        }
    }
}

?>