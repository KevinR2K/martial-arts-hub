<?php

session_start();

require_once "../config/database.php";


// Only admins can access this page
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {

    header("Location: ../index.php");
    exit();
}


$message = "";


// =====================================
// ADD ARTICLE
// =====================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $content = trim($_POST["content"] ?? "");

    $is_featured = isset($_POST["is_featured"]) ? 1 : 0;

    $image = "";


    // =====================================
    // IMAGE UPLOAD
    // =====================================

    if (
        isset($_FILES["image"]) &&
        $_FILES["image"]["error"] === UPLOAD_ERR_OK
    ) {

        $file = $_FILES["image"];

        // Maximum image size = 5MB
        $max_size = 5 * 1024 * 1024;


        if ($file["size"] > $max_size) {

            $message = "Image must be smaller than 5MB.";

        } else {

            // Check the real MIME type of the uploaded file
            $finfo = new finfo(FILEINFO_MIME_TYPE);

            $mime_type = $finfo->file($file["tmp_name"]);


            $allowed_types = [
                "image/jpeg" => "jpg",
                "image/png"  => "png",
                "image/webp" => "webp"
            ];


            if (!isset($allowed_types[$mime_type])) {

                $message = "Only JPG, PNG and WEBP images are allowed.";

            } else {

                $extension = $allowed_types[$mime_type];


                // Create a unique image filename
                $filename =
                    "article_" .
                    time() .
                    "_" .
                    bin2hex(random_bytes(4)) .
                    "." .
                    $extension;


                /*
                 * add-article.php is now inside:
                 *
                 * martial-arts-hub/admin/
                 *
                 * dirname(__DIR__) takes us back to:
                 *
                 * martial-arts-hub/
                 */
                $upload_folder =
                    dirname(__DIR__) .
                    DIRECTORY_SEPARATOR .
                    "uploads" .
                    DIRECTORY_SEPARATOR .
                    "articles" .
                    DIRECTORY_SEPARATOR;


                // Create uploads/articles if it does not exist
                if (!is_dir($upload_folder)) {

                    if (!mkdir($upload_folder, 0777, true)) {

                        die("Could not create articles upload folder.");
                    }
                }


                // Full physical location of the uploaded image
                $destination =
                    $upload_folder .
                    $filename;


                // Move uploaded image into uploads/articles/
                if (
                    move_uploaded_file(
                        $file["tmp_name"],
                        $destination
                    )
                ) {

                    /*
                     * Store the public path in MySQL.
                     *
                     * Example:
                     * uploads/articles/article_123.jpg
                     */
                    $image =
                        "uploads/articles/" .
                        $filename;

                } else {

                    $message = "Image upload failed.";
                }
            }
        }

    } else {

        $message = "Please select an article image.";
    }


    // =====================================
    // INSERT ARTICLE INTO DATABASE
    // =====================================

    if ($image !== "") {

        $sql = "
            INSERT INTO articles
            (
                title,
                category,
                content,
                image,
                is_featured
            )
            VALUES (?, ?, ?, ?, ?)
        ";

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
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Article - Admin</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">

</head>

<body>

<div class="admin-container">

    <header class="admin-header">

        <div>

            <h1>Add New Article</h1>

            <p>
                Create a new article for Martial Arts Hub.
            </p>

        </div>

        <a
            href="manage-articles.php"
            class="admin-back"
        >
            ← Manage Articles
        </a>

    </header>


    <?php if ($message): ?>

        <div class="admin-message">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <div class="admin-form-card">

        <form
            method="POST"
            enctype="multipart/form-data"
            class="article-form"
        >


            <!-- ARTICLE TITLE -->

            <div class="form-group">

                <label for="title">
                    Article Title
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    placeholder="Enter article title"
                    required
                >

            </div>


            <!-- CATEGORY -->

            <div class="form-group">

                <label for="category">
                    Category
                </label>

                <select
                    id="category"
                    name="category"
                    required
                >

                    <option value="">
                        Select Category
                    </option>

                    <option value="MMA">
                        MMA
                    </option>

                    <option value="MUAY THAI">
                        Muay Thai
                    </option>

                    <option value="BJJ">
                        BJJ
                    </option>

                    <option value="BOXING">
                        Boxing
                    </option>

                    <option value="KARATE">
                        Karate
                    </option>

                </select>

            </div>


            <!-- ARTICLE IMAGE -->

            <div class="form-group">

                <label for="image">
                    Article Image
                </label>

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept=".jpg,.jpeg,.png,.webp"
                    required
                >

                <small>
                    JPG, PNG or WEBP. Maximum 5 MB.
                </small>

            </div>


            <!-- ARTICLE CONTENT -->

            <div class="form-group">

                <label for="content">
                    Article Content
                </label>

                <textarea
                    id="content"
                    name="content"
                    rows="12"
                    placeholder="Write your article..."
                    required
                ></textarea>

            </div>


            <!-- FEATURED -->

            <label class="checkbox-label">

                <input
                    type="checkbox"
                    name="is_featured"
                >

                <span>
                    Show this article in Featured Articles
                </span>

            </label>


            <!-- BUTTONS -->

            <div class="form-buttons">

                <button
                    type="submit"
                    class="admin-btn"
                >
                    Add Article
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