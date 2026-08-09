<?php

session_start();

require_once "config/database.php";


// Get article ID from URL

$article_id = $_GET["id"] ?? 0;


// Find the article

$sql = "SELECT id, title, content, image, category, created_at
        FROM articles
        WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $article_id);

$stmt->execute();

$result = $stmt->get_result();


// Check if article exists

if ($result->num_rows == 0) {

    echo "Article not found.";
    exit();

}


$article = $result->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($article["title"]); ?>
        - Martial Arts Hub
    </title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>


    <main class="article-page">

        <div class="article-header">

            <span class="category">

                <?php echo htmlspecialchars($article["category"]); ?>

            </span>

            <h1>

                <?php echo htmlspecialchars($article["title"]); ?>

            </h1>

            <p class="article-date">

                Published:
                <?php echo htmlspecialchars($article["created_at"]); ?>

            </p>

        </div>


        <img
            class="article-main-image"
            src="<?php echo htmlspecialchars($article["image"]); ?>"
            alt="<?php echo htmlspecialchars($article["title"]); ?>"
        >


        <div class="article-body">

            <p>

                <?php echo nl2br(
                    htmlspecialchars($article["content"])
                ); ?>

            </p>

        </div>


        <div class="article-actions">

            <?php if (isset($_SESSION["user_id"])): ?>

                <a
                    href="save-article.php?article_id=<?php echo $article["id"]; ?>"
                    class="save-btn"
                >
                    ❤️ Save Article
                </a>

            <?php else: ?>

                <a href="login.php" class="save-btn">
                    ❤️ Login to Save
                </a>

            <?php endif; ?>

        </div>
        <?php

// Get comments for this article

$sql = "SELECT comments.id,
               comments.user_id,
               comments.comment,
               comments.created_at,
               users.name
        FROM comments
        INNER JOIN users
        ON comments.user_id = users.id
        WHERE comments.article_id = ?
        ORDER BY comments.created_at DESC";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $article_id);

$stmt->execute();

$comments = $stmt->get_result();

$stmt->close();

?>
        <section class="comments-section">

    <h2>💬 Comments</h2>

    <?php if (isset($_SESSION["user_id"])): ?>

        <form action="add-comment.php" method="POST" class="comment-form">

            <input
                type="hidden"
                name="article_id"
                value="<?php echo $article["id"]; ?>"
            >

            <textarea
                name="comment"
                placeholder="Write your comment..."
                required
            ></textarea>

            <button type="submit">
                Post Comment
            </button>

        </form>
        <div class="comments-list">

    <?php if ($comments->num_rows > 0): ?>

        <?php while ($comment = $comments->fetch_assoc()): ?>

            <div class="comment">

                <div class="comment-header">

                    <strong>
                        <?php echo htmlspecialchars($comment["name"]); ?>
                    </strong>

                    <span>
                        <?php echo htmlspecialchars($comment["created_at"]); ?>
                    </span>

                </div>

                <p>
                    <?php echo nl2br(
                        htmlspecialchars($comment["comment"])
                    ); ?>
                </p>
                <?php if (
    isset($_SESSION["user_id"]) &&
    $_SESSION["user_id"] == $comment["user_id"]
): ?>

    <a
        href="delete-comment.php?id=<?php echo $comment["id"]; ?>"
        class="delete-comment"
    >
        🗑️ Delete
    </a>

<?php endif; ?>

            </div>

        <?php endwhile; ?>

    <?php else: ?>

        <p>No comments yet. Be the first to comment!</p>

    <?php endif; ?>

</div>

    <?php else: ?>

        <p>
            Please
            <a href="login.php">login</a>
            to leave a comment.
        </p>

    <?php endif; ?>

</section>


        <a href="index.php" class="back-link">
            ← Back to Articles
        </a>

    </main>


</body>

</html>