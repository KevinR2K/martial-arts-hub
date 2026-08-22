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


// Get form data
$user_id = (int)$_SESSION["user_id"];

$article_id = (int)($_POST["article_id"] ?? 0);

$comment = trim($_POST["comment"] ?? "");

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

    header("Location: ../index.php");
    exit();
}


// Make sure the comment is not empty
if ($comment === "") {

    header("Location: ../article.php?id=" . $article_id);
    exit();
}


// Check that the article exists
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


// Save the comment
$sql = "INSERT INTO comments
        (user_id, article_id, comment)
        VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "iis",
    $user_id,
    $article_id,
    $comment
);

$stmt->execute();

$stmt->close();


// Return to article
header("Location: ../article.php?id=" . $article_id);
exit();

?>