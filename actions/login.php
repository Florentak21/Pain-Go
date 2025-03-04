<?php
    # Include necessary files
    require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
    require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'functions.php';

    # Start a session with a lifetime of 5 minutes (300 seconds)
    ini_set('session.gc_maxlifetime', 300); // Server-side session lifetime
    session_set_cookie_params(300); // Client-side cookie lifetime
    session_start();

    # Set the page title dynamically
    $title = basename($_SERVER['PHP_SELF'], ".php");

    # Check if the session has expired
    if (isset($_SESSION['user_credentials']) && isset($_SESSION['last_activity'])) {
        $elapsed_time = time() - $_SESSION['last_activity'];
        if ($elapsed_time > 300) { // 30 minutes in seconds
            // Destroy the session and redirect to login page
            session_unset();
            session_destroy();
            header("Location: ../actions/login.php");
            exit();
        }
    }

    # Update the last activity timestamp
    $_SESSION['last_activity'] = time();

    # Process the form if it is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $errors = [];

        // Validate form fields
        $errors += validate($_POST['email'], 'email', 5, 255, 'email');
        $errors += validate($_POST['password'], 'password', 8, 255);

        // If no errors, proceed with user authentication
        if (empty($errors)) {
            $email = htmlspecialchars($_POST['email']);
            $password = $_POST['password'];

            // Fetch user data from the database
            $query = $database->prepare("SELECT * FROM users WHERE email = ?");
            $query->execute([$email]);
            $result = $query->fetch();

            // Verify password and log the user in
            if ($result && password_verify($password, $result['password'])) {
                $_SESSION['user_credentials'] = [
                    'firstname' => $result['firstname'],
                    'lastname' => $result['lastname'],
                    'email' => $result['email'],
                    'role' => $result['role']
                ];

                // Set the last activity timestamp
                $_SESSION['last_activity'] = time();

                // Redirect based on user role
                switch ($_SESSION['user_credentials']['role']) {
                    case 'admin':
                        header("Location: ../admin/index.php");
                        exit();
                    case 'user':
                        header("Location: ../pages/profile.php");
                        exit();
                }
            } else {
                // Set error message for invalid credentials
                $_SESSION['errors']['login'] = "Incorrect email or password!";
                header("Location: ../actions/login.php");
                exit();
            }
        } else {
            // Store errors and old input values in the session
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;

            // Redirect back to the login page
            header("Location: ../actions/login.php");
            exit();
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ucfirst($title) . ' Page' ?></title>
</head>
<body>
    <h1>Login to Pain'Go</h1>
    <?php if (!empty($_SESSION['errors']['login'])): ?>
        <p><?= $_SESSION['errors']['login'] ?></p>
    <?php endif; ?>
    <form method="POST">
        <!-- Email -->
        <input type="email" name="email" value="<?= $_SESSION['old']['email'] ?? '' ?>" placeholder="Enter your email" required>
        <?= displayError('email') ?>

        <!-- Password -->
        <input type="password" name="password" placeholder="Enter your password" required>
        <?= displayError('password') ?>

        <!-- Submit Button -->
        <button type="submit">Login</button>
    </form>
</body>
</html>