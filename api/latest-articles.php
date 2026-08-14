<?php
require_once __DIR__ . "/../config/api-config.php";

$url = "https://newsapi.org/v2/everything?" . http_build_query([
    "q" => '"UFC" OR "MMA" OR "Ultimate Fighting Championship"',
    "language" => "en",
    "sortBy" => "publishedAt",
    "pageSize" => 50
]);

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-Api-Key: " . $newsApiKey,
    "User-Agent: MartialArtsHub/1.0"
]);

$response = curl_exec($ch);

if ($response === false) {
    die("News API Error: " . curl_error($ch));
}

curl_close($ch);

$newsData = json_decode($response, true);

$newsArticles = $newsData["articles"] ?? [];

$filteredArticles = [];

foreach ($newsArticles as $article) {

    $text = strtolower(
        ($article["title"] ?? "") . " " .
        ($article["description"] ?? "")
    );

    if (
        strpos($text, "ufc") !== false ||
        strpos($text, "mma") !== false ||
        strpos($text, "mixed martial arts") !== false ||
        strpos($text, "ultimate fighting championship") !== false
    ) {
        $filteredArticles[] = $article;
    }
}

$newsArticles = array_slice($filteredArticles, 0, 6);


?>