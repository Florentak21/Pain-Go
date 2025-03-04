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

    // Get the category ID from the URL
    $category_id = htmlspecialchars($_GET['id']);

    // Fetch the category from the database
    $query = $database->prepare("SELECT * FROM categories WHERE id = ?");
    $query->execute([$category_id]);
    $category = $query->fetch(PDO::FETCH_ASSOC);

    // If the category does not exist, redirect to the categories list with an error message
    if (!$category) {
        $_SESSION['errors']['not_found'] = "Category not found!";
        header("Location: ./index.php");
        exit();
    }

    // Delete the category from the database
    $delete_query = $database->prepare("DELETE FROM categories WHERE id = ?");
    $delete_query->execute([$category_id]);

    // Check if the deletion was successful
    if ($delete_query->rowCount()) {
        $_SESSION['success'] = "Category deleted successfully!";
    } else {
        $_SESSION['errors']['delete'] = "Failed to delete the category.";
    }

    // Redirect to the categories list
    header("Location: ./index.php");
    exit();
?>