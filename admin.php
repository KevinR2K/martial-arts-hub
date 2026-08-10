<?php

session_start();

require_once "config/database.php";


// Protect admin page
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.php");
    exit();
}


// Get some basic statistics
$article_count = $conn->query("SELECT COUNT(*) AS total FROM articles")->fetch_assoc()["total"];

$user_count = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()["total"];

$comment_count = $conn->query("SELECT COUNT(*) AS total FROM comments")->fetch_assoc()["total"];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard - Martial Arts Hub</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

    <div class="admin-container">

        <header class="admin-header">

            <div>
                <h1>Admin Dashboard</h1>
                <p>Welcome back, Admin.</p>
            </div>

            <a href="index.php" class="admin-back">
                ← Back to Website
            </a>

        </header>


        <section class="admin-stats">

            <div class="stat-card">
                <h2><?php echo $article_count; ?></h2>
                <p>Articles</p>
            </div>

            <div class="stat-card">
                <h2><?php echo $user_count; ?></h2>
                <p>Users</p>
            </div>

            <div class="stat-card">
                <h2><?php echo $comment_count; ?></h2>
                <p>Comments</p>
            </div>

        </section>


        <section class="admin-actions">

            <h2>Manage Website</h2>

            <div class="admin-grid">

                <a href="manage-articles.php" class="admin-card">
                    <span>📰</span>
                    <h3>Manage Articles</h3>
                    <p>Add, edit and delete articles.</p>
                </a>

                <a href="#" class="admin-card">
                    <span>⭐</span>
                    <h3>Featured Articles</h3>
                    <p>Manage articles shown on the homepage.</p>
                </a>

                <a href="#" class="admin-card">
                    <span>👥</span>
                    <h3>Users</h3>
                    <p>View registered users.</p>
                </a>

                <a href="#" class="admin-card">
                    <span>💬</span>
                    <h3>Comments</h3>
                    <p>Manage user comments.</p>
                </a>

            </div>

        </section>

    </div>

</body>

</html>