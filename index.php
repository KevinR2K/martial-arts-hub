<?php
session_start();

// Create CSRF token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

require_once "config/database.php";
require_once "api/latest-articles.php";

$sql = "SELECT id, title, content, image, category, is_featured
        FROM articles
        WHERE is_featured = TRUE
        ORDER BY created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Martial Arts Hub</title>

    <link rel="stylesheet" href="assets/css/style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >
</head>

<body>

<header class="header">

    <nav class="navbar">

        <a href="index.php" class="logo">
            Martial Arts Hub
        </a>

        <ul class="nav-links">

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


        <div class="auth-buttons">

            <?php if (isset($_SESSION["user_id"])): ?>

                <?php if (
                    isset($_SESSION["role"]) &&
                    $_SESSION["role"] === "admin"
                ): ?>

                    <a href="admin/admin.php" class="login-btn">
                        Admin
                    </a>

                <?php endif; ?>


                <a href="user/account.php" class="login-btn">
                    👤 My Account
                </a>

                <a href="auth/logout.php" class="signup-btn">
                    Logout
                </a>

            <?php else: ?>

                <a href="auth/login.php" class="login-btn">
                    Login
                </a>

                <a href="auth/signup.php" class="signup-btn">
                    Sign Up
                </a>

            <?php endif; ?>

        </div>

    </nav>

</header>


<section class="hero">

    <div class="hero-content">

        <h2>Master Every Fight.</h2>

        <p>
            Discover the latest news, techniques, fighter stories,
            and training guides from the world of Martial Arts.
        </p>

    </div>

</section>


<section class="featured">

    <div class="section-title">

        <p>DISCOVER</p>

        <h2>Featured Articles</h2>

        <span>
            Explore stories, techniques and insights from the martial arts world.
        </span>

    </div>


    <div class="article-grid">

        <?php while ($article = $result->fetch_assoc()): ?>

            <article class="article-card">

                <img
                    src="<?php echo htmlspecialchars($article["image"]); ?>"
                    alt="<?php echo htmlspecialchars($article["title"]); ?>"
                >

                <div class="article-content">

                    <span class="category">
                        <?php echo htmlspecialchars($article["category"]); ?>
                    </span>

                    <h3>
                        <?php echo htmlspecialchars($article["title"]); ?>
                    </h3>

                    <p>
                        <?php echo htmlspecialchars($article["content"]); ?>
                    </p>

                    <a href="article.php?id=<?php echo (int)$article["id"]; ?>">
                        Read Article →
                    </a>


                    <?php if (isset($_SESSION["user_id"])): ?>

                        <form
                            action="user/save-article.php"
                            method="POST"
                        >

                            <input
                                type="hidden"
                                name="article_id"
                                value="<?php echo (int)$article["id"]; ?>"
                            >

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?php echo htmlspecialchars($_SESSION["csrf_token"]); ?>"
                            >

                            <button
                                type="submit"
                                class="save-btn"
                            >
                                📌 Save Article
                            </button>

                        </form>

                    <?php else: ?>

                        <a
                            href="auth/login.php"
                            class="save-btn"
                        >
                            📌 Login to Save
                        </a>

                    <?php endif; ?>

                </div>

            </article>

        <?php endwhile; ?>

    </div>

</section>


<!-- CATEGORIES -->

<section class="categories" id="categories">

    <div class="section-title">

        <p>EXPLORE</p>

        <h2>Martial Arts Categories</h2>

        <span>
            Choose your discipline and discover something new.
        </span>

    </div>


    <div class="category-grid">

        <a
            href="categories.php?category=BOXING"
            class="category-card"
        >
            <div class="category-icon">🥊</div>
            <h3>Boxing</h3>
            <p>Footwork, punches and fighting techniques.</p>
        </a>


        <a
            href="categories.php?category=MMA"
            class="category-card"
        >
            <div class="category-icon">🥋</div>
            <h3>MMA</h3>
            <p>The world of mixed martial arts.</p>
        </a>


        <a
            href="categories.php?category=MUAY%20THAI"
            class="category-card"
        >
            <div class="category-icon">🦵</div>
            <h3>Muay Thai</h3>
            <p>The art of eight limbs and powerful striking.</p>
        </a>


        <a
            href="categories.php?category=BJJ"
            class="category-card"
        >
            <div class="category-icon">🤼</div>
            <h3>Brazilian Jiu-Jitsu</h3>
            <p>Grappling, submissions and ground fighting.</p>
        </a>


        <a
            href="categories.php?category=KARATE"
            class="category-card"
        >
            <div class="category-icon">🥋</div>
            <h3>Karate</h3>
            <p>Discipline, precision and traditional techniques.</p>
        </a>

    </div>

</section>


<!-- LATEST ARTICLES -->

<section class="latest-articles">

    <div class="article-grid">

        <?php

        $fallbackImages = [
            "assets/images/latest1.jpg",
            "assets/images/latest2.jpg",
            "assets/images/latest3.jpg",
            "assets/images/article1.jpg",
            "assets/images/article2.jpg",
            "assets/images/article3.jpg"
        ];

        ?>


        <?php foreach ($newsArticles as $index => $article): ?>

            <?php

            // Pick a different fallback image for each card
            $fallbackImage = $fallbackImages[
                $index % count($fallbackImages)
            ];

            // Use API image if available
            if (!empty($article["urlToImage"])) {

                $articleImage = $article["urlToImage"];

            } else {

                $articleImage = $fallbackImage;
            }

            ?>


            <article class="article-card">

                <img
                    src="<?php echo htmlspecialchars($articleImage); ?>"
                    alt="<?php echo htmlspecialchars($article["title"]); ?>"
                    onerror="this.onerror=null; this.src='<?php echo htmlspecialchars($fallbackImage); ?>';"
                >

                <div class="article-content">

                    <h3>
                        <?php echo htmlspecialchars($article["title"]); ?>
                    </h3>


                    <?php if (!empty($article["description"])): ?>

                        <p>
                            <?php echo htmlspecialchars($article["description"]); ?>
                        </p>

                    <?php endif; ?>


                    <small>
                        <?php
                        echo htmlspecialchars(
                            $article["source"]["name"] ?? "News"
                        );
                        ?>
                    </small>


                    <a
                        href="<?php echo htmlspecialchars($article["url"]); ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        Read Article →
                    </a>

                </div>

            </article>

        <?php endforeach; ?>

    </div>

</section>


<section class="fighters" id="fighters">

    <div class="section-title">

        <p>THE FIGHTERS</p>

        <h2>Find UFC Fighters</h2>

        <span>
            Search fighters and explore their records, divisions and profiles.
        </span>

    </div>


    <div class="fighter-search">

        <h3>Search Fighter Database</h3>

        <p>
            Enter a UFC fighter name to view their profile and record.
        </p>


        <form
            action="fighters.php"
            method="GET"
        >

            <input
                type="text"
                name="search"
                placeholder="Enter fighter name..."
                required
            >

            <button type="submit">
                Search Fighter
            </button>

        </form>

    </div>

</section>


<!-- ABOUT -->

<section class="about-section" id="about">

    <div class="about-container">

        <div class="about-image reveal-left">

            <img
                src="assets/images/about.jpg"
                alt="Martial arts training"
            >

            <div class="about-image-overlay"></div>

        </div>


        <div class="about-content reveal-right">

            <p class="about-label">
                ABOUT US
            </p>

            <h2>
                More Than Fighting.
                <span>A Martial Arts Community.</span>
            </h2>

            <p>
                Martial Arts Hub is a platform for combat sports fans
                to discover martial arts articles, explore UFC fighter
                profiles and stay updated with the latest MMA news.
            </p>

            <p>
                Whether you follow MMA, Boxing, Muay Thai, BJJ or Karate,
                our goal is to bring useful martial arts content together
                in one place.
            </p>


            <div class="about-features">

                <div>
                    <strong>🥋</strong>
                    <span>Martial Arts Articles</span>
                </div>

                <div>
                    <strong>🥊</strong>
                    <span>Fighter Profiles</span>
                </div>

                <div>
                    <strong>📰</strong>
                    <span>Latest MMA News</span>
                </div>

            </div>


            <a
                href="categories.php"
                class="about-btn"
            >
                Explore Articles →
            </a>

        </div>

    </div>

</section>


<!-- FOOTER -->

<footer class="footer">

    <div class="footer-container">


        <!-- Brand -->

        <div class="footer-brand">

            <h2>

                <a href="index.php">
                    Martial Arts Hub
                </a>

            </h2>

            <p>
                Your source for martial arts articles, fighter profiles,
                training insights and combat sports news.
            </p>

        </div>



        <!-- Quick Links -->

        <div class="footer-column">

            <h3>Quick Links</h3>

            <a href="index.php">
                Home
            </a>

            <a href="categories.php">
                Articles
            </a>

            <a href="fighters.php">
                Fighters
            </a>

            <a href="index.php#categories">
                Categories
            </a>

            <a href="index.php#about">
                About
            </a>

        </div>



        <!-- Categories -->

        <div class="footer-column">

            <h3>Categories</h3>

            <a href="categories.php?category=MMA">
                MMA
            </a>

            <a href="categories.php?category=BOXING">
                Boxing
            </a>

            <a href="categories.php?category=MUAY%20THAI">
                Muay Thai
            </a>

            <a href="categories.php?category=BJJ">
                Brazilian Jiu-Jitsu
            </a>

            <a href="categories.php?category=KARATE">
                Karate
            </a>

        </div>



        <!-- Account -->

        <div class="footer-column">

            <h3>Account</h3>


            <?php if (isset($_SESSION["user_id"])): ?>

                <a href="user/account.php">
                    My Account
                </a>


                <?php if (
                    isset($_SESSION["role"]) &&
                    $_SESSION["role"] === "admin"
                ): ?>

                    <a href="admin/admin.php">
                        Admin Dashboard
                    </a>

                <?php endif; ?>


                <a href="auth/logout.php">
                    Logout
                </a>

            <?php else: ?>

                <a href="auth/login.php">
                    Login
                </a>

                <a href="auth/signup.php">
                    Sign Up
                </a>

            <?php endif; ?>


            <a href="index.php#about">
                About Us
            </a>

        </div>

    </div>



    <div class="footer-bottom">

        <p>
            © 2026 Martial Arts Hub. All rights reserved.
        </p>

        <p>
            Built with HTML, CSS, JavaScript, PHP & MySQL.
        </p>

    </div>

</footer>

</body>
</html>
