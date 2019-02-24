<?php

$url = 'https://mail.ru/';

$code = file_get_contents($url);

echo '<ol>';

$regexp = '/<img\s+src="(([^"]+)(.)(jpeg|png|jpg|bmp|gif|base64|data:image))"/is';
$matches = getImages($regexp, $code);

foreach ($matches[1] as $match) {
    echo '<li>' . $match . '</li>';
}

$regexp = '/url\((\"|\'|)((.*\.(jpeg|png|jpg|bmp|gif|base64|data:image))(|\"|\'|))\)/ui';
$matches = getImages($regexp, $code);
foreach ($matches[3] as $match) {
    echo '<li>' . $match . '</li>';
}

echo '</ol>';

function getImages($regexp, $code)
{
    preg_match_all($regexp, $code, $matches);

    return $matches;
}