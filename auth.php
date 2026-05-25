<?php
session_start();

$currentAccessType = $accessType ?? 'private';

if ($currentAccessType === 'private') {
    if(!isset($_SESSION['id_user'])) {
        header ('Location: welcome.php');
    }
} elseif ($currentAccessType === 'public') {
    if(isset($_SESSION['id_user'])) {
        header ('Location: index.php');
    }
}