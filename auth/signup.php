<?php

require_once "../config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($name) || empty($email) || empty($password)) {

        $message = "Please fill in all fields.";

    } else {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, password)
                VALUES (?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {

            $message = "Account created successfully!";

        } else {

            $message = "Error: " . $stmt->error;

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

    <title>Sign Up - Martial Arts Hub</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

    <div class="signup-container">

        <div class="signup-box">

            <h1>Join Martial Arts Hub</h1>

            <p>Create your account</p>

            <?php if ($message != ""): ?>

                <div class="signup-message">
                    <?php echo htmlspecialchars($message); ?>
                </div>

            <?php endif; ?>


            <form method="POST" action="">

                <label>Full Name</label>

                <input
                    type="text"
                    name="name"
                    placeholder="Enter your name"
                    required
                >


                <label>Email</label>

                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >


                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    placeholder="Create a password"
                    required
                >


                <button type="submit">
                    Create Account
                </button>

            </form>


            <p class="login-link">

                Already have an account?

                <a href="login.php">Login</a>

            </p>

        </div>

    </div>

</body>

</html>