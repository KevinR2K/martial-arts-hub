<?php

require_once "config/database.php";

$category = $_GET["category"] ?? "";

if ($category !== "") {

    $stmt = $conn->prepare(
        "SELECT * FROM articles
         WHERE category = ?
         ORDER BY created_at DESC"
    );

    $stmt->bind_param("s", $category);

    $stmt->execute();

    $result = $stmt->get_result();

} else {

    $result = $conn->query(
        "SELECT * FROM articles
         ORDER BY created_at DESC"
    );

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Categories - Martial Arts Hub</title>

    <link rel="stylesheet"
          href="assets/css/style.css">

</head>

<body>

    <header>

        <nav class="navbar">

            <h1>Martial Arts Hub</h1>

            <ul>

                <li><a href="index.php">Home</a></li>

                <li><a href="categories.php">
                    Categories
                </a></li>

                <li><a href="index.php">
                    Fighters
                </a></li>

                <li><a href="index.php">
                    About
                </a></li>

            </ul>

        </nav>

    </header>


    <main class="categories-page">

        <div class="section-title">

            <p>EXPLORE</p>

            <h2>
                Martial Arts Categories
            </h2>

            <span>
                Explore articles from different martial arts.
            </span>

        </div>


        <div class="category-buttons">

            <a href="categories.php">
                All
            </a>

            <a href="categories.php?category=MMA">
                MMA
            </a>

            <a href="categories.php?category=MUAY%20THAI">
                Muay Thai
            </a>

            <a href="categories.php?category=BJJ">
                BJJ
            </a>

            <a href="categories.php?category=BOXING">
                Boxing
            </a>

            <a href="categories.php?category=KARATE">
                Karate
            </a>

        </div>


        <div class="article-grid">

            <?php if ($result->num_rows > 0): ?>

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

                                <?php

                                echo htmlspecialchars(
                                    substr($article["content"], 0, 150)
                                );

                                ?>...

                            </p>

                            <a href="article.php?id=<?php echo $article["id"]; ?>">

                                Read Article →

                            </a>

                        </div>

                    </article>

                <?php endwhile; ?>

            <?php else: ?>

                <p>No articles found in this category.</p>

            <?php endif; ?>

        </div>

    </main>

</body>

</html>