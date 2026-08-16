<?php

require_once "config/database.php";


// Allowed categories
$allowed_categories = [
    "MMA",
    "MUAY THAI",
    "BJJ",
    "BOXING",
    "KARATE"
];


// Get selected category
$category = strtoupper(trim($_GET["category"] ?? ""));


// If valid category selected
if ($category !== "" && in_array($category, $allowed_categories, true)) {

    $stmt = $conn->prepare(
        "SELECT *
         FROM articles
         WHERE UPPER(TRIM(category)) = ?
         ORDER BY created_at DESC"
    );

    $stmt->bind_param("s", $category);

    $stmt->execute();

    $result = $stmt->get_result();

} else {

    // Show all articles
    $category = "";

    $result = $conn->query(
        "SELECT *
         FROM articles
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

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>


<!-- NAVBAR -->

<header class="categories-header">

    <nav class="categories-navbar">

        <a href="index.php" class="categories-logo">
            Martial Arts Hub
        </a>


        <ul class="categories-nav">

            <li>
                <a href="index.php">Home</a>
            </li>

            <li>
                <a href="categories.php">Categories</a>
            </li>

            <li>
                <a href="fighters.php">Fighters</a>
            </li>

            <li>
                <a href="index.php#about">About</a>
            </li>

        </ul>

    </nav>

</header>



<main class="categories-main">


    <!-- TITLE -->

    <div class="categories-heading">

        <p>EXPLORE</p>

        <h1>Martial Arts Categories</h1>

        <span>
            Explore articles from different martial arts.
        </span>

    </div>



    <!-- FILTER BUTTONS -->

    <div class="categories-filters">

        <a
            href="categories.php"
            class="<?php echo $category === "" ? "active" : ""; ?>"
        >
            All
        </a>


        <a
            href="categories.php?category=MMA"
            class="<?php echo $category === "MMA" ? "active" : ""; ?>"
        >
            MMA
        </a>


        <a
            href="categories.php?category=MUAY%20THAI"
            class="<?php echo $category === "MUAY THAI" ? "active" : ""; ?>"
        >
            Muay Thai
        </a>


        <a
            href="categories.php?category=BJJ"
            class="<?php echo $category === "BJJ" ? "active" : ""; ?>"
        >
            BJJ
        </a>


        <a
            href="categories.php?category=BOXING"
            class="<?php echo $category === "BOXING" ? "active" : ""; ?>"
        >
            Boxing
        </a>


        <a
            href="categories.php?category=KARATE"
            class="<?php echo $category === "KARATE" ? "active" : ""; ?>"
        >
            Karate
        </a>

    </div>



    <!-- ARTICLES -->

    <div class="categories-results-grid">

        <?php if ($result && $result->num_rows > 0): ?>


            <?php while ($article = $result->fetch_assoc()): ?>

                <article class="categories-article-card">


                    <img
                        src="<?php echo htmlspecialchars($article["image"]); ?>"
                        alt="<?php echo htmlspecialchars($article["title"]); ?>"
                    >


                    <div class="categories-article-content">


                        <span class="categories-article-category">

                            <?php
                            echo htmlspecialchars(
                                $article["category"]
                            );
                            ?>

                        </span>


                        <h2>

                            <?php
                            echo htmlspecialchars(
                                $article["title"]
                            );
                            ?>

                        </h2>


                        <p>

                            <?php

                            echo htmlspecialchars(
                                substr($article["content"], 0, 150)
                            );

                            ?>...

                        </p>


                        <a
                            href="article.php?id=<?php echo $article["id"]; ?>"
                            class="categories-read-btn"
                        >
                            Read Article →
                        </a>


                    </div>

                </article>

            <?php endwhile; ?>


        <?php else: ?>


            <div class="categories-no-results">

                <h2>No articles found</h2>

                <p>
                    There are currently no articles in this category.
                </p>

                <a href="categories.php">
                    View All Articles
                </a>

            </div>


        <?php endif; ?>

    </div>


</main>


</body>

</html>