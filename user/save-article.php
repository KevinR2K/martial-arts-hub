<?php

session_start();

require_once "../config/database.php";


// Check if user is logged in
if (!isset($_SESSION["user_id"])) {

    header("Location: ../auth/login.php");
    exit();
}


// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: ../index.php");
    exit();
}


// Get submitted data
$article_id = (int)($_POST["article_id"] ?? 0);

$csrf_token = $_POST["csrf_token"] ?? "";

$user_id = $_SESSION["user_id"];


// Check CSRF token
if (
    empty($_SESSION["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $csrf_token)
) {

    http_response_code(403);
    exit("Invalid security token.");
}


// Validate article ID
if ($article_id <= 0) {

    header("Location: ../index.php");
    exit();
}


// Check if article exists
$sql = "SELECT id
        FROM articles
        WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $article_id);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows === 0) {

    $stmt->close();

    header("Location: ../index.php");
    exit();
}

$stmt->close();


// Save article
$sql = "INSERT IGNORE INTO saved_articles
        (user_id, article_id)
        VALUES (?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ii",
    $user_id,
    $article_id
);

$stmt->execute();

$stmt->close();


// Go to account
header("Location: account.php");
exit();

?>