<?php

session_start();

require_once "../config/database.php";


// Protect admin page
if (
    !isset($_SESSION["user_id"]) ||
    ($_SESSION["role"] ?? "") !== "admin"
) {
    header("Location: ../index.php");
    exit();
}


// Create CSRF token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}


// Delete comment
if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["delete_comment"])
) {

    $comment_id = (int)($_POST["comment_id"] ?? 0);

    $csrf_token = $_POST["csrf_token"] ?? "";


    // Check CSRF token
    if (
        !hash_equals(
            $_SESSION["csrf_token"],
            $csrf_token
        )
    ) {
        http_response_code(403);
        exit("Invalid security token.");
    }


    if ($comment_id > 0) {

        $stmt = $conn->prepare("
            DELETE FROM comments
            WHERE id = ?
        ");

        $stmt->bind_param(
            "i",
            $comment_id
        );

        $stmt->execute();

        $stmt->close();
    }


    header(
        "Location: manage-comments.php"
    );

    exit();
}


// Get all comments
$result = $conn->query("
    SELECT
        comments.id,
        comments.comment,
        comments.created_at,
        users.name AS user_name,
        articles.title AS article_title
    FROM comments
    LEFT JOIN users
        ON comments.user_id = users.id
    LEFT JOIN articles
        ON comments.article_id = articles.id
    ORDER BY comments.created_at DESC
");

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Manage Comments - Martial Arts Hub
    </title>

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

    <header class="admin-header">

        <div>

            <h1>
                Manage Comments
            </h1>

            <p>
                View and manage comments posted by users.
            </p>

        </div>

        <a
            href="admin.php"
            class="admin-back"
        >
            ← Dashboard
        </a>

    </header>


    <div class="admin-table-wrapper">

        <table class="admin-table">

            <thead>

                <tr>

                    <th>User</th>
                    <th>Article</th>
                    <th>Comment</th>
                    <th>Date</th>
                    <th>Action</th>

                </tr>

            </thead>

            <tbody>


            <?php if ($result && $result->num_rows > 0): ?>


                <?php while ($comment = $result->fetch_assoc()): ?>


                    <tr>

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $comment["user_name"]
                                ?? "Unknown User"
                            );
                            ?>

                        </td>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $comment["article_title"]
                                ?? "Deleted Article"
                            );
                            ?>

                        </td>


                        <td class="comment-text">

                            <?php
                            echo htmlspecialchars(
                                $comment["comment"]
                            );
                            ?>

                        </td>


                        <td>

                            <?php
                            echo date(
                                "d M Y",
                                strtotime(
                                    $comment["created_at"]
                                )
                            );
                            ?>

                        </td>


                        <td>

                            <form
                                method="POST"
                                action=""
                                onsubmit="return confirm('Are you sure you want to delete this comment?');"
                            >

                                <input
                                    type="hidden"
                                    name="comment_id"
                                    value="<?php echo (int)$comment["id"]; ?>"
                                >

                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?php echo htmlspecialchars($_SESSION["csrf_token"]); ?>"
                                >

                                <button
                                    type="submit"
                                    name="delete_comment"
                                    class="admin-btn delete-btn"
                                >
                                    Delete
                                </button>

                            </form>

                        </td>

                    </tr>


                <?php endwhile; ?>


            <?php else: ?>


                <tr>

                    <td
                        colspan="5"
                        class="empty-message"
                    >
                        No comments found.
                    </td>

                </tr>


            <?php endif; ?>


            </tbody>

        </table>

    </div>

</div>

</body>

</html>