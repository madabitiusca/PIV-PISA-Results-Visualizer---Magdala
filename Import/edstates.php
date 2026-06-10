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

$dataDir = __DIR__ . '/../Data';

$wantedIndicators = [
    'SE.XPD.TOTL.GD.ZS',
    'SE.ADT.LITR.ZS',
    'SE.ADT.1524.LT.ZS',
    'SE.PRM.ENRL',
    'SE.SEC.ENRL'
];

$pdo->beginTransaction();

/* Import indicators from EdStatsSeries.csv */
$file = fopen($dataDir . '/EdStatsSeries.csv', 'r');

if (!$file) {
    die("Cannot open EdStatsSeries.csv\n");
}

$header = fgetcsv($file, 0, ',', '"', '\\');
$header = normalizeHeader($header);
$columns = array_flip($header);

$indicatorStmt = $pdo->prepare("
    INSERT OR REPLACE INTO indicators (
        code,
        name,
        topic
    )
    VALUES (
        :code,
        :name,
        :topic
    )
");

while (($row = fgetcsv($file, 0, ',', '"', '\\')) !== false) {
    $code = trim($row[$columns['Series Code']] ?? '');

    if (!in_array($code, $wantedIndicators, true)) {
        continue;
    }

    $indicatorStmt->execute([
        ':code' => $code,
        ':name' => trim($row[$columns['Indicator Name']] ?? $code),
        ':topic' => trim($row[$columns['Topic']] ?? '')
    ]);
}

fclose($file);

/* Import values from EdStatsData.csv */
$file = fopen($dataDir . '/EdStatsData.csv', 'r');

if (!$file) {
    die("Cannot open EdStatsData.csv\n");
}

$header = fgetcsv($file, 0, ',', '"', '\\');
$header = normalizeHeader($header);
$columns = array_flip($header);

$statStmt = $pdo->prepare("
    INSERT INTO education_stats (
        country_code,
        indicator_code,
        year,
        value
    )
    VALUES (
        :country_code,
        :indicator_code,
        :year,
        :value
    )
");

$years = range(2000, 2022);
$count = 0;

while (($row = fgetcsv($file, 0, ',', '"', '\\')) !== false) {
    $indicatorCode = trim($row[$columns['Indicator Code']] ?? '');

    if (!in_array($indicatorCode, $wantedIndicators, true)) {
        continue;
    }

    $countryCode = trim($row[$columns['Country Code']] ?? '');

    if ($countryCode === '') {
        continue;
    }

    foreach ($years as $year) {
        $yearColumn = (string)$year;

        if (!isset($columns[$yearColumn])) {
            continue;
        }

        $value = trim($row[$columns[$yearColumn]] ?? '');

        if ($value === '') {
            continue;
        }

        $statStmt->execute([
            ':country_code' => $countryCode,
            ':indicator_code' => $indicatorCode,
            ':year' => $year,
            ':value' => (float)$value
        ]);

        $count++;
    }
}

fclose($file);

$pdo->commit();

echo "EdStats imported: " . $count . " rows\n";