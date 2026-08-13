<?php

$url = "https://ok.surf/api/v1/news-section";

$data = [
    "sections" => ["Sports"]
];

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_POST, true);

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "accept: application/json",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);

if ($response === false) {

    die("API Error: " . curl_error($ch));

}

curl_close($ch);

$result = json_decode($response, true);

echo "<pre>";

print_r($result);

echo "</pre>";

?>