<?php

$dbPath = __DIR__ . '/../Database/pisavis.sqlite';
$dataPath = __DIR__ . '/../Data';

$db = new PDO('sqlite:' . $dbPath);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->beginTransaction();

/* Countries */
$file = fopen($dataPath . '/EdStatsCountry.csv', 'r');
$header = fgetcsv($file);
$cols = array_flip($header);

$stmt = $db->prepare("
    INSERT OR REPLACE INTO countries (code, name, region, income_group)
    VALUES (:code, :name, :region, :income_group)");

while (($row = fgetcsv($file)) !== false) {
    $stmt->execute([
        ':code' => $row[$cols['Country Code']],
        ':name' => $row[$cols['Short Name']],
        ':region' => $row[$cols['Region']] ?? null,
        ':income_group' => $row[$cols['Income Group']] ?? null ]);
}

fclose($file);

/* PISA */
$file = fopen($dataPath . '/OECD PISA data.csv', 'r');
$header = fgetcsv($file);
$cols = array_flip($header);

$disciplineMap = [
    'PISAMATH' => 'math',
    'PISAREAD' => 'reading',
    'PISASCIENCE' => 'science',
    'PISASCI' => 'science'
];

$subjectMap = [
    'BOY' => 'boys',
    'GIRL' => 'girls',
    'TOT' => 'total'
];

$stmt = $db->prepare("
    INSERT INTO pisa_results (country_code, year, discipline, subject, score)
    VALUES (:country_code, :year, :discipline, :subject, :score)
");

while (($row = fgetcsv($file)) !== false) {
    $indicator = $row[$cols['INDICATOR']];

    if (!isset($disciplineMap[$indicator])) {
        continue;
    }

    $stmt->execute([
        ':country_code' => $row[$cols['LOCATION']],
        ':year' => (int)$row[$cols['TIME']],
        ':discipline' => $disciplineMap[$indicator],
        ':subject' => $subjectMap[$row[$cols['SUBJECT']]] ?? strtolower($row[$cols['SUBJECT']]),
        ':score' => (float)$row[$cols['Value']]
    ]);
}

fclose($file);

/* Wanted EdStats indicators */
$wantedIndicators = [
    'SE.XPD.TOTL.GD.ZS',
    'SE.PRM.ENRL.TC.ZS',
    'SE.SEC.ENRL.TC.ZS',
    'SE.ADT.LITR.ZS',
    'SE.ADT.1524.LT.ZS'
];

/* EdStatsSeries */
$file = fopen($dataPath . '/EdStatsSeries.csv', 'r');
$header = fgetcsv($file);
$cols = array_flip($header);

$stmt = $db->prepare("
    INSERT OR REPLACE INTO indicators (code, name, topic)
    VALUES (:code, :name, :topic)
");

while (($row = fgetcsv($file)) !== false) {
    $code = $row[$cols['Series Code']];

    if (!in_array($code, $wantedIndicators, true)) {
        continue;
    }

    $stmt->execute([
        ':code' => $code,
        ':name' => $row[$cols['Indicator Name']],
        ':topic' => $row[$cols['Topic']] ?? null
    ]);
}

fclose($file);

/* EdStatsData */
$file = fopen($dataPath . '/EdStatsData.csv', 'r');
$header = fgetcsv($file);
$cols = array_flip($header);

$stmt = $db->prepare("
    INSERT INTO education_stats (country_code, indicator_code, year, value)
    VALUES (:country_code, :indicator_code, :year, :value)
");

$years = range(2000, 2022);

while (($row = fgetcsv($file)) !== false) {
    $indicatorCode = $row[$cols['Indicator Code']];

    if (!in_array($indicatorCode, $wantedIndicators, true)) {
        continue;
    }

    foreach ($years as $year) {
        if (!isset($cols[(string)$year])) {
            continue;
        }

        $value = $row[$cols[(string)$year]];

        if ($value === '' || $value === null) {
            continue;
        }

        $stmt->execute([
            ':country_code' => $row[$cols['Country Code']],
            ':indicator_code' => $indicatorCode,
            ':year' => $year,
            ':value' => (float)$value
        ]);
    }
}

fclose($file);

$db->commit();

echo "Database imported.\n";