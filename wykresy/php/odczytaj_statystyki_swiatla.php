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

     switch($pietro){
        case 1:
            $sql= "SELECT l1_1_1, l1_1_2, l1_2_1, l1_2_2, l1_3_1, l1_3_2, 
                        l1_4_1, l1_4_2, l1_5_1, l1_5_2, 
                        l1_6_1, l1_6_2, l1_7_1, l1_7_2,
                        data
                        data
                        FROM light  
                        WHERE data > NOW() - INTERVAL 7 DAY 
                        ORDER BY data DESC";
                        WHERE data > NOW() - INTERVAL 7 DAY 
                        ORDER BY data DESC";
            break;
        case 2:
            $sql = "SELECT l2_1_1, l2_1_2, l2_2_1, l2_2_2, l2_3_1, l2_3_2, 
                        l2_4_1, l2_4_2, l2_5_1, l2_5_2, 
                        l2_6_1, l2_6_2, l2_7_1, l2_7_2,
                        data
                        FROM light  
                        WHERE data > NOW() - INTERVAL 7 DAY 
                        ORDER BY data DESC";
            break;
        case 3:
            $sql = "SELECT l3_1_1, l3_1_2, l3_2_1, l3_2_2, l3_3_1, l3_3_2, 
                        l3_4_1, l3_4_2, l3_5_1, l3_5_2, 
                        l3_6_1, l3_6_2, l3_7_1, l3_7_2,
                        data
                        FROM light  
                        WHERE data > NOW() - INTERVAL 7 DAY 
                        ORDER BY data DESC";
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
         $czas[] = $row['data'];
         switch($pietro){
             case 1:
                 $l1_1_1[] = round($row['l1_1_1']);
                 $l1_1_2[] = round($row['l1_1_2']);
                 $l1_2_1[] = round($row['l1_2_1']);
                 $l1_2_2[] = round($row['l1_2_2']);
                 $l1_3_1[] = round($row['l1_3_1']);
                 $l1_3_2[] = round($row['l1_3_2']);
                 $l1_4_1[] = round($row['l1_4_1']);
                 $l1_4_2[] = round($row['l1_4_2']);
                 $l1_5_1[] = round($row['l1_5_1']);
                 $l1_5_2[] = round($row['l1_5_2']);
                 $l1_6_1[] = round($row['l1_6_1']);
                 $l1_6_2[] = round($row['l1_6_2']);
                 $l1_7_1[] = round($row['l1_7_1']);
                 $l1_7_2[] = round($row['l1_7_2']);
                 break;
             case 2:
                 $l2_1_1[] = round($row['l2_1_1']);
                 $l2_1_2[] = round($row['l2_1_2']);
                 $l2_2_1[] = round($row['l2_2_1']);
                 $l2_2_2[] = round($row['l2_2_2']);
                 $l2_3_1[] = round($row['l2_3_1']);
                 $l2_3_2[] = round($row['l2_3_2']);
                 $l2_4_1[] = round($row['l2_4_1']);
                 $l2_4_2[] = round($row['l2_4_2']);
                 $l2_5_1[] = round($row['l2_5_1']);
                 $l2_5_2[] = round($row['l2_5_2']);
                 $l2_6_1[] = round($row['l2_6_1']);
                 $l2_6_2[] = round($row['l2_6_2']);
                 $l2_7_1[] = round($row['l2_7_1']);
                 $l2_7_2[] = round($row['l2_7_2']);
                 break;
             case 3:    
                 $l3_1_1[] = round($row['l3_1_1']);
                 $l3_1_2[] = round($row['l3_1_2']);
                 $l3_2_1[] = round($row['l3_2_1']);
                 $l3_2_2[] = round($row['l3_2_2']);
                 $l3_3_1[] = round($row['l3_3_1']);
                 $l3_3_2[] = round($row['l3_3_2']);
                 $l3_4_1[] = round($row['l3_4_1']);
                 $l3_4_2[] = round($row['l3_4_2']);
                 $l3_5_1[] = round($row['l3_5_1']);
                 $l3_5_2[] = round($row['l3_5_2']);
                 $l3_6_1[] = round($row['l3_6_1']);
                 $l3_6_2[] = round($row['l3_6_2']);
                 $l3_7_1[] = round($row['l3_7_1']);
                 $l3_7_2[] = round($row['l3_7_2']);
                 break;
            }
            
        }    
        $swiatla = [
            [$l1_1_1, $l1_1_2, $l1_2_1, $l1_2_2, $l1_3_1, $l1_3_2, $l1_4_1, $l1_4_2, $l1_5_1, $l1_5_2, $l1_6_1, $l1_6_2, $l1_7_1, $l1_7_2],
            [$l2_1_1, $l2_1_2, $l2_2_1, $l2_2_2, $l2_3_1, $l2_3_2, $l2_4_1, $l2_4_2, $l2_5_1, $l2_5_2, $l2_6_1, $l2_6_2, $l2_7_1, $l2_7_2],
            [$l3_1_1, $l3_1_2, $l3_2_1, $l3_2_2, $l3_3_1, $l3_3_2, $l3_4_1, $l3_4_2, $l3_5_1, $l3_5_2, $l3_6_1, $l3_6_2, $l3_7_1, $l3_7_2]
        ];
            
        function obliczNajczesciejWlaczoneSwiatla($swiatla) {
            $licznik = [];
            for ($i = 0; $i < count($swiatla); $i++) {
                $licznik[$i] = 0;
                for ($j = 0; $j < count($swiatla[$i]); $j++) {
                    if ($swiatla[$i][$j] == 1) {
                        $licznik[$i]++;
                    }
                }
            }
            $maxIndex = 0;
            for ($k = 1; $k < count($licznik); $k++) {
                if ($licznik[$k] > $licznik[$maxIndex]) {
                    $maxIndex = $k;
                }
            }
            return $maxIndex + 1 ; // numer światła (1-based)
        }

        $najczesciejWlaczoneSwiatlo = 1;
        switch($pietro){
            case 1:
                $najczesciejWlaczoneSwiatlo = obliczNajczesciejWlaczoneSwiatla($swiatla[0]);
                break;
            case 2:
                $najczesciejWlaczoneSwiatlo = obliczNajczesciejWlaczoneSwiatla($swiatla[1]);
                break;
            case 3:
                $najczesciejWlaczoneSwiatlo = obliczNajczesciejWlaczoneSwiatla($swiatla[2]);
                break;
        }

        
        
        $response = [
            'najczesciejWlaczoneSwiatlo' => $najczesciejWlaczoneSwiatlo,
            
        ];
        echo json_encode($response);

        mysqli_close($conn);
?>