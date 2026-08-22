<?php

session_start();

require_once "../config/database.php";


// Make sure the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}


// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.php");
    exit();
}


// Get logged-in user ID
$user_id = (int)$_SESSION["user_id"];


// Get fighter information
$fighter_slug = trim($_POST["fighter_slug"] ?? "");

$fighter_name = trim($_POST["fighter_name"] ?? "");

$fighter_image = trim($_POST["fighter_image"] ?? "");

$csrf_token = $_POST["csrf_token"] ?? "";


// Check CSRF token
if (
    empty($_SESSION["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $csrf_token)
) {
    http_response_code(403);
    exit("Invalid security token.");
}


// Make sure fighter data exists
if ($fighter_slug === "" || $fighter_name === "") {
    header("Location: ../index.php");
    exit();
}


// Save fighter
$sql = "INSERT IGNORE INTO followed_fighters
        (user_id, fighter_slug, fighter_name, fighter_image)
        VALUES (?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "isss",
    $user_id,
    $fighter_slug,
    $fighter_name,
    $fighter_image
);

$stmt->execute();

$stmt->close();


// Return to fighter profile
header(
    "Location: ../fighter.php?slug=" .
    urlencode($fighter_slug)
);

exit();

?>