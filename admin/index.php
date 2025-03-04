<?php
    require dirname(__DIR__) .DIRECTORY_SEPARATOR. 'config' .DIRECTORY_SEPARATOR. 'database.php';
    
    session_start();
    $title = basename($_SERVER['PHP_SELF'], ".php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ucfirst($title) . ' Page' ?></title>
</head>
<body>
    <h1>Welcome to admin dashboard</h1>
    <p>
        Welcome back <?= $_SESSION['user_credentials'] ? $_SESSION['user_credentials']['lastname'] .' '. $_SESSION['user_credentials']['firstname'] . '! Our admin. ' : ''?>
    </p>
    
</body>
</html>