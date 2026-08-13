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

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($fighter["name"] ?? "Fighter"); ?>
        - Martial Arts Hub
    </title>

</head>


<body>


    <h1>
        <?php echo htmlspecialchars($fighter["name"] ?? "Unknown Fighter"); ?>
    </h1>


    <?php if (!empty($fighter["imageUrl"])): ?>

        <img
            src="<?php echo htmlspecialchars($fighter["imageUrl"]); ?>"
            alt="<?php echo htmlspecialchars($fighter["name"]); ?>"
            width="300"
        >

    <?php endif; ?>


    <h2>Fighter Information</h2>


    <p>

        <strong>Division:</strong>

        <?php echo htmlspecialchars(
            $fighter["division"] ?? "N/A"
        ); ?>

    </p>


    <p>

        <strong>Record:</strong>

        <?php echo htmlspecialchars(
            $fighter["recordText"] ?? "N/A"
        ); ?>

    </p>


    <p>

        <strong>Country:</strong>

        <?php echo htmlspecialchars(
            $fighter["country"] ?? "N/A"
        ); ?>

    </p>


    <p>

        <strong>Fighting Style:</strong>

        <?php echo htmlspecialchars(
            $fighter["fightingStyle"] ?? "N/A"
        ); ?>

    </p>


    <p>

        <strong>Stance:</strong>

        <?php echo htmlspecialchars(
            $fighter["stance"] ?? "N/A"
        ); ?>

    </p>


    <p>

        <strong>Height:</strong>

        <?php echo htmlspecialchars(
            $fighter["heightInches"] ?? "N/A"
        ); ?>

    </p>


    <p>

        <strong>Reach:</strong>

        <?php echo htmlspecialchars(
            $fighter["reachInches"] ?? "N/A"
        ); ?>

    </p>


    <p>

        <strong>Age:</strong>

        <?php echo htmlspecialchars(
            $fighter["age"] ?? "N/A"
        ); ?>

    </p>
  <?php

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


<?php if (!isset($_SESSION["user_id"])): ?>

    <p>
        <a href="login.php">
            Login to follow this fighter
        </a>
    </p>


<?php elseif ($is_following): ?>

    <button type="button" disabled>
        ⭐ Following ✓
    </button>


<?php else: ?>

    <form method="POST" action="follow-fighter.php">

        <input
            type="hidden"
            name="fighter_slug"
            value="<?php echo htmlspecialchars($fighter["slug"]); ?>"
        >

        <input
            type="hidden"
            name="fighter_name"
            value="<?php echo htmlspecialchars($fighter["name"]); ?>"
        >

        <input
            type="hidden"
            name="fighter_image"
            value="<?php echo htmlspecialchars($fighter["imageUrl"] ?? ""); ?>"
        >

        <button type="submit">
            ⭐ Follow Fighter
        </button>

    </form>

<?php endif; ?>


</body>

</html>