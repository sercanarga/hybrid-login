<?php

include("oauth.php");
include("./modules/twitter_module.php");
include("./modules/instagram_module.php");
include("./modules/github_module.php");
include("./modules/google_module.php");

$config = [
    'twitter'   => [
        'enabled' => true,
        'callback_url' => '',
        'keys' => [
            'consumer_key'  => '',
            'consumer_key_secret' => ''
        ]
    ],
    'instagram' => [
        'enabled' => true,
        'callback_url' => '',
        'keys' => [
            'client_id' => '',
            'client_secret' => '',
        ]
    ],
    'github' => [
        'enabled' => true,
        'callback_url' => '',
        'keys' => [
            'client_id' => '',
            'client_secret' => '',
        ]
    ],
    'google' => [
        'enabled' => true,
        'callback_url' => '',
        'keys' => [
            'client_id' => '',
            'client_secret' => '',
        ]
    ]
];

try {
    $oauth = new oauth($config);
    echo '<a href="'.twitter_oauth::$token_url.'">'.twitter_oauth::$token_url.'</a>';
    echo '<br>';
    echo '<a href="'.instagram_login::$token_url.'">'.instagram_login::$token_url.'</a>';
    echo '<br>';
    echo '<a href="'.github_login::$token_url.'">'.github_login::$token_url.'</a>';
    echo '<br>';
    echo '<a href="'.google_login::$token_url.'">'.google_login::$token_url.'</a>';

    echo '<pre>';
    print_r(twitter_login::$user_info);
    print_r(instagram_login::$user_info);
    print_r(github_login::$user_info);
    print_r(google_login::$user_info);
    echo '</pre>';
} catch (\Exception $e) {
    echo '<pre>';
    echo $e->getMessage();
    echo '</pre>';
}

?>