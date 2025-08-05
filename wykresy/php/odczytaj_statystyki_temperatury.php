<?php
    // Połączenie z bazą danych
    $conn = mysqli_connect("localhost", "root", "", "plc_database");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
    //error_reporting(E_ALL);

    // Parametr GET
    $pietro = isset($_GET["pietro"]) ? intval($_GET["pietro"]) : 1;
    $czas = isset($_GET["czas"]) ? intval($_GET["czas"]) : 2; // Domyślnie 1 to 24h
    $interval;
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
    // Zapytanie SQL w zależności od piętra
    switch($pietro){
        case 1:
            $sql_temp = "SELECT t1_1, t1_2, t1_3, t1_4, t1_5, t1_6, t1_7, 
                        t_zewn, czas_dodania 
                        FROM temperatura 
                        WHERE czas_dodania > NOW() - INTERVAL $interval DAY 
                        ORDER BY czas_dodania DESC";
            

            break;
        case 2:
            $sql_temp = "SELECT t2_1, t2_2, t2_3, t2_4, t2_5, t2_6, t2_7, 
                        t_zewn, czas_dodania 
                        FROM temperatura 
                        WHERE czas_dodania > NOW() - INTERVAL $interval DAY 
                        ORDER BY czas_dodania DESC";
            
            break;
        case 3:
            $sql_temp = "SELECT t3_1, t3_2, t3_3, t3_4, t3_5, t3_6, t3_7, 
                        t_zewn, czas_dodania 
                        FROM temperatura 
                        WHERE czas_dodania > NOW() - INTERVAL $interval DAY 
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
        if (!$result || mysqli_num_rows($result) === 0) {
        // Brak danych w tabeli
        $null_response_text = "brak danych";
        // Czujniki pusta odpowiedz zeby nie pisac 2 razy tego samego na stronie
        $response = [
            'najmniejszaTemperatura' => $null_response_text,
            'najwyzszaTemperatura' => $null_response_text,
            'sredniaTemperatura' => $null_response_text,
            'najnizszaTemperaturaCzujnik' => "",
            'najwyzszaTemperaturaCzujnik' => "",
            'sredniaZewnetrzna' => $null_response_text
        ];
        echo json_encode($response);
        mysqli_close($conn);
        exit;
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $czas[] = $row['czas_dodania'];
        switch($pietro){
            case 1:
                if (!is_null($row['t1_1'])) $t1_1[] = round($row['t1_1'], 1);
                if (!is_null($row['t1_2'])) $t1_2[] = round($row['t1_2'], 1);
                if (!is_null($row['t1_3'])) $t1_3[] = round($row['t1_3'], 1);
                if (!is_null($row['t1_4'])) $t1_4[] = round($row['t1_4'], 1);
                if (!is_null($row['t1_5'])) $t1_5[] = round($row['t1_5'], 1);
                if (!is_null($row['t1_6'])) $t1_6[] = round($row['t1_6'], 1);
                if (!is_null($row['t1_7'])) $t1_7[] = round($row['t1_7'], 1);
                break; 
            case 2: 
                if (!is_null($row['t2_1'])) $t2_1[] = round($row['t2_1'], 1);
                if (!is_null($row['t2_2'])) $t2_2[] = round($row['t2_2'], 1);
                if (!is_null($row['t2_3'])) $t2_3[] = round($row['t2_3'], 1);
                if (!is_null($row['t2_4'])) $t2_4[] = round($row['t2_4'], 1);
                if (!is_null($row['t2_5'])) $t2_5[] = round($row['t2_5'], 1);
                if (!is_null($row['t2_6'])) $t2_6[] = round($row['t2_6'], 1);
                if (!is_null($row['t2_7'])) $t2_7[] = round($row['t2_7'], 1);
                break; 
            case 3: 
                if (!is_null($row['t3_1'])) $t3_1[] = round($row['t3_1'], 1);
                if (!is_null($row['t3_2'])) $t3_2[] = round($row['t3_2'], 1);
                if (!is_null($row['t3_3'])) $t3_3[] = round($row['t3_3'], 1);
                if (!is_null($row['t3_4'])) $t3_4[] = round($row['t3_4'], 1);
                if (!is_null($row['t3_5'])) $t3_5[] = round($row['t3_5'], 1);
                if (!is_null($row['t3_6'])) $t3_6[] = round($row['t3_6'], 1);
                if (!is_null($row['t3_7'])) $t3_7[] = round($row['t3_7'], 1);
                break;
        }
        if (!is_null($row['t_zewn'])) $temp_zewn[] = round($row['t_zewn'],1);
    }
    
    
    
    

    // Grupowanie pomieszczeń w piętra (pomiesczczenia[][] dla temperatury, swiatla[][] dla światła)
    $pomieszczenia = [
        [$t1_1, $t1_2, $t1_3, $t1_4, $t1_5, $t1_6, $t1_7],
        [$t2_1, $t2_2, $t2_3, $t2_4, $t2_5, $t2_6, $t2_7],
        [$t3_1, $t3_2, $t3_3, $t3_4, $t3_5, $t3_6, $t3_7]
    ];
   
    $pietro1 = isset($_COOKIE["pietro1"]) ? json_decode($_COOKIE["pietro1"], true) : [];
    $pietro2 = isset($_COOKIE["pietro2"]) ? json_decode($_COOKIE["pietro2"], true) : [];
    $pietro3 = isset($_COOKIE["pietro3"]) ? json_decode($_COOKIE["pietro3"], true) : [];
   
    $nazwy_czujnikow = [
        [$pietro1["t1_1"],$pietro1["t1_2"], $pietro1["t1_3"], $pietro1["t1_4"], $pietro1["t1_5"], $pietro1["t1_6"], $pietro1["t1_7"]],
        [$pietro2["t2_1"],$pietro2["t2_2"], $pietro2["t2_3"], $pietro2["t2_4"], $pietro2["t2_5"], $pietro2["t2_6"], $pietro2["t2_7"]],
        [$pietro3["t3_1"],$pietro3["t3_2"], $pietro3["t3_3"], $pietro3["t3_4"], $pietro3["t3_5"], $pietro3["t3_6"], $pietro3["t3_7"]]
    ];
        

    // Funkcje
    function znajdzNajmniejszaTemperature($tablica) {
        $najmniejsza = null;
        $czujnik = null;
        for ($i = 0; $i < count($tablica); $i++) {
            if (!empty($tablica[$i])) {
                $filtered = array_filter($tablica[$i], fn($val) => !is_null($val) && $val != 0 && $val != 99 && $val > 0 && $val <70);
                if (!empty($filtered)) {
                    $min_local = min($filtered);
                } else {
                    continue;
                }
                if ($najmniejsza === null || $min_local < $najmniejsza)   {
                    $najmniejsza = $min_local;
                    $czujnik = $i;
                }
            }
        }
        return $najmniejsza !== null ? ['temp' => $najmniejsza, 'czujnik' => $czujnik] : ['temp' => null, 'czujnik' => null];
    }
    function znajdzNajwyzszaTemperature($tablica) {
        $najwyzsza = null;
        $czujnik = null;
        for ($i = 0; $i < count($tablica); $i++) {
            if (!empty($tablica[$i])) {
                $filtered = array_filter($tablica[$i], fn($val) => !is_null($val) && $val != 0 && $val != 99 && $val > 0 && $val <70);
                if (!empty($filtered)) {
                    $max_local = max($filtered);
                } else {
                    continue;
                }
                if ($najwyzsza === null || $max_local > $najwyzsza) {
                    $najwyzsza = $max_local;
                    $czujnik = $i;
                }
            }
        }
        return $najwyzsza !== null ? ['temp' => $najwyzsza, 'czujnik' => $czujnik] : ['temp' => null, 'czujnik' => null];
    }
    function znajdzSredniaTemperature($tablica) {
        $suma = 0;
        $licznik = 0;
        for($i = 0; $i < count($tablica); $i++) {
            if (!empty($tablica[$i])) {
                $filtered = array_filter($tablica[$i], fn($val) => !is_null($val) && $val != 0 && $val != 99 && $val > 0 && $val <70);
                $suma += array_sum($filtered);
                $licznik += count($filtered);
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
    function sprawdzBledneDane($tablica, $nazwy) {
        $bledne = [];
        for ($i = 0; $i < count($tablica); $i++) {
            foreach ($tablica[$i] as $val) {
                if (is_null($val) || $val == 0 || $val == 99 || $val < 0 || $val > 70) {
                    $bledne[] = $nazwy[$i]; //wpisanie nazw wszyztkich czujnikow z blednymi danymi
                }
            }
        }
        return array_values(array_unique($bledne)); //usuniecie duplikatow z tablicy
    }
    
    // Obliczenia
    $najnizsza = znajdzNajmniejszaTemperature($pomieszczenia[$pietro-1]);
    $najwyzsza = znajdzNajwyzszaTemperature($pomieszczenia[$pietro-1]);
    $srednia = znajdzSredniaTemperature($pomieszczenia[$pietro-1]);
    $sredniaTempZewn = obliczSredniaTemperatureZewnetrzna($temp_zewn);

    $bledneCzujniki = sprawdzBledneDane($pomieszczenia[$pietro-1], $nazwy_czujnikow[$pietro-1]);

    // Odpowiedź JSON
    $response = [
        'najmniejszaTemperatura' => $najnizsza['temp'] ?? "Brak danych",
        'najwyzszaTemperatura' => $najwyzsza['temp'] ?? "Brak danych",
        'sredniaTemperatura' => $srednia ?? "Brak danych",
        'najnizszaTemperaturaCzujnik' => $nazwy_czujnikow[$pietro-1][$najnizsza['czujnik']] ?? "",
        'najwyzszaTemperaturaCzujnik' => $nazwy_czujnikow[$pietro-1][$najwyzsza['czujnik']] ?? "",
        'sredniaZewnetrzna' => $sredniaTempZewn ?? "Brak danych",
        'bledneDane' => !empty($bledneCzujniki),
        'czujnikBledneDane' => $bledneCzujniki
    ];

    echo json_encode($response);

    mysqli_close($conn);
?>