<?php

$apiKey = "a135ec0102f94414b9260e4503ceef27";

$url = "https://newsapi.org/v2/everything?" . http_build_query([
    "q" => "UFC OR MMA",
    "language" => "en",
    "sortBy" => "publishedAt",
    "pageSize" => 10
]);

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-Api-Key: " . $apiKey,
    "User-Agent: MartialArtsHub/1.0"
]);

$response = curl_exec($ch);

if ($response === false) {
    die("API Error: " . curl_error($ch));
}

curl_close($ch);

$data = json_decode($response, true);

echo "<pre>";
print_r($data);
echo "</pre>";

?>