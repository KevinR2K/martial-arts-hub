<?php

require_once "config/api.php";


// Get search term
$query = $_GET["search"] ?? "";

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
    if (isset($data["data"]["fighters"])) {
        $fighters = $data["data"]["fighters"];
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Fighters - Martial Arts Hub</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>


<!-- NAVBAR -->

<header class="fighter-page-header">

    <nav class="fighter-page-navbar">

        <a href="index.php" class="fighter-page-logo">
            Martial Arts Hub
        </a>

        <ul class="fighter-page-nav">

            <li>
                <a href="index.php">Home</a>
            </li>

            <li>
                <a href="categories.php">Categories</a>
            </li>

            <li>
                <a href="fighters.php">Fighters</a>
            </li>

            <li>
                <a href="index.php#about">About</a>
            </li>

        </ul>

    </nav>

</header>


<main class="fighters-page">


    <!-- TITLE -->

    <div class="fighters-page-title">

        <p>FIGHTER DATABASE</p>

        <h1>Find UFC Fighters</h1>

        <span>
            Search for fighters and explore their records and profiles.
        </span>

    </div>


    <!-- SEARCH -->

    <form method="GET" class="fighters-search-form">

        <input
            type="text"
            name="search"
            placeholder="Enter fighter name..."
            value="<?php echo htmlspecialchars($query); ?>"
            required
        >

        <button type="submit">
            Search
        </button>

    </form>



    <?php if (!empty($query)): ?>


        <div class="fighters-results-header">

            <h2>Search Results</h2>

            <p>
                Results for
                <strong>
                    "<?php echo htmlspecialchars($query); ?>"
                </strong>
            </p>

        </div>


        <?php if (!empty($fighters)): ?>


            <div class="fighters-results-grid">


                <?php foreach ($fighters as $fighter): ?>


                    <article class="fighter-result-card">


                        <div class="fighter-result-image">

                            <?php if (!empty($fighter["imageUrl"])): ?>

                                <img
                                    src="<?php echo htmlspecialchars($fighter["imageUrl"]); ?>"
                                    alt="<?php echo htmlspecialchars($fighter["name"]); ?>"
                                >

                            <?php else: ?>

                                <div class="fighter-no-image">
                                    No Image
                                </div>

                            <?php endif; ?>

                        </div>


                        <div class="fighter-result-content">


                            <span class="fighter-division">

                                <?php echo htmlspecialchars(
                                    $fighter["division"] ?? "UFC Fighter"
                                ); ?>

                            </span>


                            <h3>

                                <?php echo htmlspecialchars(
                                    $fighter["name"]
                                ); ?>

                            </h3>


                            <p>

                                <strong>Record:</strong>

                                <?php echo htmlspecialchars(
                                    $fighter["recordText"] ?? "N/A"
                                ); ?>

                            </p>


                            <a
                                href="fighter.php?slug=<?php echo urlencode($fighter["slug"]); ?>"
                                class="fighter-profile-btn"
                            >
                                View Profile →
                            </a>


                        </div>

                    </article>


                <?php endforeach; ?>


            </div>


        <?php else: ?>


            <div class="fighters-empty">

                <h3>No fighters found</h3>

                <p>
                    Try searching with another fighter name.
                </p>

            </div>


        <?php endif; ?>


    <?php endif; ?>


</main>


</body>

</html>