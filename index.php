<?php
session_start();
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
     <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <h1 class="logo">Martial Arts Hub</h1>
            <ul class="nav-links">
                <li><a href="#">Home</a></li>
                <li><a href="#">Articales</a></li>
                <li><a href="#">Fighters</a></li>
                <li><a href="categories.php">Categories</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Login</a></li>
                <li><a href="#">Signup</a></li>
            </ul>

              <div class="auth-buttons">

    <?php if (isset($_SESSION["user_id"])): ?>

        <a href="account.php" class="login-btn">
            👤 My Account
        </a>

        <a href="logout.php" class="signup-btn">
            Logout
        </a>

    <?php else: ?>

        <a href="login.php" class="login-btn">
            Login
        </a>

        <a href="signup.php" class="signup-btn">
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

        <a href="#" class="hero-btn">Explore Articles</a>

    </div>

</section>
<section class="featured">

    <div class="section-title">
        <p>DISCOVER</p>
        <h2>Featured Articles</h2>
        <span>Explore stories, techniques and insights from the martial arts world.</span>
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

                <a href="article.php?id=<?php echo $article["id"]; ?>">
    Read Article →
</a>

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

        </article>

    <?php endwhile; ?>

</div>

</section>
<!-- CATEGORIES -->

<section class="categories">

    <div class="section-title">
        <p>EXPLORE</p>
        <h2>Martial Arts Categories</h2>
        <span>
            Choose your discipline and discover something new.
        </span>
    </div>

    <div class="category-grid">

        <a href="#" class="category-card">
            <div class="category-icon">🥊</div>
            <h3>Boxing</h3>
            <p>Footwork, punches and fighting techniques.</p>
        </a>

        <a href="#" class="category-card">
            <div class="category-icon">🥋</div>
            <h3>MMA</h3>
            <p>The world of mixed martial arts.</p>
        </a>

        <a href="#" class="category-card">
            <div class="category-icon">🦵</div>
            <h3>Muay Thai</h3>
            <p>The art of eight limbs and powerful striking.</p>
        </a>

        <a href="#" class="category-card">
            <div class="category-icon">🤼</div>
            <h3>Brazilian Jiu-Jitsu</h3>
            <p>Grappling, submissions and ground fighting.</p>
        </a>

        <a href="#" class="category-card">
            <div class="category-icon">🥋</div>
            <h3>Karate</h3>
            <p>Discipline, precision and traditional techniques.</p>
        </a>

        <a href="#" class="category-card">
            <div class="category-icon">👊</div>
            <h3>Kickboxing</h3>
            <p>Powerful combinations and striking techniques.</p>
        </a>

    </div>

</section>
<!-- LATEST ARTICLES -->

<section class="latest-articles">

    <div class="article-grid">

        <?php foreach ($newsArticles as $article): ?>

            <article class="article-card">

                <?php if (!empty($article["urlToImage"])): ?>

                    <img
                        src="<?php echo htmlspecialchars($article["urlToImage"]); ?>"
                        alt="<?php echo htmlspecialchars($article["title"]); ?>"
                           onerror="this.onerror=null; this.src='assets/images/latest1.jpg';"
                    >

                <?php endif; ?>

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
                        <?php echo htmlspecialchars($article["source"]["name"] ?? "News"); ?>
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
<!-- FIGHTERS -->

<section class="fighters">

    <div class="section-title">
        <p>THE FIGHTERS</p>
        <h2>Featured Fighters</h2>
        <span>
            Discover some of the biggest names in combat sports.
        </span>
    </div>

    <div class="fighter-grid">

        <div class="fighter-card">

            <img src="assets/images/fighter1.jpg" alt="MMA fighter">

            <div class="fighter-info">
                <span>MMA</span>
                <h3>Featured Fighter</h3>
                <p>Mixed Martial Arts</p>

                <a href="#">View Profile →</a>
            </div>

        </div>


        <div class="fighter-card">

            <img src="assets/images/fighter2.jpg" alt="Boxing fighter">

            <div class="fighter-info">
                <span>BOXING</span>
                <h3>Featured Fighter</h3>
                <p>Professional Boxing</p>

                <a href="#">View Profile →</a>
            </div>

        </div>


        <div class="fighter-card">

            <img src="assets/images/fighter3.jpg" alt="Kickboxing fighter">

            <div class="fighter-info">
                <span>KICKBOXING</span>
                <h3>Featured Fighter</h3>
                <p>Professional Kickboxing</p>

                <a href="#">View Profile →</a>
            </div>

        </div>

    </div>

    <!-- Fighter Search -->

    <div class="fighter-search">

        <h3>Looking for a specific fighter?</h3>

        <p>Search our fighter database.</p>

        <form>
            <input
                type="text"
                placeholder="Search fighter..."
            >

            <button type="submit">Search</button>
        </form>

    </div>

</section>
<!-- ABOUT -->

<section class="about">

    <div class="about-image">
        <img src="assets/images/about.jpg" alt="Martial arts training">
    </div>

    <div class="about-content">

        <p class="about-label">ABOUT US</p>

        <h2>Your Source for the World of Martial Arts.</h2>

        <p>
            Martial Arts Hub is a platform created for people who
            are passionate about combat sports and martial arts.
        </p>

        <p>
            Explore articles, fighter profiles, training insights,
            techniques and the latest news from the world of martial arts.
        </p>

        <div class="about-features">

            <div>
                <strong>01</strong>
                <span>Martial Arts Articles</span>
            </div>

            <div>
                <strong>02</strong>
                <span>Fighter Profiles</span>
            </div>

            <div>
                <strong>03</strong>
                <span>Latest Combat Sports News</span>
            </div>

        </div>

    </div>

</section>
<!-- NEWSLETTER -->

<section class="newsletter">

    <div class="newsletter-content">

        <p>STAY IN THE FIGHT</p>

        <h2>Never Miss an Update.</h2>

        <span>
            Get the latest martial arts news, articles and fighter
            stories delivered to your inbox.
        </span>

        <form class="newsletter-form">

            <input
                type="email"
                placeholder="Enter your email address"
                required
            >

            <button type="submit">
                Subscribe
            </button>

        </form>

    </div>

</section>
<!-- FOOTER -->

<footer class="footer">

    <div class="footer-container">

        <!-- Brand -->

        <div class="footer-brand">

            <h2>Martial Arts Hub</h2>

            <p>
                Your source for martial arts articles, fighter stories,
                training insights and combat sports news.
            </p>

        </div>


        <!-- Quick Links -->

        <div class="footer-column">

            <h3>Quick Links</h3>

            <a href="#">Home</a>
            <a href="#">Articles</a>
            <a href="#">Fighters</a>
            <a href="#">Categories</a>

        </div>


        <!-- Categories -->

        <div class="footer-column">

            <h3>Categories</h3>

            <a href="#">MMA</a>
            <a href="#">Boxing</a>
            <a href="#">Muay Thai</a>
            <a href="#">Brazilian Jiu-Jitsu</a>

        </div>


        <!-- Account -->

        <div class="footer-column">

            <h3>Account</h3>

            <a href="login.php">Login</a>
            <a href="signup.php">Sign Up</a>
            <a href="#">About Us</a>
            <a href="#">Contact</a>

        </div>

    </div>


    <div class="footer-bottom">

        <p>
            © 2026 Martial Arts Hub. All rights reserved.
        </p>

        <p>
            Built with HTML, CSS, JavaScript & PHP.
        </p>

    </div>

</footer>
    
</body>
</html>
