<?php

session_start();

require_once "config/database.php";


// Make sure the user is logged in
if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit();

}


// Get the comment ID
$comment_id = $_GET["id"] ?? 0;

$user_id = $_SESSION["user_id"];


// Delete only if the comment belongs to this user
$sql = "DELETE FROM comments
        WHERE id = ?
        AND user_id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("ii", $comment_id, $user_id);


if ($stmt->execute()) {

    header("Location: " . $_SERVER["HTTP_REFERER"]);
    exit();

} else {

    echo "Could not delete comment.";

}


$stmt->close();

?>