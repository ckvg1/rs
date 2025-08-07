
<?php
//skrypt do odczytania, o ktorej godzienie jest najwiecej wlaczonych swiatel

$conn = mysqli_connect("localhost", "root", "", "plc_database");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$pietro = isset($_GET["pietro"]) ? intval($_GET["pietro"]) : 1;
$czas = isset($_GET["czas"]) ? intval($_GET["czas"]) : 1;

// Sprawdzenie, czy piętro jest poprawne
if ($pietro < 1 || $pietro > 3) {
    die("Nieprawidłowe piętro");
}

// Sprawdzenie, czy czas jest poprawny
if ($czas < 1 || $czas > 3) {
    die("Nieprawidłowy czas");
}

// Ustawienie interwału w zależności od wybranego czasu
// 1 - 24h, 2 - 7 dni, 3 - 30 dni

$interval = 1;
switch($czas) {
    case 1: // 24h
        $interval = "1";
        break;
    case 2: // 7 dni
        $interval = "7";
        break;
    case 3: // 30 dni
        $interval = "30";
        break;
    default:
        die("Nieprawidłowy czas");
}
// Zapytanie SQL do pobrania godziny z największą liczbą włączeń świateł
// Używamy SUM() do zliczenia wszystkich włączeń świateł na danym piętrze
// Zakładamy, że kolumny są nazwane zgodnie z konwencją l{pietro}_{numer_swiatla}_{numer_czujnika}
// Przykład: l1_1_1, l1_1_2, l2_2_1, itd.
// W zależności od piętra, zmieniamy prefiks kolumny w zapytaniu SQL

// Przygotowanie zapytania SQL
$sql = "SELECT 
  SUM(
    l{$pietro}_1_1 + l{$pietro}_1_2 + l{$pietro}_2_1 + l{$pietro}_2_2 + l{$pietro}_3_1 + l{$pietro}_3_2 + l{$pietro}_4_1 + l{$pietro}_4_2 + l{$pietro}_5_1 + l{$pietro}_5_2 + l{$pietro}_6_1 + l{$pietro}_6_2 + l{$pietro}_7_1 + l{$pietro}_7_2 
  ) AS ilosc_wlaczen,
  HOUR(data) AS godzina
FROM `light`
WHERE data > NOW() - INTERVAL $interval DAY
GROUP BY godzina
ORDER BY ilosc_wlaczen DESC LIMIT 1;";

$result = mysqli_query($conn, $sql);
// Inicjalizacja zmiennych
$godzina;
$ilosc_wlaczen;
// Sprawdzenie, czy zapytanie zostało wykonane poprawnie
if (!$result) {
    die("Błąd zapytania SQL: " . mysqli_error($conn));
}
// Sprawdzenie, czy są wyniki
// Jeśli są wyniki, pobieramy godzinę i liczbę włączeń
// Jeśli nie ma wyników, wyświetlamy komunikat o braku danych
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $godzina = $row['godzina'];
    $ilosc_wlaczen = $row['ilosc_wlaczen'];
    
    // Wyświetlenie wyniku
   // echo "Godzina z największą liczbą włączeń świateł: " . $row['godzina'] . ":00, Liczba włączeń: " . $row['ilosc_wlaczen'];
} else{
    $response = [
        'godzina' => "brak danych",
        'ilosc_wlaczen' => "brak danych"
    ];
    echo json_encode($response);
    mysqli_close($conn);
    exit;
}   
    


// Przygotowanie odpowiedzi w formacie JSON
// Zwracamy godzinę i liczbę włączeń w formacie JSON
// Można to wykorzystać w aplikacji frontendowej do wyświetlenia danych
$response = [
            'godzina' => $godzina,
            'ilosc_wlaczen' => $ilosc_wlaczen, 
        ];
    
echo json_encode($response);

mysqli_close($conn);


?>