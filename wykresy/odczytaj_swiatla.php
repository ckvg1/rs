<?php
$conn = mysqli_connect("localhost","root","","plc_database");
$sql = "SELECT * FROM `light` WHERE data > now() - INTERVAL 7 day ";
$l1_1 = []; 
$l1_2 = [];
$l2_1 = [];
$l2_2 = [];
$l3_1 = [];
$l3_2 = [];
$l4_1 = [];
$l4_2 = [];
$l5_1 = [];
$l5_2 = [];
$l6_1 = [];
$l6_2 = [];
$l7_1 = [];
$l7_2 = [];
$data = [];
if(isset($_GET["od"])) {
    $data_od = $_GET["od"];
}
if(isset($_GET["do"])) {
    $data_do = $_GET["do"];
}

if (isset($data_do) && isset($data_od)) {
    $sql = "SELECT * FROM `light` WHERE `data` BETWEEN '$data_od' AND '$data_do'"; 
}
if(isset($_GET["wszystko"])) {
    $wyswietl_wszystkie_dane = $_GET["wszystko"];
}
$result = mysqli_query($conn, $sql);
while ($row=mysqli_fetch_assoc($result)) {
    $l1_1[] = $row['1_1']; 
    $l1_2[] = $row['1_2'];
    $l2_1[] = $row['2_1'];
    $l2_2[] = $row['2_2'];
    $l3_1[] = $row['3_1'];
    $l3_2[] = $row['3_2'];
    $l4_1[] = $row['4_1'];
    $l4_2[] = $row['4_2'];
    $l5_1[] = $row['5_1'];
    $l5_2[] = $row['5_2'];
    $l6_1[] = $row['6_1'];
    $l6_2[] = $row['6_2'];
    $l7_1[] = $row['7_1'];
    $l7_2[] = $row['7_2'];
    $data[] = $row['data'];
}
if(!isset($_GET['wszystko']) || $wyswietl_wszystkie_dane != 'true') {

    // Wysyła dane procentowe dla pary czujników
    // Dla czujnika 1_1 zwróci to samo co dla czujnika 1_2
echo json_encode([
    '1_1'=>policzProcentDlaParyCzujnikow($l1_1, $l1_2),
    '1_2'=>policzProcentDlaParyCzujnikow($l1_1, $l1_2),
    '2_1'=>policzProcentDlaParyCzujnikow($l2_1, $l2_2),
    '2_2'=>policzProcentDlaParyCzujnikow($l2_1, $l2_2),
    '3_1'=>policzProcentDlaParyCzujnikow($l3_1, $l3_2),
    '3_2'=>policzProcentDlaParyCzujnikow($l3_1, $l3_2),
    '4_1'=>policzProcentDlaParyCzujnikow($l4_1, $l4_2),
    '4_2'=>policzProcentDlaParyCzujnikow($l4_1, $l4_2),
    '5_1'=>policzProcentDlaParyCzujnikow($l5_1, $l5_2),
    '5_2'=>policzProcentDlaParyCzujnikow($l5_1, $l5_2),
    '6_1'=>policzProcentDlaParyCzujnikow($l6_1, $l6_2),   
    '6_2'=>policzProcentDlaParyCzujnikow($l6_1, $l6_2),
    '7_1'=>policzProcentDlaParyCzujnikow($l7_1, $l7_2),
    '7_2'=>policzProcentDlaParyCzujnikow($l7_1, $l7_2),
    'data'=>$data
]);
} else {
    echo json_encode([
        '1_1'=>$l1_1,
        '1_2'=>$l1_2,
        '2_1'=>$l2_1,
        '2_2'=>$l2_2,
        '3_1'=>$l3_1, 
        '3_2'=>$l3_2,
        '4_1'=>$l4_1, 
        '4_2'=>$l4_2,
        '5_1'=>$l5_1, 
        '5_2'=>$l5_2,
        '6_1'=>$l6_1,   
        '6_2'=>$l6_2,
        '7_1'=>$l7_1, 
        '7_2'=>$l7_2,
        'data'=>$data
    ]);
}

mysqli_close($conn);
function policzProcentDlaParyCzujnikow($czujnik1, $czujnik2) {
    $aktywnych = 0;
    $iloscOdczytow = count($czujnik1); // zakładamy, że oba mają tę samą liczbę elementów
    
    // Iterujemy przez każdy odczyt
    for ($i = 0; $i < $iloscOdczytow; $i++) {
        // Jeśli któregokolwiek z czujników jest włączony (1), uznajemy, że światło było włączone
        if ($czujnik1[$i] == 1 || $czujnik2[$i] == 1) {
            $aktywnych++;
        }
    }
    
    if ($iloscOdczytow > 0) {
        $procent = round(($aktywnych / $iloscOdczytow) * 100);
    } else {
        $procent = 0;
    }
    
    return $procent;
}

?>