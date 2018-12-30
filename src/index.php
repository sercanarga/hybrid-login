<?php

include("oauth.php");

$config = [
    'twitter'   => [
        'enabled' => true,
        'callback' => '',
        'keys' => [
            'key'  => '',
            'secret' => ''
        ]
    ],
];

try {
    $oauth = new oauth($config);
} catch (\Exception $e) {
    echo '<b>HATA:</b> '.$e->getMessage();
}

?>