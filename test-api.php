<?php

require_once "config/api.php";


// Search for a fighter
$query = "islam";

$url = $cito_base_url . "/ufc/search?q=" . urlencode($query);


$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "x-api-key: " . $cito_api_key
]);


$response = curl_exec($ch);


if ($response === false) {

    die("API Error: " . curl_error($ch));

}


curl_close($ch);


// Convert JSON response into PHP array
$data = json_decode($response, true);


// Display the API response
echo "<pre>";

print_r($data);

echo "</pre>";

?>