<?php
    // Start the session
    session_start();

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

    // Include necessary files
    require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
    require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'functions.php';

    // Set the page title dynamically
    $title = basename($_SERVER['PHP_SELF'], ".php");

    // Fetch category from database
    $category_id = htmlspecialchars($_GET['id']);
    $query = $database->prepare("SELECT * FROM categories WHERE id = ?");
    $query->execute([$category_id]);
    $category = $query->fetch(PDO::FETCH_ASSOC);

    // If the category does not exist, redirect to the categories list
    if (!$category) {
        $_SESSION['errors']['not_found'] = "Category not found!";
        header("Location: ./index.php");
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

            // Check if the category code already exists (excluding the current category)
            $category_query = $database->prepare("SELECT * FROM categories WHERE codecat = ? AND id != ?");
            $category_query->execute([$code, $category_id]);

            if ($category_query->rowCount()) {
                $_SESSION['errors']['existing'] = "Category code $code already exists!";
            } else {
                // Update the category in the database
                $query = $database->prepare("UPDATE categories SET codecat = ?, libcat = ? WHERE id = ?");
                $query->execute([$code, $name, $category_id]);

                $_SESSION['success'] = "Category updated successfully!";
                header('Location: ./index.php');
                exit();
            }
        } else {
            // Store errors and old input values in the session
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;

            // Redirect back to the update page
            header("Location: ./update.php?id=$category_id");
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
    <h1>Update Category</h1>
    <?php if (!empty($_SESSION['errors']['existing'])): ?>
        <p class="error"><?= $_SESSION['errors']['existing'] ?></p>
    <?php endif; ?>
    <form method="POST">
        <!-- Category Code -->
        <input type="text" name="code" value="<?= $_SESSION['old']['code'] ?? $category['codecat'] ?>" placeholder="Enter category code" required>
        <?= displayError('code') ?>

        <!-- Category Name -->
        <input type="text" name="name" value="<?= $_SESSION['old']['name'] ?? $category['libcat'] ?>" placeholder="Enter category label" required>
        <?= displayError('name') ?>

        <!-- Submit Button -->
        <button type="submit">Update Category</button>
    </form>
</body>
</html>