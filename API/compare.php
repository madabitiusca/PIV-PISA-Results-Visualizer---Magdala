<?php

require_once __DIR__ . '/../Config/Database.php';

header('Content-Type: application/json; charset=utf-8');

try {

    $pdo = Database::getConnection();

    $countries = $_GET['countries'] ?? '';
    $discipline = $_GET['discipline'] ?? 'math';
    $subject = $_GET['subject'] ?? 'total';
    $year = $_GET['year'] ?? null;

    $countryList = array_filter(
        array_map(
            'trim',
            explode(',', strtoupper($countries))
        )
    );

    if (empty($countryList)) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'countries parameter required'
        ]);

        exit;
    }

    $placeholders = [];
    $params = [];

    foreach ($countryList as $index => $country) {

        $key = ':country' . $index;

        $placeholders[] = $key;
        $params[$key] = $country;
    }

    $sql = "
        SELECT
            c.name AS country,
            p.country_code,
            p.year,
            p.discipline,
            p.subject,
            p.score
        FROM pisa_results p
        LEFT JOIN countries c
            ON c.code = p.country_code
        WHERE p.country_code IN (
            " . implode(',', $placeholders) . "
        )
    ";

    if ($discipline) {

        $sql .= "
            AND p.discipline = :discipline
        ";

        $params[':discipline'] = $discipline;
    }

    if ($subject) {

        $sql .= "
            AND p.subject = :subject
        ";

        $params[':subject'] = $subject;
    }

    if ($year) {

        $sql .= "
            AND p.year = :year
        ";

        $params[':year'] = (int)$year;
    }

    $sql .= "
        ORDER BY p.score DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode([
        'success' => true,
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ], JSON_PRETTY_PRINT);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}