<?php
    # Include necessary files
    require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
    require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'functions.php';

    # Start a session for the user making the request
    session_start();

    # Set the page title dynamically
    $title = basename($_SERVER['PHP_SELF']);

    # Process the form if it is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $errors = [];

        // Validate form fields
        $errors += validate($_POST['firstname'], 'firstname', 5, 50);
        $errors += validate($_POST['lastname'], 'lastname', 5, 50);
        $errors += validate($_POST['email'], 'email', 5, 255, 'email');
        $errors += validate($_POST['password'], 'password', 8, 255);

        // If no errors, proceed with user registration
        if (empty($errors)) {
            $firstname = htmlspecialchars($_POST['firstname']);
            $lastname = htmlspecialchars($_POST['lastname']);
            $email = htmlspecialchars($_POST['email']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            // Insert user data into the database
            $query = $database->prepare("INSERT INTO users (firstname, lastname, email, password, created_at) VALUES (?, ?, ?, ?, NOW())");
            $query->execute([$firstname, $lastname, $email, $password]);

            // Set success message and redirect to login page
            $_SESSION['success'] = "Registration successful. Please log in.";
            header('Location: ../actions/login.php');
            exit();
        } else {
            // Store errors and old input values in the session
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;

            // Redirect back to the registration page
            header('Location: ../actions/register.php');
            exit();
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
</head>
<body>
    <h1>Create an Account</h1>
    <form method="POST">
        <!-- First Name -->
        <input type="text" name="firstname" value="<?= $_SESSION['old']['firstname'] ?? '' ?>" placeholder="Enter your first name" required>
        <?= displayError('firstname') ?>

        <!-- Last Name -->
        <input type="text" name="lastname" value="<?= $_SESSION['old']['lastname'] ?? '' ?>" placeholder="Enter your last name" required>
        <?= displayError('lastname') ?>

        <!-- Email -->
        <input type="email" name="email" value="<?= $_SESSION['old']['email'] ?? '' ?>" placeholder="Enter your email" required>
        <?= displayError('email') ?>

        <!-- Password -->
        <input type="password" name="password" placeholder="Choose a password" required>
        <?= displayError('password') ?>

        <!-- Submit Button -->
        <button type="submit">Create Account</button>
    </form>
</body>
</html>