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
    $czas = isset($_GET["czas"]) ? intval($_GET["czas"]) : 1;
    $interval = 1;

    
    //ustawiamy interwał na podstawie czasu
    if($pietro < 1 || $pietro > 3) {
        die("Nieprawidłowe piętro");
    }
    
    // Sprawdzenie poprawności parametru czas
    if($czas < 1 || $czas > 3) {
        die("Nieprawidłowy czas");
    }
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
    // Zapytanie SQL
    $sql = "SELECT SUM(l{$pietro}_1_1) AS l{$pietro}_1_1_count,
    SUM(l{$pietro}_1_2) AS l{$pietro}_1_2_count,
    SUM(l{$pietro}_2_1) AS l{$pietro}_2_1_count,
    SUM(l{$pietro}_2_2) AS l{$pietro}_2_2_count,
    SUM(l{$pietro}_3_1) AS l{$pietro}_3_1_count,
    SUM(l{$pietro}_3_2) AS l{$pietro}_3_2_count,
    SUM(l{$pietro}_4_1) AS l{$pietro}_4_1_count,
    SUM(l{$pietro}_4_2) AS l{$pietro}_4_2_count,
    SUM(l{$pietro}_5_1) AS l{$pietro}_5_1_count,
    SUM(l{$pietro}_5_2) AS l{$pietro}_5_2_count,
    SUM(l{$pietro}_6_1) AS l{$pietro}_6_1_count,
    SUM(l{$pietro}_6_2) AS l{$pietro}_6_2_count,
    SUM(l{$pietro}_7_1) AS l{$pietro}_7_1_count,
    SUM(l{$pietro}_7_2) AS l{$pietro}_7_2_count
    FROM light
    WHERE data > NOW() - INTERVAL ". $interval ." DAY";

     // Inicjalizacja tablic do przechowywania danych pobranych z bazy
     if($pietro == 1) {
        $l1_1_1 = [];
        $l1_1_2 = [];
        $l1_2_1 = [];
        $l1_2_2 = [];
        $l1_3_1 = [];
        $l1_3_2 = [];
        $l1_4_1 = [];
        $l1_4_2 = [];
        $l1_5_1 = [];
        $l1_5_2 = [];
        $l1_6_1 = [];
        $l1_6_2 = [];
        $l1_7_1 = [];
        $l1_7_2 = [];
     }else if($pietro == 2) {
        $l2_1_1 = [];
        $l2_1_2 = [];
        $l2_2_1 = [];
        $l2_2_2 = [];
        $l2_3_1 = [];
        $l2_3_2 = [];
        $l2_4_1 = [];
        $l2_4_2 = [];
        $l2_5_1 = [];
        $l2_5_2 = [];
        $l2_6_1 = [];
        $l2_6_2 = [];
        $l2_7_1 = [];
        $l2_7_2 = [];
    }else if($pietro == 3) {
        $l3_1_1 = [];
        $l3_1_2 = [];
        $l3_2_1 = [];
        $l3_2_2 = [];
        $l3_3_1 = [];
        $l3_3_2 = [];
        $l3_4_1 = [];
        $l3_4_2 = [];
        $l3_5_1 = [];
        $l3_5_2 = [];
        $l3_6_1 = [];
        $l3_6_2 = [];
        $l3_7_1 = [];
        $l3_7_2 = [];
    }
    
    // Wykonanie zapytania
     $result = mysqli_query($conn, $sql);

     //Przypisanie danych z bazy do wcześniej zainicjalizowanych tablic
     if (!$result) {
         die("Błąd zapytania SQL: " . mysqli_error($conn));
     }
     while($row = mysqli_fetch_assoc($result)) {
         //W zaleznosci od pietra przypisujemy dane do odpowiednich tablic
         switch($pietro){
             case 1:
                 $l1_1_1[] = ($row['l1_1_1_count']);
                 $l1_1_2[] = ($row['l1_1_2_count']);
                 $l1_2_1[] = ($row['l1_2_1_count']);
                 $l1_2_2[] = ($row['l1_2_2_count']);
                 $l1_3_1[] = ($row['l1_3_1_count']);
                 $l1_3_2[] = ($row['l1_3_2_count']);
                 $l1_4_1[] = ($row['l1_4_1_count']);
                 $l1_4_2[] = ($row['l1_4_2_count']);
                 $l1_5_1[] = ($row['l1_5_1_count']);
                 $l1_5_2[] = ($row['l1_5_2_count']);
                 $l1_6_1[] = ($row['l1_6_1_count']);
                 $l1_6_2[] = ($row['l1_6_2_count']);
                 $l1_7_1[] = ($row['l1_7_1_count']);
                 $l1_7_2[] = ($row['l1_7_2_count']);
                 break;
             case 2:
                 $l2_1_1[] = ($row['l2_1_1_count']);
                 $l2_1_2[] = ($row['l2_1_2_count']);
                 $l2_2_1[] = ($row['l2_2_1_count']);
                 $l2_2_2[] = ($row['l2_2_2_count']);
                 $l2_3_1[] = ($row['l2_3_1_count']);
                 $l2_3_2[] = ($row['l2_3_2_count']);
                 $l2_4_1[] = ($row['l2_4_1_count']);
                 $l2_4_2[] = ($row['l2_4_2_count']);
                 $l2_5_1[] = ($row['l2_5_1_count']);
                 $l2_5_2[] = ($row['l2_5_2_count']);
                 $l2_6_1[] = ($row['l2_6_1_count']);
                 $l2_6_2[] = ($row['l2_6_2_count']);
                 $l2_7_1[] = ($row['l2_7_1_count']);
                 $l2_7_2[] = ($row['l2_7_2_count']);
                 break;
             case 3:    
                 $l3_1_1[] = ($row['l3_1_1_count']);
                 $l3_1_2[] = ($row['l3_1_2_count']);
                 $l3_2_1[] = ($row['l3_2_1_count']);
                 $l3_2_2[] = ($row['l3_2_2_count']);
                 $l3_3_1[] = ($row['l3_3_1_count']);
                 $l3_3_2[] = ($row['l3_3_2_count']);
                 $l3_4_1[] = ($row['l3_4_1_count']);
                 $l3_4_2[] = ($row['l3_4_2_count']);
                 $l3_5_1[] = ($row['l3_5_1_count']);
                 $l3_5_2[] = ($row['l3_5_2_count']);
                 $l3_6_1[] = ($row['l3_6_1_count']);
                 $l3_6_2[] = ($row['l3_6_2_count']);
                 $l3_7_1[] = ($row['l3_7_1_count']);
                 $l3_7_2[] = ($row['l3_7_2_count']);
                 break;
            }
            
        }    
        // Tablica swiatla do latwiejszego poruszania sie po danych. swiatla[pietro-1]
        // gdzie pietro to 1,2,3 a numer_swiatla to 1,2,3,4,5,6,7
        // swiatla[pietro-1] = [l1_1_1, l1_1_2, l1_2_1, l1_2_2, l1_3_1, l1_3_2, l1_4_1, l1_4_2, l1_5_1, l1_5_2, l1_6_1, l1_6_2, l1_7_1, l1_7_2]
        $swiatla = [
            [$l1_1_1, $l1_1_2, $l1_2_1, $l1_2_2, $l1_3_1, $l1_3_2, $l1_4_1, $l1_4_2, $l1_5_1, $l1_5_2, $l1_6_1, $l1_6_2, $l1_7_1, $l1_7_2],
            [$l2_1_1, $l2_1_2, $l2_2_1, $l2_2_2, $l2_3_1, $l2_3_2, $l2_4_1, $l2_4_2, $l2_5_1, $l2_5_2, $l2_6_1, $l2_6_2, $l2_7_1, $l2_7_2],
            [$l3_1_1, $l3_1_2, $l3_2_1, $l3_2_2, $l3_3_1, $l3_3_2, $l3_4_1, $l3_4_2, $l3_5_1, $l3_5_2, $l3_6_1, $l3_6_2, $l3_7_1, $l3_7_2]
        ];
        // Jako parametr podajemy z ktorego piętra liczymy najczesciej i najrzadziej włączane światło
        function obliczNajczesciejWlaczoneSwiatla($swiatla) {
            $licznik = [];
            foreach ($swiatla as $index => $wartosci) {
                $licznik[$index] = array_sum($wartosci);
            }
            return array_search(max($licznik), $licznik) + 1; // numer światła (1-based)
        }
        function obliczNajrzadziejWlaczoneSwiatlo($swiatla) {
            $licznik = [];
            foreach ($swiatla as $index => $wartosci) {
                $licznik[$index] = array_sum($wartosci);
            }
            return array_search(min($licznik), $licznik) + 1; // numer światła (1-based)
        }   
        // Pobranie nazw czujników z ciasteczek
        // Zakładamy, że ciasteczka są zapisane w formacie JSON
        // Przykład: {"l1_1_1": "Czujnik 1-1-1", "l1_1_2": "Czujnik 1-1-2", ...}
        // W przypadku braku ciasteczek, używamy pustych tablic
        $pietro1 = isset($_COOKIE["pietro1"]) ? json_decode($_COOKIE["pietro1"], true) : [];
        $pietro2 = isset($_COOKIE["pietro2"]) ? json_decode($_COOKIE["pietro2"], true) : [];    
        $pietro3 = isset($_COOKIE["pietro3"]) ? json_decode($_COOKIE["pietro3"], true) : [];

        // Obliczenie najczęściej i najrzadziej włączanych świateł
        // Funkcje obliczające najczęściej i najrzadziej włączane światła
        $najczesciejWlaczoneSwiatlo = obliczNajczesciejWlaczoneSwiatla($swiatla[$pietro - 1]);
        $najrzadziejWlaczoneSwiatlo = obliczNajrzadziejWlaczoneSwiatlo($swiatla[$pietro - 1]);
        //nazwy czujnikow pobrane z ciasteczek przypisujemy do tablicy tak aby łatwo można było się do nich odwołać
        $nazwy_czujnikow = [
            [$pietro1["l1_1_1"],$pietro1["l1_1_2"], $pietro1["l1_2_1"], $pietro1["l1_2_2"], $pietro1["l1_3_1"], $pietro1["l1_3_2"], $pietro1["l1_4_1"], $pietro1["l1_4_2"], $pietro1["l1_5_1"], $pietro1["l1_5_2"], $pietro1["l1_6_1"], $pietro1["l1_6_2"], $pietro1["l1_7_1"], $pietro1["l1_7_2"]],
            [$pietro2["l2_1_1"],$pietro2["l2_1_2"], $pietro2["l2_2_1"], $pietro2["l2_2_2"], $pietro2["l2_3_1"], $pietro2["l2_3_2"], $pietro2["l2_4_1"], $pietro2["l2_4_2"], $pietro2["l2_5_1"], $pietro2["l2_5_2"], $pietro2["l2_6_1"], $pietro2["l2_6_2"], $pietro2["l2_7_1"], $pietro2["l2_7_2"]],
            [$pietro3["l3_1_1"],$pietro3["l3_1_2"], $pietro3["l3_2_1"], $pietro3["l3_2_2"], $pietro3["l3_3_1"], $pietro3["l3_3_2"], $pietro3["l3_4_1"], $pietro3["l3_4_2"], $pietro3["l3_5_1"], $pietro3["l3_5_2"], $pietro3["l3_6_1"], $pietro3["l3_6_2"], $pietro3["l3_7_1"], $pietro3["l3_7_2"]]
        ];
        // Obliczenie czasu włączenia świateł
        // Funkcja do obliczenia czasu włączenia świateł
        // Zakładamy, że każde wystąpienie to 5 minut
        // Można to zmienić w zależności od potrzeb
        function obliczCzasWlaczoneSwiatlo($swiatla) {
            return array_sum(array_map('array_sum', $swiatla));
        }

        // Obliczenie czasu włączenia świateł
        // Ponownie jako argument podajemy tablicę z danymi dla danego piętra
        $czas_wlaczone_swiatlo = obliczCzasWlaczoneSwiatlo($swiatla[$pietro - 1]) ; // zakładając, że każde wystąpienie to 5 minut
        
        
        // Przygotowanie odpowiedzi
        $response = [
            'najczesciejWlaczoneSwiatlo' => $nazwy_czujnikow[$pietro-1][$najczesciejWlaczoneSwiatlo],
            'najrzadziejWlaczoneSwiatlo' => $nazwy_czujnikow[$pietro-1][$najrzadziejWlaczoneSwiatlo],  
            'czasWlaczoneSwiatlo' => round(($czas_wlaczone_swiatlo * 5)/60,1),
        ];
        echo json_encode($response);

        mysqli_close($conn);
?>