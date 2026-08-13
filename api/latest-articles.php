<?php

$apiKey = "a135ec0102f94414b9260e4503ceef27";

$url = "https://newsapi.org/v2/everything?" . http_build_query([
    "q" => "UFC OR MMA",
    "language" => "en",
    "sortBy" => "publishedAt",
    "pageSize" => 6
]);

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-Api-Key: " . $apiKey,
    "User-Agent: MartialArtsHub/1.0"
]);

$response = curl_exec($ch);

if ($response === false) {
    die("News API Error: " . curl_error($ch));
}

curl_close($ch);

$newsData = json_decode($response, true);

$newsArticles = $newsData["articles"] ?? [];

echo "<pre>";
print_r($newsArticles);
echo "</pre>";

?>