<?php

function normalizeHeader(array $header): array {
    return array_map(function ($value) {
        $value = str_replace("\xEF\xBB\xBF", '', $value);
        $value = trim($value);
        $value = trim($value, '"');
        return $value;
    }, $header);
}

$pdo = new PDO(
    'sqlite:' . __DIR__ . '/../Database/pisavis.sqlite'
);

$pdo->setAttribute(
    PDO::ATTR_ERRMODE,
    PDO::ERRMODE_EXCEPTION
);

$csvPath = __DIR__ . '/../Data/OECD PISA data.csv';

$file = fopen($csvPath, 'r');

if (!$file) {
    die("Cannot open OECD PISA data.csv\n");
}

$header = fgetcsv($file, 0, ',', '"', '\\');
$header = normalizeHeader($header);
$columns = array_flip($header);

$disciplineMap = [
    'PISAMATH' => 'math',
    'PISAREAD' => 'reading',
    'PISASCI' => 'science',
    'PISASCIENCE' => 'science'
];

$subjectMap = [
    'BOY' => 'boys',
    'GIRL' => 'girls',
    'TOT' => 'total'
];

$stmt = $pdo->prepare("
    INSERT INTO pisa_results (
        country_code,
        year,
        discipline,
        subject,
        score
    )
    VALUES (
        :country_code,
        :year,
        :discipline,
        :subject,
        :score
    )
");

$count = 0;

while (($row = fgetcsv($file, 0, ',', '"', '\\')) !== false) {
    $countryCode = trim($row[$columns['LOCATION']] ?? '');
    $indicator = trim($row[$columns['INDICATOR']] ?? '');
    $subjectRaw = trim($row[$columns['SUBJECT']] ?? '');
    $year = trim($row[$columns['TIME']] ?? '');
    $value = trim($row[$columns['Value']] ?? '');

    if (
        $countryCode === '' ||
        $indicator === '' ||
        $subjectRaw === '' ||
        $year === '' ||
        $value === ''
    ) {
        continue;
    }

    if (!isset($disciplineMap[$indicator])) {
        continue;
    }

    $discipline = $disciplineMap[$indicator];
    $subject = $subjectMap[$subjectRaw] ?? strtolower($subjectRaw);

    $stmt->execute([
        ':country_code' => $countryCode,
        ':year' => (int)$year,
        ':discipline' => $discipline,
        ':subject' => $subject,
        ':score' => (float)$value
    ]);

    $count++;
}

fclose($file);

echo "PISA imported: " . $count . " rows\n";