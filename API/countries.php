<?php

require_once __DIR__ . '/../Config/Database.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = Database::getConnection();

    $stmt = $pdo->query("
        SELECT DISTINCT 
            c.code,
            c.name,
            c.region,
            c.income_group
        FROM countries c
        INNER JOIN pisa_results p
            ON p.country_code = c.code
        WHERE c.code IS NOT NULL
        AND c.code <> ''
        ORDER BY c.name
    ");

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