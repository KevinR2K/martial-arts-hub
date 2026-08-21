<?php

session_start();

require_once "../config/database.php";

// Protect admin page
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
   header("Location: ../index.php");
    exit();
}

// Get all articles
$sql = "SELECT id, title, category, is_featured, created_at
        FROM articles
        ORDER BY created_at DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Articles - Admin</title>

    <link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/admin.css">>
</head>

<body>

<div class="admin-container">

    <header class="admin-header">

        <div>
            <h1>Manage Articles</h1>
            <p>View and manage your website articles.</p>
        </div>

        <a href="admin.php" class="admin-back">
            ← Dashboard
        </a>

    </header>


    <div class="admin-toolbar">

        <a href="add-article.php" class="admin-btn">
            + Add New Article
        </a>

    </div>


    <div class="admin-table-wrapper">

        <table class="admin-table">

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Featured</th>
                    <th>Actions</th>
                </tr>

            </thead>

            <tbody>

            <?php if ($result->num_rows > 0): ?>

                <?php while ($article = $result->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?php echo $article["id"]; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($article["title"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($article["category"]); ?>
                        </td>

                        <td>

                            <?php if ($article["is_featured"]): ?>

                                <span class="status-featured">
                                    ⭐ Featured
                                </span>

                            <?php else: ?>

                                <span class="status-normal">
                                    Normal
                                </span>

                            <?php endif; ?>

                        </td>

                        <td>

                            <div class="table-actions">

                                <a
                                    href="edit-article.php?id=<?php echo $article["id"]; ?>"
                                    class="admin-btn"
                                >
                                    Edit
                                </a>

                                <a
                                    href="delete-article.php?id=<?php echo $article["id"]; ?>"
                                    class="admin-btn delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this article?');"
                                >
                                    Delete
                                </a>

                            </div>

                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="5" class="empty-message">
                        No articles found.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>

</html>