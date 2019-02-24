<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

require_once 'function.php';
$config = require_once 'config.php';

header('Content-Type: text/html; charset=utf-8');
header('Host: auth.mail.ru');

$url_login = 'https://auth.mail.ru/cgi-bin/auth';
$url_threads = 'https://e.mail.ru/api/v1/threads';
$url_message = 'https://e.mail.ru/messages/inbox/?back=1';

$post_data = [
    'Login' => $config['login'],
    'Password' => $config['password'],
    'Domain' => $config['domain']
];

list($headers, $content) = send_data('POST', $url_login, $post_data);

if (empty($headers)) {
    echo 'Пользователь не авторизован';
    return false;
}

$cookies_list = '';
if (!empty($headers['set-cookie'])) {
    foreach ($headers['set-cookie'] as $cookie_line) {
        $cookies_list .= $cookie_line . ';';
    }
}

$data = [
    'ajax_call' => 1,
    'x-email' => $config['email'],
    'email' => $config['email'],
    'offset' => 0,
    'htmlencoded' => 'false',
    'api' => 1,
    'token' => $config['token'],
    'folder' => 0
];

list($result_headers, $result) = send_data('GET', $url_threads . '?' . http_build_query($data), [], [
    'User-Agent: ' . $config['agent'],
    'Content-Type: application/json; charset=utf-8',
    'Cookie: ' . $cookies_list
]);

if (empty($result)) {
    echo 'Нет данных';
    return false;
}

$result = json_decode($result, true);
$subjects = [];

if (empty($result['body']) || !is_array($result['body'])) {
    echo 'Нет данных';
    return false;
}

echo '<ol>';

foreach ($result['body'] as $body) {
    echo '<li>' . $body['subjects'] . '</li>';
}

echo '</ol>';