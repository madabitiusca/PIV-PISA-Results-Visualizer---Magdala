<?php

require_once 'auth.php';
require_once '../Config/Cache.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$cache = new Cache(__DIR__ . '/../Cache');
$cache->clear();

header('Location: page.php');
exit;