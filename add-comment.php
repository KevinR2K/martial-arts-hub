<?php

session_start();

require_once "config/database.php";


// Make sure the user is logged in
if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit();

}


// Get the data from the form
$user_id = $_SESSION["user_id"];

$article_id = $_POST["article_id"] ?? 0;

$comment = trim($_POST["comment"] ?? "");


// Make sure the comment is not empty
if ($comment === "") {

    echo "Comment cannot be empty.";
    exit();

}


// Check that the article exists
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


// Save the comment
$sql = "INSERT INTO comments (user_id, article_id, comment)
        VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param("iis", $user_id, $article_id, $comment);


if ($stmt->execute()) {

    header("Location: article.php?id=" . $article_id);
    exit();

} else {

    echo "Could not add comment.";

}

$stmt->close();

?>