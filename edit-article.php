<?php

session_start();

require_once "config/database.php";


// Admin protection
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.php");
    exit();
}


// Get article ID
$article_id = (int)($_GET["id"] ?? 0);


// Update article
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"]);
    $category = trim($_POST["category"]);
    $content = trim($_POST["content"]);
    $image = trim($_POST["image"]);

    $is_featured = isset($_POST["is_featured"]) ? 1 : 0;


    $sql = "UPDATE articles
            SET title = ?,
                category = ?,
                content = ?,
                image = ?,
                is_featured = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssssii",
        $title,
        $category,
        $content,
        $image,
        $is_featured,
        $article_id
    );


    if ($stmt->execute()) {

        header("Location: manage-articles.php");
        exit();

    }

    $stmt->close();
}


// Get current article
$sql = "SELECT *
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

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Article - Admin</title>

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin.css">

</head>

<body>

<div class="admin-container">

    <header class="admin-header">

        <div>
            <h1>Edit Article</h1>
            <p>Update article information and content.</p>
        </div>

        <a href="manage-articles.php" class="admin-back">
            ← Manage Articles
        </a>

    </header>


    <div class="admin-form-card">

        <form method="POST" class="article-form">


            <div class="form-group">

                <label for="title">
                    Article Title
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    value="<?php echo htmlspecialchars($article["title"]); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="category">
                    Category
                </label>

                <select
                    id="category"
                    name="category"
                    required
                >

                    <option value="MMA"
                        <?php if ($article["category"] === "MMA") echo "selected"; ?>>
                        MMA
                    </option>

                    <option value="MUAY THAI"
                        <?php if ($article["category"] === "MUAY THAI") echo "selected"; ?>>
                        Muay Thai
                    </option>

                    <option value="BJJ"
                        <?php if ($article["category"] === "BJJ") echo "selected"; ?>>
                        BJJ
                    </option>

                    <option value="BOXING"
                        <?php if ($article["category"] === "BOXING") echo "selected"; ?>>
                        Boxing
                    </option>

                    <option value="KARATE"
                        <?php if ($article["category"] === "KARATE") echo "selected"; ?>>
                        Karate
                    </option>

                </select>

            </div>


            <div class="form-group">

                <label for="image">
                    Image Path
                </label>

                <input
                    type="text"
                    id="image"
                    name="image"
                    value="<?php echo htmlspecialchars($article["image"]); ?>"
                    required
                >

                <small>
                    Example: assets/images/article1.jpg
                </small>

            </div>


            <div class="form-group">

                <label for="content">
                    Article Content
                </label>

                <textarea
                    id="content"
                    name="content"
                    rows="12"
                    required
                ><?php echo htmlspecialchars($article["content"]); ?></textarea>

            </div>


            <label class="checkbox-label">

                <input
                    type="checkbox"
                    name="is_featured"
                    <?php if ($article["is_featured"]) echo "checked"; ?>
                >

                <span>
                    Show this article in Featured Articles
                </span>

            </label>


            <div class="form-buttons">

                <button
                    type="submit"
                    class="admin-btn"
                >
                    Update Article
                </button>

                <a
                    href="manage-articles.php"
                    class="cancel-btn"
                >
                    Cancel
                </a>

            </div>


        </form>

    </div>

</div>

</body>

</html>