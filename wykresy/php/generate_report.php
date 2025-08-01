<?php
header('Content-Type: text/xls; charset=utf-8');
$dozwoloneTabele = ["temperatura", "light"];
$tabela = isset($_GET["tabela"]) && in_array($_GET["tabela"], $dozwoloneTabele)
          ? $_GET["tabela"] 
          : "temperatura";

$pietro = isset($_GET["pietro"]) ? intval($_GET["pietro"]) : 1;

// dynamiczna kolumna czasu
$czasDodaniaKlauzula = $tabela === "light" ? "data" : "czas_dodania";
$month = date('m');
$filename = $pietro . $tabela . $month . ".xls";
if($pietro === 4) {
    $filename = "wszystkie" . $tabela . $month . ".xls";
}

header("Content-Disposition: attachment; filename=\"$filename\"");

$conn = new mysqli("localhost", "root", "", "plc_database");
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}



$sql = "SELECT * FROM $tabela 
        WHERE $czasDodaniaKlauzula > NOW() - INTERVAL 30 DAY 
        ORDER BY $czasDodaniaKlauzula DESC";


switch ($pietro) {
    case 1:
        if ($tabela === "temperatura") {
            $sql = str_replace("SELECT *", "SELECT t1_1, t1_2, t1_3, t1_4, t1_5, t1_6, t1_7, zewnetrzna, czas_dodania", $sql);
        } else {
            $sql = str_replace("SELECT *", "SELECT l1_1_1, l1_1_2, l1_2_1, l1_2_2, l1_3_1, l1_3_2, l1_4_1, l1_4_2, l1_5_1, l1_5_2, l1_6_1, l1_6_2, l1_7_1, l1_7_2", $sql);
        }
        break;
    case 2:
        if ($tabela === "temperatura") {
            $sql = str_replace("SELECT *", "SELECT t2_1, t2_2, t2_3, t2_4, t2_5, t2_6, t2_7, zewnetrzna, czas_dodania", $sql);
        } else {
            $sql = str_replace("SELECT *", "SELECT l2_1_1, l2_1_2, l2_2_1, l2_2_2, l2_3_1, l2_3_2, l2_4_1, l2_4_2, l2_5_1, l2_5_2, l2_6_1, l2_6_2, l2_7_1, l2_7_2", $sql);
        }
        break;
    case 3:
        if ($tabela === "temperatura") {
            $sql = str_replace("SELECT *", "SELECT t3_1, t3_2, t3_3, t3_4, t3_5, t3_6, t3_7, zewnetrzna, czas_dodania", $sql);
        } else {
            $sql = str_replace("SELECT *", "SELECT l3_1_1, l3_1_2, l3_2_1, l3_2_2, l3_3_1, l3_3_2, l3_4_1, l3_4_2, l3_5_1, l3_5_2, l3_6_1, l3_6_2, l3_7_1, l3_7_2", $sql);
        }
        break;
    case 4:
        break; // dla pietro 4 nie zmieniamy zapytania, pobieramy wszystkie dane
    default:
        die("Nieprawidłowe piętro");
}

$result = $conn->query($sql);
if (!$result) {
    die("Błąd zapytania SQL: " . $conn->error);
}

$output = fopen("php://output", "w");

// --- aliasy z cookies ---
$aliases = [];

if ($pietro === 4) {
    // scalamy aliasy z pietro1, pietro2, pietro3
    for ($i = 1; $i <= 3; $i++) {
        $cookieName = "pietro" . $i;
        if (isset($_COOKIE[$cookieName])) {
            $decoded = json_decode($_COOKIE[$cookieName], true);
            if (is_array($decoded)) {
                $aliases = array_merge($aliases, $decoded);
            }
        }
    }
} else {
    $cookieName = "pietro" . $pietro;
    if (isset($_COOKIE[$cookieName])) {
        $aliases = json_decode($_COOKIE[$cookieName], true);
        if (!is_array($aliases)) {
            $aliases = [];
        }
    }
}

// --- nagłówki xlsx ---
if ($result->num_rows > 0) {
    $firstRow = $result->fetch_assoc();
    $columns = array_keys($firstRow);

    // zamiana nazw kolumn na aliasy z cookies
    $header = [];
    foreach ($columns as $col) {
        $header[] = $aliases[$col] ?? $col;
    }

    fputcsv($output, $header);

    // zapis pierwszego wiersza
    fputcsv($output, $firstRow);

    // reszta danych
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
} else {
    fputcsv($output, ["Brak danych"]);
}

fclose($output);
exit;