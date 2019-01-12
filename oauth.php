<?php

class oauth {
    public function _curl($options, $encode = 'parse') {
        $ct = curl_init();
        $limit = [CURLOPT_CONNECTTIMEOUT => 30, CURLOPT_TIMEOUT => 30];
        array_merge($options, $limit);
        curl_setopt_array($ct, $options);
        $result = [
            'http_code' => curl_getinfo($ct, CURLINFO_HTTP_CODE)
        ];
        if ($encode == 'json') {
            $result['exec'] = json_decode(curl_exec($ct), true);
        } else {
            parse_str(curl_exec($ct), $result['exec']);
        }
        curl_close($ct);
        return $result;
    }
    function __construct($c) {
        if (@isset($c['twitter'])) {
            if (@$c['twitter']['enabled'] == 1) {
                if(@$c['twitter']['keys']['consumer_key'] != null && @$c['twitter']['keys']['consumer_key_secret'] != null) {
                    new twitter_oauth(
                        $c['twitter']['keys']['consumer_key'],
                        $c['twitter']['keys']['consumer_key_secret'],
                        @$c['twitter']['callback_url']
                    );
                } else {
                    throw new Exception('Twitter API bilgileri eksik!');
                }
            }
        }
        if (@isset($c['instagram'])) {
            if(@$c['instagram']['enabled'] == 1) {
                if (@$c['instagram']['keys']['client_id'] != null && @$c['instagram']['keys']['client_secret'] != null && @$c['instagram']['callback_url'] != null) {
                    new instagram_login(
                        $c['instagram']['keys']['client_id'],
                        $c['instagram']['keys']['client_secret'],
                        $c['instagram']['callback_url']
                    );
                } else {
                    throw new Exception('Instagram API bilgileri eksik!');
                }
            }
        }
    }
}

?>