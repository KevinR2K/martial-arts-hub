<?php

session_start();

require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$sql = "SELECT *
        FROM followed_fighters
        WHERE user_id = ?
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$followed_fighters = $stmt->get_result();

$sql = "SELECT id, name, email, created_at
        FROM users
        WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();
// Get the user's saved articles

$sql = "SELECT articles.id,
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
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Account - Martial Arts Hub</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

    <div class="account-container">

        <div class="account-box">

            <p class="account-label">MY ACCOUNT</p>

            <h1>Welcome, <?php echo htmlspecialchars($user["name"]); ?> 👋</h1>

            <div class="account-info">

                <div>
                    <strong>Name</strong>
                    <span>
                        <?php echo htmlspecialchars($user["name"]); ?>
                    </span>
                </div>

                <div>
                    <strong>Email</strong>
                    <span>
                        <?php echo htmlspecialchars($user["email"]); ?>
                    </span>
                </div>

                <div>
                    <strong>Member Since</strong>
                    <span>
                        <?php echo htmlspecialchars($user["created_at"]); ?>
                    </span>
                </div>

            </div>

            <div class="account-links">

                <a href="#">❤️ Saved Articles</a>

                <a href="#">💬 My Comments</a>

                <a href="#">🥊 Followed Fighters</a>

                <a href="#">🔔 Newsletter Settings</a>

                <a href="logout.php" class="logout-btn">🚪 Logout</a>

            </div>
            <div class="saved-articles">

    <h2>❤️ Saved Articles</h2>

    <?php if ($saved_articles->num_rows > 0): ?>

        <div class="saved-article-list">

            <?php while ($article = $saved_articles->fetch_assoc()): ?>

                <div class="saved-article">

                    <img
                        src="<?php echo htmlspecialchars($article["image"]); ?>"
                        alt="<?php echo htmlspecialchars($article["title"]); ?>"
                    >

                    <div>

                        <span>
                            <?php echo htmlspecialchars($article["category"]); ?>
                        </span>

                        <h3>
                            <?php echo htmlspecialchars($article["title"]); ?>
                        </h3>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <p>You haven't saved any articles yet.</p>

    <?php endif; ?>

</div>

        </div>

    </div>
    <section class="followed-fighters">

    <h2>My Followed Fighters</h2>

    <?php if ($followed_fighters->num_rows > 0): ?>

        <div class="fighter-grid">

            <?php while ($fighter = $followed_fighters->fetch_assoc()): ?>

                <div class="fighter-card">

                    <?php if (!empty($fighter["fighter_image"])): ?>

                        <img
                            src="<?php echo htmlspecialchars($fighter["fighter_image"]); ?>"
                            alt="<?php echo htmlspecialchars($fighter["fighter_name"]); ?>"
                        >

                    <?php endif; ?>


                    <h3>
                        <?php echo htmlspecialchars($fighter["fighter_name"]); ?>
                    </h3>


                    <a href="fighter.php?slug=<?php echo urlencode($fighter["fighter_slug"]); ?>">
                        View Profile →
                    </a>

                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <p>
            You are not following any fighters yet.
        </p>

        <a href="fighters.php">
            Explore Fighters →
        </a>

    <?php endif; ?>

</section>

</body>

</html>