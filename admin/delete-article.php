<?php

session_start();

require_once "../config/database.php";


// Only admins can delete articles
if (
    !isset($_SESSION["user_id"]) ||
    ($_SESSION["role"] ?? "") !== "admin"
) {
    header("Location: ../index.php");
    exit();
}


// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: manage-articles.php");
    exit();
}


// Get submitted data
$article_id = (int)($_POST["article_id"] ?? 0);

$csrf_token = $_POST["csrf_token"] ?? "";


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
    header("Location: manage-articles.php");
    exit();
}


// Delete article
$sql = "DELETE FROM articles
        WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $article_id);

$stmt->execute();

$stmt->close();


// Return to Manage Articles
header("Location: manage-articles.php");
exit();

?>