<?php

session_start();

require_once "../config/database.php";


// =====================================
// ADMIN PROTECTION
// =====================================

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {

    header("Location: ../index.php");
    exit();
}


// =====================================
// GET ARTICLE ID
// =====================================

$article_id = (int)($_GET["id"] ?? 0);

if ($article_id <= 0) {

    echo "Invalid article ID.";
    exit();
}


// =====================================
// GET CURRENT ARTICLE
// =====================================

$sql = "
    SELECT *
    FROM articles
    WHERE id = ?
";

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


$message = "";


// =====================================
// UPDATE ARTICLE
// =====================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $content = trim($_POST["content"] ?? "");

    $is_featured = isset($_POST["is_featured"]) ? 1 : 0;


    // Keep current image by default
    $image = $article["image"];


    // =====================================
    // OPTIONAL NEW IMAGE UPLOAD
    // =====================================

    if (
        isset($_FILES["image"]) &&
        $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {

        if ($_FILES["image"]["error"] === UPLOAD_ERR_OK) {

            $file = $_FILES["image"];


            // Maximum image size = 5MB
            $max_size = 5 * 1024 * 1024;


            if ($file["size"] > $max_size) {

                $message = "Image must be smaller than 5MB.";

            } else {

                // Check real MIME type
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


                    // Create unique filename
                    $filename =
                        "article_" .
                        time() .
                        "_" .
                        bin2hex(random_bytes(4)) .
                        "." .
                        $extension;


                    /*
                     * edit-article.php is inside admin/
                     *
                     * dirname(__DIR__) takes us back to:
                     * martial-arts-hub/
                     */
                    $upload_folder =
                        dirname(__DIR__) .
                        DIRECTORY_SEPARATOR .
                        "uploads" .
                        DIRECTORY_SEPARATOR .
                        "articles" .
                        DIRECTORY_SEPARATOR;


                    // Create folder if it does not exist
                    if (!is_dir($upload_folder)) {

                        if (!mkdir($upload_folder, 0777, true)) {

                            die("Could not create articles upload folder.");
                        }
                    }


                    // Physical location
                    $destination =
                        $upload_folder .
                        $filename;


                    // Move uploaded image
                    if (
                        move_uploaded_file(
                            $file["tmp_name"],
                            $destination
                        )
                    ) {

                        // New public image path for MySQL
                        $image =
                            "uploads/articles/" .
                            $filename;

                    } else {

                        $message = "Image upload failed.";
                    }
                }
            }

        } else {

            $message = "There was a problem uploading the image.";
        }
    }


    // =====================================
    // UPDATE DATABASE
    // =====================================

    if ($message === "") {

        $sql = "
            UPDATE articles
            SET
                title = ?,
                category = ?,
                content = ?,
                image = ?,
                is_featured = ?
            WHERE id = ?
        ";

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

            $stmt->close();

            header("Location: manage-articles.php");
            exit();

        } else {

            $message = "Error updating article.";
        }


        $stmt->close();
    }


    // Keep submitted values visible if an error occurs
    $article["title"] = $title;
    $article["category"] = $category;
    $article["content"] = $content;
    $article["image"] = $image;
    $article["is_featured"] = $is_featured;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Article - Admin</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

    <link
        rel="stylesheet"
        href="../assets/css/admin.css"
    >

</head>

<body>

<div class="admin-container">


    <!-- HEADER -->

    <header class="admin-header">

        <div>

            <h1>Edit Article</h1>

            <p>
                Update article information and content.
            </p>

        </div>

        <a
            href="manage-articles.php"
            class="admin-back"
        >
            ← Manage Articles
        </a>

    </header>


    <!-- MESSAGE -->

    <?php if ($message): ?>

        <div class="admin-message">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <!-- FORM -->

    <div class="admin-form-card">

        <form
            method="POST"
            enctype="multipart/form-data"
            class="article-form"
        >


            <!-- TITLE -->

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

                    <option
                        value="MMA"
                        <?php if ($article["category"] === "MMA") echo "selected"; ?>
                    >
                        MMA
                    </option>


                    <option
                        value="MUAY THAI"
                        <?php if ($article["category"] === "MUAY THAI") echo "selected"; ?>
                    >
                        Muay Thai
                    </option>


                    <option
                        value="BJJ"
                        <?php if ($article["category"] === "BJJ") echo "selected"; ?>
                    >
                        BJJ
                    </option>


                    <option
                        value="BOXING"
                        <?php if ($article["category"] === "BOXING") echo "selected"; ?>
                    >
                        Boxing
                    </option>


                    <option
                        value="KARATE"
                        <?php if ($article["category"] === "KARATE") echo "selected"; ?>
                    >
                        Karate
                    </option>

                </select>

            </div>


            <!-- CURRENT IMAGE -->

            <div class="form-group">

                <label>
                    Current Image
                </label>

                <small>
                    <?php echo htmlspecialchars($article["image"]); ?>
                </small>

            </div>


            <!-- NEW IMAGE -->

            <div class="form-group">

                <label for="image">
                    Replace Image
                </label>

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept=".jpg,.jpeg,.png,.webp"
                >

                <small>
                    Leave this empty to keep the current image.
                    JPG, PNG or WEBP. Maximum 5 MB.
                </small>

            </div>


            <!-- CONTENT -->

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


            <!-- FEATURED -->

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


            <!-- BUTTONS -->

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