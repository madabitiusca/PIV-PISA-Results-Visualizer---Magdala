<?php

require_once __DIR__ . '/../Config/Database.php';

$pdo = Database::getConnection();

$format = $_GET['format'] ?? 'json';
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

$sql .= " ORDER BY p.year, c.name";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($format === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="pisa_export.json"');

    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

if ($format === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="pisa_export.csv"');

    $output = fopen('php://output', 'w');

    fputcsv($output, [
        'Country',
        'Country Code',
        'Year',
        'Discipline',
        'Subject',
        'Score'
    ]);

    foreach ($data as $row) {
        fputcsv($output, [
            $row['country'],
            $row['country_code'],
            $row['year'],
            $row['discipline'],
            $row['subject'],
            $row['score']
        ]);
    }

    fclose($output);
    exit;
}

if ($format === 'svg') {
    header('Content-Type: image/svg+xml; charset=utf-8');
    header('Content-Disposition: attachment; filename="pisa_chart.svg"');

    $width = 800;
    $height = 400;
    $padding = 50;

    $maxScore = 600;
    $barWidth = 40;
    $gap = 20;

    echo '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '">';
    echo '<rect width="100%" height="100%" fill="white"/>';

    $x = $padding;

    foreach ($data as $row) {
        $barHeight = ((float)$row['score'] / $maxScore) * ($height - 2 * $padding);
        $y = $height - $padding - $barHeight;

        echo '<rect x="' . $x . '" y="' . $y . '" width="' . $barWidth . '" height="' . $barHeight . '" fill="#4f46e5"/>';
        echo '<text x="' . $x . '" y="' . ($height - 20) . '" font-size="10">' . htmlspecialchars($row['country_code']) . '</text>';
        echo '<text x="' . $x . '" y="' . ($y - 5) . '" font-size="10">' . htmlspecialchars($row['score']) . '</text>';

        $x += $barWidth + $gap;
    }

    echo '</svg>';
    exit;
}

http_response_code(400);
header('Content-Type: application/json');

echo json_encode([
    'success' => false,
    'message' => 'Invalid export format'
]);