<?php

session_start();

require_once "../config/database.php";


// Protect account page
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}


$user_id = $_SESSION["user_id"];


// ========================================
// USER INFORMATION
// ========================================

$sql = "SELECT id, name, email, created_at
        FROM users
        WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();


// ========================================
// SAVED ARTICLES
// ========================================

$sql = "SELECT
            articles.id,
            articles.title,
            articles.image,
            articles.category
        FROM saved_articles
        INNER JOIN articles
            ON saved_articles.article_id = articles.id
        WHERE saved_articles.user_id = ?
        ORDER BY saved_articles.saved_at DESC";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$saved_articles = $stmt->get_result();

$stmt->close();


// ========================================
// FOLLOWED FIGHTERS
// ========================================

$sql = "SELECT *
        FROM followed_fighters
        WHERE user_id = ?
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$followed_fighters = $stmt->get_result();

$stmt->close();


// ========================================
// MY COMMENTS
// ========================================

$sql = "SELECT
            comments.id,
            comments.comment,
            comments.created_at,
            articles.id AS article_id,
            articles.title AS article_title
        FROM comments
        INNER JOIN articles
            ON comments.article_id = articles.id
        WHERE comments.user_id = ?
        ORDER BY comments.created_at DESC";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$my_comments = $stmt->get_result();

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

    <title>My Account - Martial Arts Hub</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>


<!-- NAVBAR -->

<header class="account-page-header">

    <nav class="account-navbar">

        <a href="../index.php" class="account-logo">
            Martial Arts Hub
        </a>

        <ul class="account-nav">

            <li>
                <a href="../index.php">Home</a>
            </li>

            <li>
                <a href="../categories.php">Categories</a>
            </li>

            <li>
                <a href="../fighters.php">Fighters</a>
            </li>

            <li>
                <a href="../index.php#about">About</a>
            </li>

            <li>
                <a href="../auth/logout.php">Logout</a>
            </li>

        </ul>

    </nav>

</header>



<main class="account-page">


    <!-- PROFILE -->

    <section class="account-profile-card">

        <p class="account-label">
            MY ACCOUNT
        </p>

        <h1>
            Welcome,
            <?php echo htmlspecialchars($user["name"]); ?> 👋
        </h1>


        <div class="account-info-grid">

            <div>

                <span>Name</span>

                <strong>
                    <?php echo htmlspecialchars($user["name"]); ?>
                </strong>

            </div>


            <div>

                <span>Email</span>

                <strong>
                    <?php echo htmlspecialchars($user["email"]); ?>
                </strong>

            </div>


            <div>

                <span>Member Since</span>

                <strong>
                    <?php
                    echo date(
                        "d M Y",
                        strtotime($user["created_at"])
                    );
                    ?>
                </strong>

            </div>

        </div>


        <!-- ACCOUNT LINKS -->

        <div class="account-quick-links">

            <a href="#saved-articles">
                ❤️ Saved Articles
            </a>

            <a href="#my-comments">
                💬 My Comments
            </a>

            <a href="#followed-fighters">
                🥊 Followed Fighters
            </a>

           
        </div>

    </section>



    <!-- =====================================
         SAVED ARTICLES
    ====================================== -->

    <section
        class="account-section"
        id="saved-articles"
    >

        <div class="account-section-heading">

            <div>

                <p>YOUR COLLECTION</p>

                <h2>❤️ Saved Articles</h2>

            </div>

            <span>
                <?php echo $saved_articles->num_rows; ?>
                saved
            </span>

        </div>


        <?php if ($saved_articles->num_rows > 0): ?>


            <div class="account-content-grid">


                <?php while ($article = $saved_articles->fetch_assoc()): ?>


                    <article class="account-article-card">


                        <img
                            src="../<?php echo htmlspecialchars(ltrim($article["image"], "/")); ?>"
                            alt="<?php echo htmlspecialchars($article["title"]); ?>"
                        >


                        <div class="account-card-content">


                            <span class="account-category">

                                <?php echo htmlspecialchars(
                                    $article["category"]
                                ); ?>

                            </span>


                            <h3>

                                <?php echo htmlspecialchars(
                                    $article["title"]
                                ); ?>

                            </h3>


                            <a
                                href="../article.php?id=<?php echo $article["id"]; ?>"
                                class="account-action-link"
                            >
                                Read Article →
                            </a>


                        </div>

                    </article>


                <?php endwhile; ?>


            </div>


        <?php else: ?>


            <div class="account-empty">

                <h3>No saved articles yet</h3>

                <p>
                    Save articles you want to read again later.
                </p>

                <a href="../categories.php">
                    Explore Articles
                </a>

            </div>


        <?php endif; ?>


    </section>



    <!-- =====================================
         FOLLOWED FIGHTERS
    ====================================== -->

    <section
        class="account-section"
        id="followed-fighters"
    >


        <div class="account-section-heading">

            <div>

                <p>YOUR FIGHTERS</p>

                <h2>🥊 Followed Fighters</h2>

            </div>

            <span>
                <?php echo $followed_fighters->num_rows; ?>
                following
            </span>

        </div>


        <?php if ($followed_fighters->num_rows > 0): ?>


            <div class="account-fighter-grid">


                <?php while ($fighter = $followed_fighters->fetch_assoc()): ?>


                    <article class="account-fighter-card">


                        <div class="account-fighter-image">


                            <?php if (!empty($fighter["fighter_image"])): ?>

                                <img
                                    src="<?php echo htmlspecialchars($fighter["fighter_image"]); ?>"
                                    alt="<?php echo htmlspecialchars($fighter["fighter_name"]); ?>"
                                >

                            <?php else: ?>

                                <span>
                                    No Image
                                </span>

                            <?php endif; ?>


                        </div>


                        <div class="account-card-content">


                            <h3>
                                <?php echo htmlspecialchars(
                                    $fighter["fighter_name"]
                                ); ?>
                            </h3>


                            <a
                                href="../fighter.php?slug=<?php echo urlencode($fighter["fighter_slug"]); ?>"
                                class="account-action-link"
                            >
                                View Profile →
                            </a>


                        </div>

                    </article>


                <?php endwhile; ?>


            </div>


        <?php else: ?>


            <div class="account-empty">

                <h3>No followed fighters yet</h3>

                <p>
                    Search the fighter database and follow your favorites.
                </p>

                <a href="../fighters.php">
                    Explore Fighters
                </a>

            </div>


        <?php endif; ?>


    </section>



    <!-- =====================================
         MY COMMENTS
    ====================================== -->

    <section
        class="account-section"
        id="my-comments"
    >


        <div class="account-section-heading">

            <div>

                <p>YOUR ACTIVITY</p>

                <h2>💬 My Comments</h2>

            </div>

            <span>
                <?php echo $my_comments->num_rows; ?>
                comments
            </span>

        </div>


        <?php if ($my_comments->num_rows > 0): ?>


            <div class="account-comments-list">


                <?php while ($comment = $my_comments->fetch_assoc()): ?>


                    <article class="account-comment-card">


                        <div class="account-comment-top">


                            <h3>

                                <?php echo htmlspecialchars(
                                    $comment["article_title"]
                                ); ?>

                            </h3>


                            <span>

                                <?php

                                echo date(
                                    "d M Y",
                                    strtotime($comment["created_at"])
                                );

                                ?>

                            </span>


                        </div>


                        <p>

                            <?php echo htmlspecialchars(
                                $comment["comment"]
                            ); ?>

                        </p>


                        <a
                            href="../article.php?id=<?php echo $comment["article_id"]; ?>"
                            class="account-action-link"
                        >
                            View Article →
                        </a>


                    </article>


                <?php endwhile; ?>


            </div>


        <?php else: ?>


            <div class="account-empty">

                <h3>No comments yet</h3>

                <p>
                    Comments you post on articles will appear here.
                </p>

                <a href="../categories.php">
                    Explore Articles
                </a>

            </div>


        <?php endif; ?>


    </section>



    <!-- =====================================
         NEWSLETTER
    ====================================== -->

    



    <!-- LOGOUT -->

    <div class="account-logout-area">

        <a href="../auth/logout.php">
            🚪 Logout
        </a>

    </div>


</main>


</body>

</html>
