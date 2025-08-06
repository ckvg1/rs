
<?php
//skrypt do odczytania, o ktorej godzienie jest najwiecej wlaczonych swiatel

$conn = mysqli_connect("localhost", "root", "", "plc_database");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$pietro = isset($_GET["pietro"]) ? intval($_GET["pietro"]) : 1;
$czas = isset($_GET["czas"]) ? intval($_GET["czas"]) : 1;

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

$godzina;
$ilosc_wlaczen;

if (!$result) {
    die("Błąd zapytania SQL: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $godzina = $row['godzina'];
    $ilosc_wlaczen = $row['ilosc_wlaczen'];
    
    // Wyświetlenie wyniku
   // echo "Godzina z największą liczbą włączeń świateł: " . $row['godzina'] . ":00, Liczba włączeń: " . $row['ilosc_wlaczen'];
} else {
    echo "Brak danych.";
}



$response = [
            'godzina' => $godzina,
            'ilosc_wlaczen' => $ilosc_wlaczen, 
        ];
    
echo json_encode($response);

mysqli_close($conn);


?>