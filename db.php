<?php

$servername = "localhost";
$dbname = "fitness_tracker";
$username = "root";
$password = "";

$dsn = "mysql:host=$servername;dbname=$dbname";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];

try {
    $conn = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    echo "Connection failed" . $e->getMessage();
}