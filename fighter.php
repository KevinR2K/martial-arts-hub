<?php

session_start();

require_once "config/api.php";
require_once "config/database.php";


// Create CSRF token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}


// Helper function for API values
function displayFighterValue($value)
{
    if ($value === null || $value === "") {
        return "N/A";
    }

    if (is_array($value)) {

        $parts = [];

        foreach ($value as $key => $item) {

            if (
                is_string($item) ||
                is_numeric($item)
            ) {
                $parts[] = $item;
            }
        }

        if (!empty($parts)) {
            return implode(" - ", $parts);
        }

        return "N/A";
    }

    return (string)$value;
}


// Format fighter record
function formatFighterRecord($record)
{
    if (empty($record)) {
        return "N/A";
    }

    if (!is_array($record)) {
        return (string)$record;
    }


    $wins = $record["wins"]
        ?? $record["win"]
        ?? null;

    $losses = $record["losses"]
        ?? $record["loss"]
        ?? null;

    $draws = $record["draws"]
        ?? $record["draw"]
        ?? null;


    if (
        $wins !== null ||
        $losses !== null ||
        $draws !== null
    ) {

        return ($wins ?? 0)
            . "-"
            . ($losses ?? 0)
            . "-"
            . ($draws ?? 0);
    }


    $parts = [];

    foreach ($record as $value) {

        if (
            is_string($value) ||
            is_numeric($value)
        ) {
            $parts[] = $value;
        }
    }


    return !empty($parts)
        ? implode("-", $parts)
        : "N/A";
}


// Get fighter slug
$slug = trim($_GET["slug"] ?? "");


// Validate slug
if ($slug === "") {
    die("Fighter not found.");
}


// Build API URL
$url = $cito_base_url
    . "/ufc/fighters/"
    . urlencode($slug);


// Call API
$ch = curl_init($url);

curl_setopt(
    $ch,
    CURLOPT_RETURNTRANSFER,
    true
);

curl_setopt(
    $ch,
    CURLOPT_HTTPHEADER,
    [
        "x-api-key: " . $cito_api_key
    ]
);

$response = curl_exec($ch);


// Check API request
if ($response === false) {

    curl_close($ch);

    die("Unable to load fighter.");
}

curl_close($ch);


// Convert JSON
$data = json_decode(
    $response,
    true
);


// Check API response
if (
    !isset($data["success"]) ||
    !$data["success"] ||
    empty($data["data"])
) {
    die("Unable to find fighter.");
}


// Fighter information
$fighter = $data["data"];


$fighter_slug =
    $fighter["slug"] ?? $slug;


$fighter_name =
    displayFighterValue(
        $fighter["name"] ?? "Unknown Fighter"
    );


$fighter_image =
    isset($fighter["imageUrl"]) &&
    is_string($fighter["imageUrl"])
        ? $fighter["imageUrl"]
        : "";


// Format values
$division = displayFighterValue(
    $fighter["division"] ?? null
);

$record = formatFighterRecord(
    $fighter["record"] ?? null
);

$stance = displayFighterValue(
    $fighter["stance"] ?? null
);

$height = displayFighterValue(
    $fighter["height"] ?? null
);

$weight = displayFighterValue(
    $fighter["weight"] ?? null
);

$reach = displayFighterValue(
    $fighter["reach"] ?? null
);


// Check whether user already follows fighter
$is_following = false;


if (isset($_SESSION["user_id"])) {

    $user_id =
        (int)$_SESSION["user_id"];


    $check_sql = "
        SELECT id
        FROM followed_fighters
        WHERE user_id = ?
        AND fighter_slug = ?
    ";


    $check_stmt =
        $conn->prepare($check_sql);


    $check_stmt->bind_param(
        "is",
        $user_id,
        $fighter_slug
    );


    $check_stmt->execute();


    $check_result =
        $check_stmt->get_result();


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
        <?php echo htmlspecialchars($fighter_name); ?>
        - Martial Arts Hub
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>


<!-- NAVBAR -->

<header class="fighter-profile-header">

    <nav class="fighter-profile-navbar">


        <a
            href="index.php"
            class="fighter-profile-logo"
        >
            Martial Arts Hub
        </a>


        <ul class="fighter-profile-nav">


            <li>
                <a href="index.php">
                    Home
                </a>
            </li>


            <li>
                <a href="categories.php">
                    Categories
                </a>
            </li>


            <li>
                <a href="fighters.php">
                    Fighters
                </a>
            </li>


            <li>
                <a href="index.php#about">
                    About
                </a>
            </li>


            <?php if (isset($_SESSION["user_id"])): ?>


                <li>

                    <a href="user/account.php">
                        My Account
                    </a>

                </li>


                <li>

                    <a href="auth/logout.php">
                        Logout
                    </a>

                </li>


            <?php else: ?>


                <li>

                    <a href="auth/login.php">
                        Login
                    </a>

                </li>


            <?php endif; ?>


        </ul>


    </nav>

</header>



<main class="fighter-profile-page">


    <a
        href="fighters.php"
        class="fighter-back-link"
    >
        ← Back to Fighter Search
    </a>



    <section class="fighter-profile-card">


        <!-- IMAGE -->

        <div class="fighter-profile-image">


            <?php if ($fighter_image !== ""): ?>


                <img
                    src="<?php echo htmlspecialchars($fighter_image); ?>"
                    alt="<?php echo htmlspecialchars($fighter_name); ?>"
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

                <?php
                echo htmlspecialchars(
                    $fighter_name
                );
                ?>

            </h1>



            <!-- MAIN DETAILS -->

            <div class="fighter-main-details">


                <div>

                    <span>
                        Division
                    </span>

                    <strong>

                        <?php
                        echo htmlspecialchars(
                            $division
                        );
                        ?>

                    </strong>

                </div>


                <div>

                    <span>
                        Record
                    </span>

                    <strong>

                        <?php
                        echo htmlspecialchars(
                            $record
                        );
                        ?>

                    </strong>

                </div>


            </div>



            <!-- INFORMATION -->

            <h2>
                Fighter Information
            </h2>


            <div class="fighter-info-grid">


                <div class="fighter-info-item">

                    <span>
                        Stance
                    </span>

                    <strong>

                        <?php
                        echo htmlspecialchars(
                            $stance
                        );
                        ?>

                    </strong>

                </div>


                <div class="fighter-info-item">

                    <span>
                        Height
                    </span>

                    <strong>

                        <?php
                        echo htmlspecialchars(
                            $height
                        );
                        ?>

                    </strong>

                </div>


                <div class="fighter-info-item">

                    <span>
                        Weight
                    </span>

                    <strong>

                        <?php
                        echo htmlspecialchars(
                            $weight
                        );
                        ?>

                    </strong>

                </div>


                <div class="fighter-info-item">

                    <span>
                        Reach
                    </span>

                    <strong>

                        <?php
                        echo htmlspecialchars(
                            $reach
                        );
                        ?>

                    </strong>

                </div>


            </div>



            <!-- FOLLOW AREA -->

            <div class="fighter-follow-area">


                <?php if (isset($_SESSION["user_id"])): ?>


                    <?php if ($is_following): ?>


                        <button
                            type="button"
                            class="fighter-following-btn"
                            disabled
                        >
                            ✓ Following
                        </button>


                    <?php else: ?>


                        <form
                            action="user/follow-fighter.php"
                            method="POST"
                        >


                            <input
                                type="hidden"
                                name="fighter_slug"
                                value="<?php echo htmlspecialchars($fighter_slug); ?>"
                            >


                            <input
                                type="hidden"
                                name="fighter_name"
                                value="<?php echo htmlspecialchars($fighter_name); ?>"
                            >


                            <input
                                type="hidden"
                                name="fighter_image"
                                value="<?php echo htmlspecialchars($fighter_image); ?>"
                            >


                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?php echo htmlspecialchars($_SESSION["csrf_token"]); ?>"
                            >


                            <button
                                type="submit"
                                class="fighter-follow-btn"
                            >
                                + Follow Fighter
                            </button>


                        </form>


                    <?php endif; ?>


                <?php else: ?>


                    <a
                        href="auth/login.php"
                        class="fighter-login-btn"
                    >
                        Login to Follow
                    </a>


                <?php endif; ?>


            </div>


        </div>


    </section>


</main>


</body>

</html>