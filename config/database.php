<?php

    /* DATABASE CONNECTION STRING */
    $hostname = 'localhost';
    $username = 'florentak';
    $password = 'florentak@localHost@2003';
    $db = "pain_go";
    try {
        $database = new PDO("mysql:dbname=$db;host=$hostname", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        echo "Database connection made successfully !";
    } catch (PDOException $e) {
        echo "Database connection error: " . $e->getMessage();
    }
?>