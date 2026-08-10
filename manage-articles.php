<?php

session_start();

require_once "config/database.php";


// Protect admin page
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.php");
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

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

    <div class="admin-container">

        <div class="admin-header">

            <div>

                <h1>Manage Articles</h1>

                <p>View and manage your website articles.</p>

            </div>

            <a href="admin.php" class="admin-back">
                ← Dashboard
            </a>

        </div>


        <a href="add-article.php" class="admin-back">
            + Add New Article
        </a>


        <div class="article-table">

            <table>

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

                                    ⭐ Yes

                                <?php else: ?>

                                    No

                                <?php endif; ?>

                            </td>

                            <td>

                                <a href="edit-article.php?id=<?php echo $article["id"]; ?>">
                                    Edit
                                </a>

                                |

                                <a href="delete-article.php?id=<?php echo $article["id"]; ?>"
                                   onclick="return confirm('Are you sure you want to delete this article?');">
                                    Delete
                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</body>

</html>