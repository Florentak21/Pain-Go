<?php
    // Start the session
    session_start();

    // Include necessary files
    require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
    require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'functions.php';
    
    // Set the page title dynamically
    $title = basename($_SERVER['PHP_SELF'], ".php");

    // Redirect to login page if the user is not logged in
    if (!isset($_SESSION['user_credentials'])) {
        header("Location: ../../actions/login.php");
        exit();
    }

    // Redirect to home page if the user is not an admin
    if ($_SESSION['user_credentials']['role'] !== 'admin') {
        header("Location: ../../index.php");
        exit();
    }


    // Process the form if it is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $errors = [];

        // Validate form fields
        $errors += validate($_POST['code'], 'code', 3, 5);
        $errors += validate($_POST['name'], 'name', 5, 20);

        if (empty($errors)) {
            $code = htmlspecialchars($_POST['code']);
            $name = htmlspecialchars($_POST['name']);

            // Check if the category already exists
            $category_query = $database->prepare("SELECT * FROM categories WHERE codecat = ?");
            $category_query->execute([$code]);

            if ($category_query->rowCount()) {
                $_SESSION['errors']['existing'] = "Category $code already exists!";
            } else {
                // Insert the new category into the database
                $query = $database->prepare("INSERT INTO categories (codecat, libcat, created_at) VALUES (?, ?, NOW())");
                $query->execute([$code, $name]);

                $_SESSION['success'] = "New category created successfully!";
                header('Location: ./index.php');
                exit();
            }
        } else {
            // Store errors and old input values in the session
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;

            // Redirect back to the insert page
            header('Location: ./insert.php');
            exit();
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ucfirst($title) ?></title>
    <style>
        form {
            max-width: 400px;
            margin: 0 auto;
        }
        input, button {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
        .error {
            color: red;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <h1>Create a New Category</h1>
    <?php if (!empty($_SESSION['errors']['existing'])): ?>
        <p class="error"><?= $_SESSION['errors']['existing'] ?></p>
    <?php endif; ?>
    <form method="POST">
        <!-- Category Code -->
        <input type="text" name="code" value="<?= $_SESSION['old']['code'] ?? '' ?>" placeholder="Enter category code" required>
        <?= displayError('code') ?>

        <!-- Category Name -->
        <input type="text" name="name" value="<?= $_SESSION['old']['name'] ?? '' ?>" placeholder="Enter category label" required>
        <?= displayError('name') ?>

        <!-- Submit Button -->
        <button type="submit">Create New Category</button>
    </form>
</body>
</html>