<?php

require_once 'auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (
        $username === ADMIN_USERNAME && $password === ADMIN_PASSWORD
    ) {
        $_SESSION['admin'] = true;
        header('Location: page.php');
        exit;
    }
    $error = 'Invalid credentials.';
}
?>
