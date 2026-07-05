<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = 'ملف الخريجين القدامى -البصرة 2022.xlsx';
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray();

echo 'Total rows: ' . count($rows) . "\n";
echo "=== Header Row ===\n";
foreach ($rows[0] as $i => $h) {
    echo "[$i] " . (is_null($h) ? 'NULL' : $h) . "\n";
}

echo "=== First 3 data rows ===\n";
for ($r = 1; $r <= min(3, count($rows)-1); $r++) {
    echo 'Row ' . ($r+1) . ": ";
    foreach ($rows[$r] as $i => $v) {
        $val = is_null($v) ? 'NULL' : (is_string($v) ? '"' . $v . '"' : $v);
        echo "[$i]=$val ";
    }
    echo "\n";
}
