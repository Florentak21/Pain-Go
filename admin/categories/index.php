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

    // Include the database configuration file
    require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

    // Fetch all categories from the database
    $query = $database->prepare("SELECT * FROM categories");
    $query->execute();
    $categories = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories List</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        a {
            margin-right: 10px;
            text-decoration: none;
            color: #007bff;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php if ($query->rowCount() === 0): ?>
        <h1>Oops! No categories found.</h1>
    <?php else: ?>
        <h1>Categories List</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Category Code</th>
                    <th>Category Label</th>
                    <th>Created Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?= htmlspecialchars($category['id']) ?></td>
                    <td><?= htmlspecialchars($category['codecat']) ?></td>
                    <td><?= htmlspecialchars($category['libcat']) ?></td>
                    <td><?= htmlspecialchars($category['created_at']) ?></td>
                    <td>
                        <a href="./update.php?id=<?= $category['id'] ?>" id="update_link">Update</a>
                        <a href="./delete.php?id=<?= $category['id'] ?>" id="delete_link">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>