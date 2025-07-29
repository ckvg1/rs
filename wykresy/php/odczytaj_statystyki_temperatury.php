<?php
    // Połączenie z bazą danych
    $conn = mysqli_connect("localhost", "root", "", "plc_database");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Parametr GET
    $pietro = isset($_GET["pietro"]) ? intval($_GET["pietro"]) : 1;

    // Zapytanie SQL w zależności od piętra
    switch($pietro){
        case 1:
            $sql_temp = "SELECT t1_1, t1_2, t1_3, t1_4, t1_5, t1_6, t1_7, 
                        zewnetrzna, czas_dodania 
                        FROM temperatura 
                        WHERE czas_dodania > NOW() - INTERVAL 7 DAY 
                        ORDER BY czas_dodania DESC";
            

            break;
        case 2:
            $sql_temp = "SELECT t2_1, t2_2, t2_3, t2_4, t2_5, t2_6, t2_7, 
                        zewnetrzna, czas_dodania 
                        FROM temperatura 
                        WHERE czas_dodania > NOW() - INTERVAL 7 DAY 
                        ORDER BY czas_dodania DESC";
            
            break;
        case 3:
            $sql_temp = "SELECT t3_1, t3_2, t3_3, t3_4, t3_5, t3_6, t3_7, 
                        zewnetrzna, czas_dodania 
                        FROM temperatura 
                        WHERE czas_dodania > NOW() - INTERVAL 7 DAY 
                        ORDER BY czas_dodania DESC";
            
            break;
        default:
            die("Nieprawidłowe piętro");
    }

    // Inicjalizacja tablic
    $czas = [];
    $t1_1 = $t1_2 = $t1_3 = $t1_4 = $t1_5 = $t1_6 = $t1_7 = [];
    $t2_1 = $t2_2 = $t2_3 = $t2_4 = $t2_5 = $t2_6 = $t2_7 = [];
    $t3_1 = $t3_2 = $t3_3 = $t3_4 = $t3_5 = $t3_6 = $t3_7 = [];
    $temp_zewn = [];

    // Pobieranie danych z bazy
    $result = mysqli_query($conn, $sql_temp);
    while ($row = mysqli_fetch_assoc($result)) {
        $czas[] = $row['czas_dodania'];
        switch($pietro){
            case 1:
                $t1_1[] = ($row['t1_1']);
                $t1_2[] = round($row['t1_2'], 1);
                $t1_3[] = round($row['t1_3'], 1);
                $t1_4[] = round($row['t1_4'], 1);
                $t1_5[] = round($row['t1_5'], 1);
                $t1_6[] = round($row['t1_6'], 1);
                $t1_7[] = round($row['t1_7'], 1) ;
                break;
            case 2:
                $t2_1[] = round($row['t2_1'], 1);
                $t2_2[] = round($row['t2_2'], 1);
                $t2_3[] = round($row['t2_3'], 1);
                $t2_4[] = round($row['t2_4'], 1);
                $t2_5[] = round($row['t2_5'], 1);
                $t2_6[] = round($row['t2_6'], 1);
                $t2_7[] = round($row['t2_7'], 1);
                break;
            case 3:
                $t3_1[] = round($row['t3_1'], 1);
                $t3_2[] = round($row['t3_2'], 1);
                $t3_3[] = round($row['t3_3'], 1);
                $t3_4[] = round($row['t3_4'], 1);
                $t3_5[] = round($row['t3_5'], 1);
                $t3_6[] = round($row['t3_6'], 1);
                $t3_7[] = round($row['t3_7'], 1);
                break;
        }
        $temp_zewn[] = round($row['zewnetrzna'],1);
    }
    
    
    
    

    // Grupowanie pomieszczeń w piętra (pomiesczczenia[][] dla temperatury, swiatla[][] dla światła)
    $pomieszczenia = [
        [$t1_1, $t1_2, $t1_3, $t1_4, $t1_5, $t1_6, $t1_7],
        [$t2_1, $t2_2, $t2_3, $t2_4, $t2_5, $t2_6, $t2_7],
        [$t3_1, $t3_2, $t3_3, $t3_4, $t3_5, $t3_6, $t3_7]
    ];
   

    // Funkcje
    function znajdzNajmniejszaTemperature($tablica) {
        $najmniejsza = null;
        $czujnik = null;
        for ($i = 0; $i < count($tablica); $i++) {
            if (!empty($tablica[$i])) {
                $min_local = min($tablica[$i]);
                if ($najmniejsza === null || $min_local < $najmniejsza) {
                    $najmniejsza = $min_local;
                    $czujnik = $i + 1;
                }
            }
        }
        return ['temp' => $najmniejsza, 'czujnik' => $czujnik];
    }
    function znajdzNajwyzszaTemperature($tablica) {
        $najwyzsza = null;
        $czujnik = null;
        for ($i = 0; $i < count($tablica); $i++) {
            if (!empty($tablica[$i])) {
                $max_local = max($tablica[$i]);
                if ($najwyzsza === null || $max_local > $najwyzsza) {
                    $najwyzsza = $max_local;
                    $czujnik = $i + 1;
                }
            }
        }
        return ['temp' => $najwyzsza, 'czujnik' => $czujnik];
    }
    function znajdzSredniaTemperature($tablica) {
        $suma = 0;
        $licznik = 0;
        for($i = 0; $i < count($tablica); $i++) {
            if (!empty($tablica[$i])) {
                $suma += array_sum($tablica[$i]);
                $licznik += count($tablica[$i]);
            }
        }
        return $licznik > 0 ? round($suma / $licznik, 2) : null;
    }
    function obliczSredniaTemperatureZewnetrzna($tablica){
        $suma = 0;
        $licznik = 0;
        for($i = 0; $i < count($tablica); $i++){
            $suma += $tablica[$i];
            $licznik++;
        }
        return $licznik > 0 ? round($suma / $licznik, 2) : null;
    }
    
    // Obliczenia
    $najnizsza = znajdzNajmniejszaTemperature($pomieszczenia[$pietro-1]);
    $najwyzsza = znajdzNajwyzszaTemperature($pomieszczenia[$pietro-1]);
    $srednia = znajdzSredniaTemperature($pomieszczenia[$pietro-1]);
    $sredniaTempZewn = obliczSredniaTemperatureZewnetrzna($temp_zewn);

    // Odpowiedź JSON
    $response = [
        'najmniejszaTemperatura' => $najnizsza['temp'],
        'najwyzszaTemperatura' => $najwyzsza['temp'],
        'sredniaTemperatura' => $srednia,
        'najnizszaTemperaturaCzujnik' => $najnizsza['czujnik'],
        'najwyzszaTemperaturaCzujnik' => $najwyzsza['czujnik'],
        'sredniaZewnetrzna' => $sredniaTempZewn
        
    ];

    echo json_encode($response);

    mysqli_close($conn);
?>