<?php

$dbPath = __DIR__ . '/../Database/pisavis.sqlite';
$csvPath = __DIR__ . '/../Data/EdStatsCountry.csv';

$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$file = fopen($csvPath, 'r');

if (!$file) {
    die("Could not open CSV file.\n");
}

$header = fgetcsv($file, 0, ',', '"', '\\');
// echo "<pre>";
// print_r($header);
// echo "</pre>";
// exit;
$header = array_map(function ($value) {
    $value = str_replace("\xEF\xBB\xBF", '', $value);
    $value = trim($value);
    $value = trim($value, '"');
    return $value;
}, $header);

$columns = array_flip($header);

$stmt = $pdo->prepare("
    INSERT OR REPLACE INTO countries (
        code,
        name,
        region,
        income_group
    ) VALUES (
        :code,
        :name,
        :region,
        :income_group
    )
");

$count = 0;

while (($row = fgetcsv($file, 0, ',', '"', '\\')) !== false) {
    $code = trim($row[$columns['Country Code']] ?? '');
    $name = trim($row[$columns['Short Name']] ?? '');
    $region = trim($row[$columns['Region']] ?? '');
    $incomeGroup = trim($row[$columns['Income Group']] ?? '');

    if ($code === '' || $name === '') {
        continue;
    }

    $stmt->execute([
        ':code' => $code,
        ':name' => $name,
        ':region' => $region !== '' ? $region : null,
        ':income_group' => $incomeGroup !== '' ? $incomeGroup : null
    ]);

    $count++;
}

fclose($file);

echo "Imported countries: " . $count . PHP_EOL;