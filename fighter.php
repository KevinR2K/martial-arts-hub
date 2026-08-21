<?php

session_start();

require_once "config/api.php";
require_once "config/database.php";


// Get fighter slug from URL
$slug = $_GET["slug"] ?? "";


// Make sure a slug was provided
if (empty($slug)) {
    die("Fighter not found.");
}


// Build API URL
$url = $cito_base_url . "/ufc/fighters/" . urlencode($slug);


// Call API
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


// Convert JSON to PHP array
$data = json_decode($response, true);


// Check API response
if (
    !isset($data["success"]) ||
    !$data["success"]
) {
    die("Unable to find fighter.");
}


// Get fighter information
$fighter = $data["data"];


// Check whether logged-in user follows fighter
$is_following = false;

if (isset($_SESSION["user_id"])) {

    $user_id = $_SESSION["user_id"];

    $check_sql = "SELECT id
                  FROM followed_fighters
                  WHERE user_id = ?
                  AND fighter_slug = ?";

    $check_stmt = $conn->prepare($check_sql);

    $check_stmt->bind_param(
        "is",
        $user_id,
        $fighter["slug"]
    );

    $check_stmt->execute();

    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $is_following = true;
    }

    $check_stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?php echo htmlspecialchars($fighter["name"] ?? "Fighter"); ?>
        - Martial Arts Hub
    </title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>


<!-- NAVBAR -->

<header class="fighter-profile-header">

    <nav class="fighter-profile-navbar">

        <a href="index.php" class="fighter-profile-logo">
            Martial Arts Hub
        </a>

        <ul class="fighter-profile-nav">

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

            <?php if (isset($_SESSION["user_id"])): ?>

                <li>
                    <a href="user/account.php">My Account</a>
                </li>

                <li>
                    <a href="auth/logout.php">Logout</a>
                </li>

            <?php else: ?>

                <li>
                    <a href="auth/login.php">Login</a>
                </li>

            <?php endif; ?>

        </ul>

    </nav>

</header>



<main class="fighter-profile-page">


    <a href="fighters.php" class="fighter-back-link">
        ← Back to Fighter Search
    </a>


    <section class="fighter-profile-card">


        <!-- IMAGE -->

        <div class="fighter-profile-image">

            <?php if (!empty($fighter["imageUrl"])): ?>

                <img
                    src="<?php echo htmlspecialchars($fighter["imageUrl"]); ?>"
                    alt="<?php echo htmlspecialchars($fighter["name"]); ?>"
                >

            <?php else: ?>

                <div class="fighter-profile-no-image">
                    No Fighter Image
                </div>

            <?php endif; ?>

        </div>



        <!-- INFORMATION -->

        <div class="fighter-profile-content">


            <span class="fighter-profile-label">
                UFC FIGHTER
            </span>


            <h1>
               