<?php

function send_data($method = 'GET', $url, $data = [], $http_headers = [])
{
    $headers = [];

    if ($method == 'GET') {
        $url .= '?' . http_build_query($data);
    }

    $ch = curl_init($url);
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_headers);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_UNRESTRICTED_AUTH, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
    curl_setopt($ch, CURLOPT_HEADERFUNCTION,
        function($curl, $header) use (&$headers)
        {
            $len = strlen($header);
            $header = explode(':', $header, 2);
            if (count($header) < 2)
                return $len;

            $name = strtolower(trim($header[0]));
            if (!array_key_exists($name, $headers))
                $headers[$name] = [trim($header[1])];
            else
                $headers[$name][] = trim($header[1]);

            return $len;
        }
    );

    if ($method == 'POST') {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }

    $content = curl_exec($ch);
    curl_close($ch);

    return [$headers, $content];
}