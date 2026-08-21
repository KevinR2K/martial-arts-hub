<?php

session_start();
require_once "../config/database.php";


// Protect admin page
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
   header("Location: ../index.php");
    exit();
}


// Feature / Unfeature article
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $article_id = (int)($_POST["article_id"] ?? 0);
    $featured = (int)($_POST["featured"] ?? 0);

    if ($article_id > 0) {

        $stmt = $conn->prepare("
            UPDATE articles
            SET is_featured = ?
            WHERE id = ?
        ");

        $stmt->bind_param("ii", $featured, $article_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: manage-featured.php");
    exit();
}


// Get all articles
$result = $conn->query("
    SELECT id, title, category, is_featured, created_at
    FROM articles
    ORDER BY is_featured DESC, created_at DESC
");

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Featured Articles - Martial Arts Hub</title>

    <link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/admin.css">>

</head>

<body>

<div class="admin-container">

    <header class="admin-header">

        <div>
            <h1>Featured Articles</h1>
            <p>Choose which articles appear on the homepage.</p>
        </div>

        <a href="admin.php" class="admin-back">
            ← Dashboard
        </a>

    </header>


    <div class="admin-table-wrapper">

        <table class="admin-table">

            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php while ($article = $result->fetch_assoc()): ?>

                <tr>

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

                        <?php if ($article["is_featured"]): ?>

                            <form method="POST">

                                <input
                                    type="hidden"
                                    name="article_id"
                                    value="<?php echo $article["id"]; ?>"
                                >

                                <input
                                    type="hidden"
                                    name="featured"
                                    value="0"
                                >

                                <button class="admin-btn secondary-btn">
                                    Remove Featured
                                </button>

                            </form>

                        <?php else: ?>

                            <form method="POST">

                                <input
                                    type="hidden"
                                    name="article_id"
                                    value="<?php echo $article["id"]; ?>"
                                >

                                <input
                                    type="hidden"
                                    name="featured"
                                    value="1"
                                >

                                <button class="admin-btn">
                                    Make Featured
                                </button>

                            </form>

                        <?php endif; ?>

                    </td>

                </tr>

            <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</div>

</body>

</html>
