<?php
session_start();

echo "<h1>Session Test Page</h1>";

if (isset($_SESSION['id_user'])) {
    echo "logged in ";
    echo "ID: " . $_SESSION['id_user'];
} else {
    echo "logged out";
}

echo "<pre>";
print_r($_SESSION);
echo "</pre>";