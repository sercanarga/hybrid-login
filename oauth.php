<?php

class oauth {
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
            if(@$c['instagram']['enabled'] == 1) {
                if (@$c['instagram']['keys']['client_id'] != null && @$c['instagram']['keys']['client_secret'] != null) {
                    new instagram_oauth(
                        $c['instagram']['keys']['client_id'],
                        $c['instagram']['keys']['client_secret'],
                        @$c['instagram']['callback_url']
                    );
                } else {
                    throw new Exception('Instagram API bilgileri eksik!');
                }
            }
        }
    }
}

?>