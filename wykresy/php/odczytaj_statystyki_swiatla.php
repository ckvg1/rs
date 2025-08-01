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
     switch($pietro){
    case 1:
        $sql = "SELECT 
    SUM(l1_1_1) AS l1_1_1_count,
    SUM(l1_1_2) AS l1_1_2_count,
    SUM(l1_2_1) AS l1_2_1_count,
    SUM(l1_2_2) AS l1_2_2_count,
    SUM(l1_3_1) AS l1_3_1_count,
    SUM(l1_3_2) AS l1_3_2_count,
    SUM(l1_4_1) AS l1_4_1_count,
    SUM(l1_4_2) AS l1_4_2_count,
    SUM(l1_5_1) AS l1_5_1_count,
    SUM(l1_5_2) AS l1_5_2_count,
    SUM(l1_6_1) AS l1_6_1_count,
    SUM(l1_6_2) AS l1_6_2_count,
    SUM(l1_7_1) AS l1_7_1_count,
    SUM(l1_7_2) AS l1_7_2_count
FROM light
WHERE data > NOW() - INTERVAL ". $interval ." DAY
";
        break;
    case 2:
        $sql = "SELECT 
    SUM(l2_1_1) AS l2_1_1_count,
    SUM(l2_1_2) AS l2_1_2_count,
    SUM(l2_2_1) AS l2_2_1_count,
    SUM(l2_2_2) AS l2_2_2_count,
    SUM(l2_3_1) AS l2_3_1_count,
    SUM(l2_3_2) AS l2_3_2_count,
    SUM(l2_4_1) AS l2_4_1_count,
    SUM(l2_4_2) AS l2_4_2_count,
    SUM(l2_5_1) AS l2_5_1_count,
    SUM(l2_5_2) AS l2_5_2_count,
    SUM(l2_6_1) AS l2_6_1_count,
    SUM(l2_6_2) AS l2_6_2_count,
    SUM(l2_7_1) AS l2_7_1_count,
    SUM(l2_7_2) AS l2_7_2_count
FROM light
WHERE data > NOW() - INTERVAL ". $interval ." DAY
";
        break;
    case 3:
        $sql = "SELECT 
    SUM(l3_1_1) AS l3_1_1_count,
    SUM(l3_1_2) AS l3_1_2_count,
    SUM(l3_2_1) AS l3_2_1_count,
    SUM(l3_2_2) AS l3_2_2_count,
    SUM(l3_3_1) AS l3_3_1_count,
    SUM(l3_3_2) AS l3_3_2_count,
    SUM(l3_4_1) AS l3_4_1_count,
    SUM(l3_4_2) AS l3_4_2_count,
    SUM(l3_5_1) AS l3_5_1_count,
    SUM(l3_5_2) AS l3_5_2_count,
    SUM(l3_6_1) AS l3_6_1_count,
    SUM(l3_6_2) AS l3_6_2_count,
    SUM(l3_7_1) AS l3_7_1_count,
    SUM(l3_7_2) AS l3_7_2_count
FROM light
WHERE data > NOW() - INTERVAL ". $interval ." DAY
";
        break;
    default:
        die("Nieprawidłowe piętro");
}

     // Inicjalizacja tablic
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

     $data = [];
     $result = mysqli_query($conn, $sql);
     while($row = mysqli_fetch_assoc($result)) {
         
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
        $swiatla = [
            [$l1_1_1, $l1_1_2, $l1_2_1, $l1_2_2, $l1_3_1, $l1_3_2, $l1_4_1, $l1_4_2, $l1_5_1, $l1_5_2, $l1_6_1, $l1_6_2, $l1_7_1, $l1_7_2],
            [$l2_1_1, $l2_1_2, $l2_2_1, $l2_2_2, $l2_3_1, $l2_3_2, $l2_4_1, $l2_4_2, $l2_5_1, $l2_5_2, $l2_6_1, $l2_6_2, $l2_7_1, $l2_7_2],
            [$l3_1_1, $l3_1_2, $l3_2_1, $l3_2_2, $l3_3_1, $l3_3_2, $l3_4_1, $l3_4_2, $l3_5_1, $l3_5_2, $l3_6_1, $l3_6_2, $l3_7_1, $l3_7_2]
        ];
            
        //function obliczNajczesciejWlaczoneSwiatla($swiatla) {
        //    $licznik = [];
        //    for ($i = 0; $i < count($swiatla); $i++) {
        //        $licznik[$i] = 0;
        //        for ($j = 0; $j < count($swiatla[$i]); $j++) {
        //            if ($swiatla[$i][$j] == 1) {
        //                $licznik[$i]++;
        //            }
        //        }
        //    }
        //    $maxIndex = 0;
        //    for ($k = 1; $k < count($licznik); $k++) {
        //        if ($licznik[$k] > $licznik[$maxIndex]) {
        //            $maxIndex = $k;
        //        }
        //    }
        //    return $maxIndex + 1 ; // numer światła (1-based)
        //}
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

        $pietro1 = isset($_COOKIE["pietro1"]) ? json_decode($_COOKIE["pietro1"], true) : [];
        $pietro2 = isset($_COOKIE["pietro2"]) ? json_decode($_COOKIE["pietro2"], true) : [];
        $pietro3 = isset($_COOKIE["pietro3"]) ? json_decode($_COOKIE["pietro3"], true) : [];

        $najczesciejWlaczoneSwiatlo = obliczNajczesciejWlaczoneSwiatla($swiatla[$pietro - 1]);
        $najrzadziejWlaczoneSwiatlo = obliczNajrzadziejWlaczoneSwiatlo($swiatla[$pietro - 1]);
        //nazwy czujnikow pobrane z ciasteczek
        $nazwy_czujnikow = [
            [$pietro1["l1_1_1"],$pietro1["l1_1_2"], $pietro1["l1_2_1"], $pietro1["l1_2_2"], $pietro1["l1_3_1"], $pietro1["l1_3_2"], $pietro1["l1_4_1"], $pietro1["l1_4_2"], $pietro1["l1_5_1"], $pietro1["l1_5_2"], $pietro1["l1_6_1"], $pietro1["l1_6_2"], $pietro1["l1_7_1"], $pietro1["l1_7_2"]],
            [$pietro2["l2_1_1"],$pietro2["l2_1_2"], $pietro2["l2_2_1"], $pietro2["l2_2_2"], $pietro2["l2_3_1"], $pietro2["l2_3_2"], $pietro2["l2_4_1"], $pietro2["l2_4_2"], $pietro2["l2_5_1"], $pietro2["l2_5_2"], $pietro2["l2_6_1"], $pietro2["l2_6_2"], $pietro2["l2_7_1"], $pietro2["l2_7_2"]],
            [$pietro3["l3_1_1"],$pietro3["l3_1_2"], $pietro3["l3_2_1"], $pietro3["l3_2_2"], $pietro3["l3_3_1"], $pietro3["l3_3_2"], $pietro3["l3_4_1"], $pietro3["l3_4_2"], $pietro3["l3_5_1"], $pietro3["l3_5_2"], $pietro3["l3_6_1"], $pietro3["l3_6_2"], $pietro3["l3_7_1"], $pietro3["l3_7_2"]]
        ];
       
        function obliczCzasWlaczoneSwiatlo($swiatla) {
            return array_sum(array_map('array_sum', $swiatla));
        }

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