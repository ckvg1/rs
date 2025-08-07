<?php
header('Content-Type: text/csv; charset=utf-8');
//Skrypt do generowania raportu w formacie CSV

// Sprawdzenie, czy użytkownik podał parametr "tabela"
if (!isset($_GET["tabela"])) {
    die("Brak parametru 'tabela'");
}

// Dozwolone tabele do pobrania danych
// Można dodać więcej tabel, jeśli zajdzie taka potrzeba
$dozwoloneTabele = ["temperatura", "light"];
$tabela = isset($_GET["tabela"]) && in_array($_GET["tabela"], $dozwoloneTabele)
          ? $_GET["tabela"] 
          : "temperatura";
// Pobranie piętra z parametru GET, domyślnie 1
$pietro = isset($_GET["pietro"]) ? intval($_GET["pietro"]) : 1;

// Sprawdzenie, czy piętro jest poprawne
if ($pietro < 1 || $pietro > 4) {
    die("Nieprawidłowe piętro");
}

//W tabelach kolumna czasu nazwya sie inaczej, wiec sprawdzamu zmienna $tabela i ustawiamy odpowiednią kolumnę
// Dla tabeli "light" kolumna czasu to "data", dla "temperatura" to "czas_dodania"

$czasDodaniaKlauzula = $tabela === "light" ? "data" : "czas_dodania";
// Ustawienie nazwy pliku CSV na podstawie piętra i tabeli
// Miesiąc jest dodawany do nazwy pliku, aby uniknąć konfliktów nazw plików
// Jeśli piętro to 4, to pobieramy wszystkie dane z wszystkich pięter
// W przeciwnym razie, pobieramy dane tylko z wybranego piętra
// Nazwa pliku będzie miała format: "pietro_tabela_miesiac.csv"
$month = date('m');
$filename = $pietro . $tabela . $month . ".csv";
if($pietro === 4) {
    $filename = "wszystkie" . $tabela . $month . ".csv";
}

header("Content-Disposition: attachment; filename=\"$filename\"");

// Otwarcie połączenia do bazy danych
$conn = new mysqli("localhost", "root", "", "plc_database");

// Sprawdzenie połączenia
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

// Zapytanie SQL wybiera wszystkie kolumny z tabeli, gdzie czas_dodania jest większy niż 30 dni temu
$sql = "SELECT * FROM $tabela 
        WHERE $czasDodaniaKlauzula > NOW() - INTERVAL 30 DAY 
        ORDER BY $czasDodaniaKlauzula DESC";

// Modyfikacja zapytania SQL w zależności od piętra
switch ($pietro) {
    case 1:
        if ($tabela === "temperatura") {
            $sql = str_replace("SELECT *", "SELECT t1_1, t1_2, t1_3, t1_4, t1_5, t1_6, t1_7, t_zewn, czas_dodania", $sql);
        } else {
            $sql = str_replace("SELECT *", "SELECT l1_1_1, l1_1_2, l1_2_1, l1_2_2, l1_3_1, l1_3_2, l1_4_1, l1_4_2, l1_5_1, l1_5_2, l1_6_1, l1_6_2, l1_7_1, l1_7_2", $sql);
        }
        break;
    case 2:
        if ($tabela === "temperatura") {
            $sql = str_replace("SELECT *", "SELECT t2_1, t2_2, t2_3, t2_4, t2_5, t2_6, t2_7, t_zewn, czas_dodania", $sql);
        } else {
            $sql = str_replace("SELECT *", "SELECT l2_1_1, l2_1_2, l2_2_1, l2_2_2, l2_3_1, l2_3_2, l2_4_1, l2_4_2, l2_5_1, l2_5_2, l2_6_1, l2_6_2, l2_7_1, l2_7_2", $sql);
        }
        break;
    case 3:
        if ($tabela === "temperatura") {
            $sql = str_replace("SELECT *", "SELECT t3_1, t3_2, t3_3, t3_4, t3_5, t3_6, t3_7, t_zewn, czas_dodania", $sql);
        } else {
            $sql = str_replace("SELECT *", "SELECT l3_1_1, l3_1_2, l3_2_1, l3_2_2, l3_3_1, l3_3_2, l3_4_1, l3_4_2, l3_5_1, l3_5_2, l3_6_1, l3_6_2, l3_7_1, l3_7_2", $sql);
        }
        break;
    case 4:
        break; // dla pietro 4 nie zmieniamy zapytania, pobieramy wszystkie dane
    default:
        die("Nieprawidłowe piętro");
}

// Wykonanie zapytania SQL
// Używamy mysqli_query, aby wykonać zapytanie i pobrać wyniki
// Sprawdzamy, czy zapytanie zostało wykonane poprawnie
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

// --- nagłówki csv ---
if ($result->num_rows > 0) {
    $firstRow = $result->fetch_assoc();
    $columns = array_keys($firstRow);

    // zamiana nazw kolumn na aliasy z cookies
    $header = [];
    foreach ($columns as $col) {
        $header[] = $aliases[$col] ?? $col;
    }

    fputcsv($output, $header, ';');

    // zapis pierwszego wiersza
    fputcsv($output, $firstRow, ';');

    // reszta danych
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row, ';');
    }
} else {
    fputcsv($output, ["Brak danych"], ';');
}

fclose($output);
exit;