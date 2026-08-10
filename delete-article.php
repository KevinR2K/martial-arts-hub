<?php

session_start();

require_once "config/database.php";


// Only admins can delete articles
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.php");
    exit();
}


// Get article ID
$article_id = $_GET["id"] ?? 0;


// Delete article
$sql = "DELETE FROM articles WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $article_id);


if ($stmt->execute()) {

    header("Location: manage-articles.php");
    exit();

} else {

    echo "Error deleting article.";

}


$stmt->close();

?>