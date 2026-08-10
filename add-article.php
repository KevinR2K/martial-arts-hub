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

</head>

<body>

    <div class="admin-container">

        <div class="admin-header">

            <div>

                <h1>Add New Article</h1>

                <p>Create a new article for Martial Arts Hub.</p>

            </div>

            <a href="admin.php" class="admin-back">
                ← Dashboard
            </a>

        </div>


        <?php if ($message): ?>

            <p class="admin-message">
                <?php echo htmlspecialchars($message); ?>
            </p>

        <?php endif; ?>


        <form method="POST" class="article-form">

            <label>Article Title</label>

            <input
                type="text"
                name="title"
                placeholder="Enter article title"
                required
            >


            <label>Category</label>

            <select name="category" required>

                <option value="">Select Category</option>

                <option value="MMA">MMA</option>

                <option value="MUAY THAI">Muay Thai</option>

                <option value="BJJ">BJJ</option>

                <option value="BOXING">Boxing</option>

                <option value="KARATE">Karate</option>

            </select>


            <label>Image Path</label>

            <input
                type="text"
                name="image"
                placeholder="assets/images/article1.jpg"
                required
            >


            <label>Article Content</label>

            <textarea
                name="content"
                rows="10"
                placeholder="Write your article..."
                required
            ></textarea>


            <label class="checkbox-label">

                <input
                    type="checkbox"
                    name="is_featured"
                >

                Featured Article

            </label>


            <button type="submit">
                Add Article
            </button>

        </form>

    </div>

</body>

</html>