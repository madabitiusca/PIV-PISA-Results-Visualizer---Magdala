<?php

require_once __DIR__ . '/../Config/Database.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = Database::getConnection();

    $stmt = $pdo->query("
        SELECT code, name, region, income_group
        FROM countries
        WHERE region IS NOT NULL
        ORDER BY name
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