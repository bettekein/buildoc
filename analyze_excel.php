<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$output = '';

// グリーンファイル作成シートの分析
$output .= "=== 安全書類グリーンファイル作成シート 分析結果 ===\n\n";
$greenFile = IOFactory::load('docs/参考元エクセルファイル/建設関係_安全書類グリーンファイル作成シート_サンプル.xlsx');
$sheets = $greenFile->getSheetNames();
$output .= "【シート一覧】(" . count($sheets) . "シート)\n";
foreach ($sheets as $i => $name) {
    $output .= "  " . ($i + 1) . ". " . $name . "\n";
}
$output .= "\n";

// 各シートのヘッダー行を分析
foreach ($sheets as $sheetName) {
    $output .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $output .= "【シート: {$sheetName}】\n";
    $output .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $sheet = $greenFile->getSheetByName($sheetName);
    $highestRow = min($sheet->getHighestRow(), 30);
    $highestColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn());

    for ($row = 1; $row <= $highestRow; $row++) {
        $rowData = [];
        for ($colIndex = 1; $colIndex <= min($highestColIndex, 20); $colIndex++) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $cell = $sheet->getCell($col . $row);
            $value = $cell->getValue();
            if ($value !== null && $value !== '' && !str_starts_with((string) $value, '=')) {
                $displayValue = mb_substr(str_replace(["\n", "\r"], ' ', (string) $value), 0, 25);
                $rowData[] = "{$col}:{$displayValue}";
            }
        }
        if (!empty($rowData)) {
            $output .= "Row{$row}: " . implode(' | ', $rowData) . "\n";
        }
    }
    $output .= "\n";
}

$output .= "\n\n";
$output .= "=== 請求書作成シート 分析結果 ===\n\n";
$invoiceFile = IOFactory::load('docs/参考元エクセルファイル/建設関係_請求書作成シート_サンプル.xlsx');
$invoiceSheets = $invoiceFile->getSheetNames();
$output .= "【シート一覧】(" . count($invoiceSheets) . "シート)\n";
foreach ($invoiceSheets as $i => $name) {
    $output .= "  " . ($i + 1) . ". " . $name . "\n";
}
$output .= "\n";

foreach ($invoiceSheets as $sheetName) {
    $output .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $output .= "【シート: {$sheetName}】\n";
    $output .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $sheet = $invoiceFile->getSheetByName($sheetName);
    $highestRow = min($sheet->getHighestRow(), 30);
    $highestColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($sheet->getHighestColumn());

    for ($row = 1; $row <= $highestRow; $row++) {
        $rowData = [];
        for ($colIndex = 1; $colIndex <= min($highestColIndex, 15); $colIndex++) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $cell = $sheet->getCell($col . $row);
            $value = $cell->getValue();
            if ($value !== null && $value !== '' && !str_starts_with((string) $value, '=')) {
                $displayValue = mb_substr(str_replace(["\n", "\r"], ' ', (string) $value), 0, 25);
                $rowData[] = "{$col}:{$displayValue}";
            }
        }
        if (!empty($rowData)) {
            $output .= "Row{$row}: " . implode(' | ', $rowData) . "\n";
        }
    }
    $output .= "\n";
}

file_put_contents('docs/参考元エクセルファイル/分析結果.txt', $output);
echo "分析完了: docs/参考元エクセルファイル/分析結果.txt に出力しました\n";
