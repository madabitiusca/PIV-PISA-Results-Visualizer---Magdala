<?php

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Config/Cache.php';

header('Content-Type: application/json; charset=utf-8');

$cache = new Cache(__DIR__ . '/../Cache', 300);
$cacheKey = $_SERVER['REQUEST_URI'];

$cached = $cache->get($cacheKey);

if ($cached !== null) {
    echo $cached;
    exit;
}

try {
    $pdo = Database::getConnection();

    $country = $_GET['country'] ?? null;
    $discipline = $_GET['discipline'] ?? null;
    $subject = $_GET['subject'] ?? null;
    $year = $_GET['year'] ?? null;

    $sql = "
        SELECT 
            c.name AS country,
            p.country_code,
            p.year,
            p.discipline,
            p.subject,
            p.score
        FROM pisa_results p
        LEFT JOIN countries c ON c.code = p.country_code
        WHERE 1 = 1
    ";

    $params = [];

    if ($country) {
        $sql .= " AND p.country_code = :country";
        $params[':country'] = strtoupper($country);
    }

    if ($discipline) {
        $sql .= " AND p.discipline = :discipline";
        $params[':discipline'] = strtolower($discipline);
    }

    if ($subject) {
        $sql .= " AND p.subject = :subject";
        $params[':subject'] = strtolower($subject);
    }

    if ($year) {
        $sql .= " AND p.year = :year";
        $params[':year'] = (int)$year;
    }

    $sql .= " ORDER BY p.year, c.name, p.discipline";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $response = json_encode([
    'success' => true,
    'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$cache->set($cacheKey, $response);

echo $response;

} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Server error'
    ]);
}