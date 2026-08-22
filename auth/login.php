<?php

session_start();

require_once "../config/database.php";

$message = "";


// ========================================
// BASIC LOGIN RATE LIMITING
// ========================================

$max_attempts = 5;
$lockout_time = 60; // 60 seconds


// Create session values if they do not exist
if (!isset($_SESSION["login_attempts"])) {
    $_SESSION["login_attempts"] = 0;
}

if (!isset($_SESSION["login_lockout_time"])) {
    $_SESSION["login_lockout_time"] = 0;
}


// Check whether the user is currently locked out
$is_locked = false;

if (
    $_SESSION["login_attempts"] >= $max_attempts &&
    time() < $_SESSION["login_lockout_time"]
) {

    $is_locked = true;

    $remaining_time =
        $_SESSION["login_lockout_time"] - time();

    $message =
        "Too many failed login attempts. Please try again in "
        . $remaining_time
        . " seconds.";
}


// Reset lockout after the time has passed
if (
    $_SESSION["login_attempts"] >= $max_attempts &&
    time() >= $_SESSION["login_lockout_time"]
) {

    $_SESSION["login_attempts"] = 0;
    $_SESSION["login_lockout_time"] = 0;
}


// ========================================
// LOGIN
// ========================================

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    !$is_locked
) {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";


    // Check empty fields
    if ($email === "" || $password === "") {

        $message = "Please enter your email and password.";

    }


    // Validate email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";

    }


    else {

        $sql = "SELECT
                    id,
                    name,
                    email,
                    password,
                    role
                FROM users
                WHERE email = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();


        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();


            // Correct password
            if (
                password_verify(
                    $password,
                    $user["password"]
                )
            ) {

                // Protect against session fixation
                session_regenerate_id(true);


                // Store user information in session
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_email"] = $user["email"];
                $_SESSION["role"] = $user["role"];


                // Reset failed login attempts
                $_SESSION["login_attempts"] = 0;
                $_SESSION["login_lockout_time"] = 0;


                // Redirect depending on role
                if ($user["role"] === "admin") {

                    header(
                        "Location: ../admin/admin.php"
                    );
                    exit();

                } else {

                    header(
                        "Location: ../index.php"
                    );
                    exit();

                }

            }

        }


        // ========================================
        // FAILED LOGIN
        // ========================================

        $_SESSION["login_attempts"]++;


        // Lock after too many attempts
        if (
            $_SESSION["login_attempts"] >=
            $max_attempts
        ) {

            $_SESSION["login_lockout_time"] =
                time() + $lockout_time;

            $message =
                "Too many failed login attempts. "
                . "Please try again in 60 seconds.";

        } else {

            $remaining_attempts =
                $max_attempts -
                $_SESSION["login_attempts"];

            $message =
                "Incorrect email or password. "
                . $remaining_attempts
                . " attempts remaining.";

        }


        $stmt->close();
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
        Login - Martial Arts Hub
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body>

    <div class="signup-container">

        <div class="signup-box">

            <h1>Welcome Back</h1>

            <p>
                Login to Martial Arts Hub
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

                <label>Email</label>

                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    autocomplete="email"
                    required
                >


                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    placeholder="Enter your password"
                    autocomplete="current-password"
                    required
                >


                <button
                    type="submit"
                    <?php
                    echo $is_locked
                        ? "disabled"
                        : "";
                    ?>
                >
                    Login
                </button>

            </form>


            <p class="login-link">

                Don't have an account?

                <a href="signup.php">
                    Sign Up
                </a>

            </p>

        </div>

    </div>

</body>

</html>