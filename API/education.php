<?php

require_once __DIR__ . '/../Config/Database.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = Database::getConnection();

    $country = $_GET['country'] ?? null;
    $indicator = $_GET['indicator'] ?? null;
    $year = $_GET['year'] ?? null;

    $sql = "
        SELECT
            c.name AS country,
            e.country_code,
            i.name AS indicator,
            e.indicator_code,
            e.year,
            e.value
        FROM education_stats e
        LEFT JOIN countries c ON c.code = e.country_code
        LEFT JOIN indicators i ON i.code = e.indicator_code
        WHERE 1 = 1
    ";

    $params = [];

    if ($country) {
        $sql .= " AND e.country_code = :country";
        $params[':country'] = strtoupper($country);
    }

    if ($indicator) {
        $sql .= " AND e.indicator_code = :indicator";
        $params[':indicator'] = $indicator;
    }

    if ($year) {
        $sql .= " AND e.year = :year";
        $params[':year'] = (int)$year;
    }

    $sql .= " ORDER BY e.year, c.name";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode([
        'success' => true,
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Server error'
    ]);
}