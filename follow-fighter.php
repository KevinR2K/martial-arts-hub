<?php

session_start();

require_once "config/database.php";


// Make sure the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}


// Get the logged-in user's ID
$user_id = $_SESSION["user_id"];


// Get fighter information
$fighter_slug = $_POST["fighter_slug"] ?? "";
$fighter_name = $_POST["fighter_name"] ?? "";
$fighter_image = $_POST["fighter_image"] ?? "";


// Make sure we received the fighter
if (empty($fighter_slug) || empty($fighter_name)) {
    header("Location: index.php");
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
header("Location: fighter.php?slug=" . urlencode($fighter_slug));
exit();

?>
