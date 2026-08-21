<?php

session_start();

require_once "../config/database.php";


// Protect admin page
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
   header("Location: ../index.php");
    exit();
}


// Get all users
$result = $conn->query("
    SELECT id, name, email, role, created_at
    FROM users
    ORDER BY created_at DESC
");

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Users - Martial Arts Hub</title>

    <link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

<div class="admin-container">

    <header class="admin-header">

        <div>
            <h1>Registered Users</h1>
            <p>View users registered on Martial Arts Hub.</p>
        </div>

        <a href="admin.php" class="admin-back">
            ← Dashboard
        </a>

    </header>


    <div class="admin-table-wrapper">

        <table class="admin-table">

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                </tr>

            </thead>

            <tbody>

            <?php if ($result->num_rows > 0): ?>

                <?php while ($user = $result->fetch_assoc()): ?>

                    <tr>

                        <td>
                            <?php echo $user["id"]; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($user["name"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($user["email"]); ?>
                        </td>

                        <td>

                            <?php if ($user["role"] === "admin"): ?>

                                <span class="role-admin">
                                    Admin
                                </span>

                            <?php else: ?>

                                <span class="role-user">
                                    User
                                </span>

                            <?php endif; ?>

                        </td>

                        <td>
                            <?php echo date("d M Y", strtotime($user["created_at"])); ?>
                        </td>

                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="5" class="empty-message">
                        No users found.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>

</html>