<?php
$url = 'https://images.unsplash.com/photo-1605897472359-8dd3632f7489?auto=format&fit=crop&w=600&q=80';
$savePath = __DIR__ . '/debug_charlie.jpg';

$ch = curl_init($url);
$fp = fopen($savePath, 'wb');

curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_FAILONERROR, true); // Fail on 4xx/5xx

curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    echo 'HTTP Code: ' . curl_getinfo($ch, CURLINFO_HTTP_CODE) . "\n";
    echo 'Size: ' . filesize($savePath) . " bytes\n";
}

curl_close($ch);
fclose($fp);
