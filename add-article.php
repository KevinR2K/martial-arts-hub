<?php

session_start();

require_once "config/database.php";


// Only admins can access this page
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {

    header("Location: index.php");
    exit();

}


$message = "";


// When the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"]);
    $category = trim($_POST["category"]);
    $content = trim($_POST["content"]);
    $image = trim($_POST["image"]);

    $is_featured = isset($_POST["is_featured"]) ? 1 : 0;


    // Insert article
    $sql = "INSERT INTO articles
            (title, category, content, image, is_featured)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssssi",
        $title,
        $category,
        $content,
        $image,
        $is_featured
    );


    if ($stmt->execute()) {

        $message = "Article added successfully!";

    } else {

        $message = "Error adding article.";

    }

    $stmt->close();

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Article - Admin</title>

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin.css">

</head>

<body>

<div class="admin-container">

    <header class="admin-header">

        <div>
            <h1>Add New Article</h1>
            <p>Create a new article for Martial Arts Hub.</p>
        </div>

        <a href="manage-articles.php" class="admin-back">
            ← Manage Articles
        </a>

    </header>


    <?php if ($message): ?>

        <div class="admin-message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <div class="admin-form-card">

        <form method="POST" class="article-form">

            <div class="form-group">

                <label for="title">Article Title</label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    placeholder="Enter article title"
                    required
                >

            </div>


            <div class="form-group">

                <label for="category">Category</label>

                <select id="category" name="category" required>

                    <option value="">Select Category</option>
                    <option value="MMA">MMA</option>
                    <option value="MUAY THAI">Muay Thai</option>
                    <option value="BJJ">BJJ</option>
                    <option value="BOXING">Boxing</option>
                    <option value="KARATE">Karate</option>

                </select>

            </div>


            <div class="form-group">

                <label for="image">Image Path</label>

                <input
                    type="text"
                    id="image"
                    name="image"
                    placeholder="assets/images/article1.jpg"
                    required
                >

                <small>
                    Example: assets/images/mma-training.jpg
                </small>

            </div>


            <div class="form-group">

                <label for="content">Article Content</label>

                <textarea
                    id="content"
                    name="content"
                    rows="12"
                    placeholder="Write your article..."
                    required
                ></textarea>

            </div>


            <label class="checkbox-label">

                <input
                    type="checkbox"
                    name="is_featured"
                >

                <span>Show this article in Featured Articles</span>

            </label>


            <div class="form-buttons">

                <button type="submit" class="admin-btn">
                    Add Article
                </button>

                <a href="manage-articles.php" class="cancel-btn">
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>

</body>

</html>