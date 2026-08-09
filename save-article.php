<?php

session_start();

require_once "config/database.php";

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit();

}

// Get the article ID
$article_id = $_GET["article_id"] ?? 0;

$user_id = $_SESSION["user_id"];

// Check if the article exists
$sql = "SELECT id FROM articles WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $article_id);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {

    echo "Article not found.";
    exit();

}

$stmt->close();


// Save the article
$sql = "INSERT INTO saved_articles (user_id, article_id)
        VALUES (?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param("ii", $user_id, $article_id);

if ($stmt->execute()) {

    header("Location: account.php");
    exit();

} else {

    echo "Could not save article.";

}

$stmt->close();

?>