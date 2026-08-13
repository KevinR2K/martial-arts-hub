<?php

require_once "config/api.php";


// Get search term
$query = $_GET['search'] ?? '';

$fighters = [];


// Only call the API when the user searches
if (!empty($query)) {

    $url = $cito_base_url . "/ufc/search?q=" . urlencode($query);

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "x-api-key: " . $cito_api_key
    ]);

    $response = curl_exec($ch);

    curl_close($ch);


    // Convert JSON response into PHP array
    $data = json_decode($response, true);


    // Get fighters from response
    if (isset($data['data']['fighters'])) {

        $fighters = $data['data']['fighters'];

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Fighters - Martial Arts Hub</title>

</head>


<body>

    <h1>Fighters</h1>


    <form method="GET">

        <input
            type="text"
            name="search"
            placeholder="Search fighter..."
            value="<?php echo htmlspecialchars($query); ?>"
        >

        <button type="submit">
            Search
        </button>

    </form>


    <?php if (!empty($query)): ?>

        <h2>Search Results</h2>


        <?php if (!empty($fighters)): ?>

            <?php foreach ($fighters as $fighter): ?>

                <div>

                    <?php if (!empty($fighter['imageUrl'])): ?>

    <img
        src="<?php echo htmlspecialchars($fighter['imageUrl']); ?>"
        alt="<?php echo htmlspecialchars($fighter['name']); ?>"
        width="200"
    >

<?php endif; ?>

<h3>
    <?php echo htmlspecialchars($fighter['name']); ?>
</h3>

<p>
    Division:
    <?php echo htmlspecialchars($fighter['division'] ?? 'N/A'); ?>
</p>

<p>
    Record:
    <?php echo htmlspecialchars($fighter['recordText'] ?? 'N/A'); ?>
</p>
<a href="fighter.php?slug=<?php echo urlencode($fighter['slug']); ?>">
    View Profile
</a>

                </div>

                <hr>

            <?php endforeach; ?>

        <?php else: ?>

            <p>No fighters found.</p>

        <?php endif; ?>


    <?php endif; ?>


</body>

</html>