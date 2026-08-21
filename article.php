<?php

session_start();

require_once "config/database.php";


// Get article ID
$article_id = (int)($_GET["id"] ?? 0);


// Get article
$sql = "SELECT id, title, content, image, category, created_at
        FROM articles
        WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $article_id);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows === 0) {
    echo "Article not found.";
    exit();
}


$article = $result->fetch_assoc();

$stmt->close();


// Get comments
$sql = "SELECT
            comments.id,
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

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?php echo htmlspecialchars($article["title"]); ?>
        - Martial Arts Hub
    </title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>


<body>


<!-- NAVBAR -->

<header class="article-page-header">

    <nav class="article-navbar">

        <a href="index.php" class="article-logo">
            Martial Arts Hub
        </a>


        <ul class="article-nav-links">

            <li>
                <a href="index.php">Home</a>
            </li>

            <li>
                <a href="categories.php">Articles</a>
            </li>

            <li>
                <a href="fighters.php">Fighters</a>
            </li>

            <li>
                <a href="index.php#categories">Categories</a>
            </li>

            <li>
                <a href="index.php#about">About</a>
            </li>

        </ul>


        <div class="article-auth">

            <?php if (isset($_SESSION["user_id"])): ?>

                <a href="user/account.php">
                    👤 My Account
                </a>

            <?php else: ?>

                <a href="auth/login.php">
                    Login
                </a>

            <?php endif; ?>

        </div>

    </nav>

</header>



<main class="single-article-page">


    <!-- BACK -->

    <a href="categories.php" class="article-back">
        ← Back to Articles
    </a>



    <!-- ARTICLE -->

    <article class="single-article">


        <header class="single-article-header">

            <span class="single-article-category">

                <?php echo htmlspecialchars(
                    $article["category"]
                ); ?>

            </span>


            <h1>

                <?php echo htmlspecialchars(
                    $article["title"]
                ); ?>

            </h1>


            <p class="single-article-date">

                Published
                <?php
                echo date(
                    "d M Y",
                    strtotime($article["created_at"])
                );
                ?>

            </p>

        </header>



        <div class="single-article-image">

            <img
                src="<?php echo htmlspecialchars($article["image"]); ?>"
                alt="<?php echo htmlspecialchars($article["title"]); ?>"
            >

        </div>



        <div class="single-article-content">

            <p>
                <?php
                echo nl2br(
                    htmlspecialchars($article["content"])
                );
                ?>
            </p>

        </div>



        <!-- SAVE -->

        <div class="single-article-actions">

            <?php if (isset($_SESSION["user_id"])): ?>

                <a
                    href="user/save-article.php?article_id=<?php echo $article["id"]; ?>"
                    class="article-save-btn"
                >
                    ❤️ Save Article
                </a>

            <?php else: ?>

                <a
                    href="auth/login.php"
                    class="article-save-btn"
                >
                    ❤️ Login to Save
                </a>

            <?php endif; ?>

        </div>


    </article>



    <!-- COMMENTS -->

    <section class="article-comments">


        <div class="comments-heading">

            <div>

                <span>JOIN THE DISCUSSION</span>

                <h2>
                    💬 Comments
                </h2>

            </div>

            <strong>
                <?php echo $comments->num_rows; ?>
            </strong>

        </div>



        <!-- COMMENT FORM -->

        <?php if (isset($_SESSION["user_id"])): ?>


            <form
                action="user/add-comment.php"
                method="POST"
                class="article-comment-form"
            >

                <input
                    type="hidden"
                    name="article_id"
                    value="<?php echo $article["id"]; ?>"
                >


                <textarea
                    name="comment"
                    placeholder="Share your thoughts..."
                    required
                ></textarea>


                <button type="submit">
                    Post Comment
                </button>

            </form>


        <?php else: ?>


            <div class="comment-login-message">

                <p>
                    Want to join the discussion?
                </p>

                <a href="auth/login.php">
                    Login to Comment
                </a>

            </div>


        <?php endif; ?>



        <!-- COMMENTS LIST -->

        <div class="article-comments-list">


            <?php if ($comments->num_rows > 0): ?>


                <?php while ($comment = $comments->fetch_assoc()): ?>


                    <article class="article-comment">


                        <div class="article-comment-header">

                            <div>

                                <div class="comment-avatar">
                                    <?php
                                    echo strtoupper(
                                        substr($comment["name"], 0, 1)
                                    );
                                    ?>
                                </div>


                                <div>

                                    <strong>
                                        <?php echo htmlspecialchars(
                                            $comment["name"]
                                        ); ?>
                                    </strong>

                                    <span>

                                        <?php

                                        echo date(
                                            "d M Y • H:i",
                                            strtotime($comment["created_at"])
                                        );

                                        ?>

                                    </span>

                                </div>

                            </div>


                            <?php if (
                                isset($_SESSION["user_id"]) &&
                                $_SESSION["user_id"] == $comment["user_id"]
                            ): ?>

                                <a
                                    href="user/delete-comment.php?id=<?php echo $comment["id"]; ?>"
                                    class="article-delete-comment"
                                    onclick="return confirm('Delete this comment?');"
                                >
                                    Delete
                                </a>

                            <?php endif; ?>

                        </div>



                        <p>

                            <?php
                            echo nl2br(
                                htmlspecialchars($comment["comment"])
                            );
                            ?>

                        </p>


                    </article>


                <?php endwhile; ?>


            <?php else: ?>


                <div class="no-comments">

                    <h3>No comments yet</h3>

                    <p>
                        Be the first person to share your thoughts.
                    </p>

                </div>


            <?php endif; ?>


        </div>


    </section>


</main>


</body>

</html>