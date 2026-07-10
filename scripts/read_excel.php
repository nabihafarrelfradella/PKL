<?php
require __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

function readExcel($path) {
    echo "\n========================================\n";
    echo "FILE: " . basename($path) . "\n";
    echo "========================================\n";
    $spreadsheet = IOFactory::load($path);
    foreach ($spreadsheet->getSheetNames() as $sheetName) {
        $sheet = $spreadsheet->getSheetByName($sheetName);
        $highestRow = $sheet->getHighestRow();
        $highestCol = $sheet->getHighestColumn();
        echo "\n--- SHEET: $sheetName (rows: $highestRow, cols: $highestCol) ---\n";
        $rows = $sheet->toArray(null, true, true, false);
        $rowCount = 0;
        foreach ($rows as $rowIndex => $row) {
            $vals = array_filter($row, fn($v) => $v !== null && $v !== '');
            if (empty($vals)) continue;
            echo "Row " . ($rowIndex + 1) . ": " . implode(" | ", array_map(fn($v) => $v === null ? '' : (string)$v, $row)) . "\n";
            $rowCount++;
            if ($rowCount >= 500) {
                echo "... (truncated at 500 non-empty rows)\n";
                break;
            }
        }
    }
}

$baseDir = __DIR__ . '/../excel alfatindo';
readExcel($baseDir . '/BARANG.xlsx');
readExcel($baseDir . '/LAPORAN STOK BARANG - BARANG MASUK - BARANG KELUAR.xlsx');
