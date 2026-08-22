<?php

require_once "../config/database.php";

$message = "";


// ========================================
// SIGNUP
// ========================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = strtolower(trim($_POST["email"] ?? ""));
    $password = $_POST["password"] ?? "";


    // ========================================
    // VALIDATION
    // ========================================

    if ($name === "" || $email === "" || $password === "") {

        $message = "Please fill in all fields.";

    } elseif (strlen($name) < 2 || strlen($name) > 100) {

        $message = "Please enter a valid name.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";

    } elseif (strlen($password) < 8) {

        $message = "Password must be at least 8 characters long.";

    } elseif (
        !preg_match("/[A-Za-z]/", $password) ||
        !preg_match("/[0-9]/", $password)
    ) {

        $message = "Password must contain at least one letter and one number.";

    } else {


        // ========================================
        // CHECK IF EMAIL ALREADY EXISTS
        // ========================================

        $check_sql = "SELECT id
                      FROM users
                      WHERE email = ?";

        $check_stmt = $conn->prepare($check_sql);

        $check_stmt->bind_param("s", $email);

        $check_stmt->execute();

        $check_result = $check_stmt->get_result();


        if ($check_result->num_rows > 0) {

            $message = "An account with this email already exists.";

            $check_stmt->close();

        } else {

            $check_stmt->close();


            // ========================================
            // HASH PASSWORD
            // ========================================

            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );


            // ========================================
            // CREATE ACCOUNT
            // ========================================

            $sql = "INSERT INTO users
                        (name, email, password)
                    VALUES (?, ?, ?)";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "sss",
                $name,
                $email,
                $hashed_password
            );


            if ($stmt->execute()) {

                $message =
                    "Account created successfully! You can now login.";

            } else {

                // Do not expose database errors to the user
                $message =
                    "Unable to create account. Please try again.";

            }

            $stmt->close();
        }
    }
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

    <title>
        Sign Up - Martial Arts Hub
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body>

    <div class="signup-container">

        <div class="signup-box">

            <h1>
                Join Martial Arts Hub
            </h1>

            <p>
                Create your account
            </p>


            <?php if ($message !== ""): ?>

                <div class="signup-message">

                    <?php
                    echo htmlspecialchars($message);
                    ?>

                </div>

            <?php endif; ?>


            <form
                method="POST"
                action=""
            >

                <label>
                    Full Name
                </label>

                <input
                    type="text"
                    name="name"
                    placeholder="Enter your name"
                    autocomplete="name"
                    minlength="2"
                    maxlength="100"
                    required
                >


                <label>
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    autocomplete="email"
                    required
                >


                <label>
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    placeholder="Minimum 8 characters"
                    autocomplete="new-password"
                    minlength="8"
                    required
                >


                <button type="submit">
                    Create Account
                </button>

            </form>


            <p class="login-link">

                Already have an account?

                <a href="login.php">
                    Login
                </a>

            </p>

        </div>

    </div>

</body>

</html>