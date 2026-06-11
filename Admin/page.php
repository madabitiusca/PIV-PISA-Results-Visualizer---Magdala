<?php

require_once 'auth.php';
require_once '../Config/Database.php';

if (!isset($_SESSION['admin'])) { 
    header('Location: login.php');
    exit;
}

$pdo = Database::getConnection();

$countries = $pdo->query(
    "SELECT COUNT(*) FROM countries"
)->fetchColumn();

$pisa = $pdo->query(
    "SELECT COUNT(*) FROM pisa_results"
)->fetchColumn();

$indicators = $pdo->query(
    "SELECT COUNT(*) FROM indicators"
)->fetchColumn();

$education = $pdo->query(
    "SELECT COUNT(*) FROM education_stats"
)->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>

    <style>

        body {
            font-family: Arial;
            background: #f4f6f8;
            padding: 2rem;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(4,1fr);
            gap: 1rem;
        }

        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,.1);
        }

        .card h2 {
            margin: 0;
        }

        .card p {
            font-size: 2rem;
            font-weight: bold;
        }

        .actions {
            margin-top: 2rem;
        }

        .actions a {
            display: inline-block;
            padding: .8rem 1rem;
            background: #9ED3DC;
            color: white;
            text-decoration: none;
            margin-right: .5rem;
        }

    </style>

</head>
<body>

<h1>Pisa- Admin</h1>

<div class="cards">

    <div class="card">
        <h2>Countries</h2>
        <p><?= $countries ?></p>
    </div>

    <div class="card">
        <h2>PISA Results</h2>
        <p><?= $pisa ?></p>
    </div>

    <div class="card">
        <h2>Indicators</h2>
        <p><?= $indicators ?></p>
    </div>

    <div class="card">
        <h2>Education Stats</h2>
        <p><?= $education ?></p>
    </div>

</div>

<div class="actions">

    <a href="logout.php">
        Logout
    </a>

</div>

</body>
</html>