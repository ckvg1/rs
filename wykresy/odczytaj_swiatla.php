<?php
$conn = mysqli_connect("localhost","root","","plc_database");
$sql = "SELECT * FROM `light` WHERE data > now() - INTERVAL 7 day ";

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
if(isset($_GET["pietro"])) {
    $pietro = $_GET["pietro"];
}
$result = mysqli_query($conn, $sql);
while ($row=mysqli_fetch_assoc($result)) {
    $l1_1_1[] = $row['l1_1_1']; 
    $l1_1_2[] = $row['l1_1_2'];
    $l1_2_1[] = $row['l1_2_1'];
    $l1_2_2[] = $row['l1_2_2'];
    $l1_3_1[] = $row['l1_3_1'];
    $l1_3_2[] = $row['l1_3_2'];
    $l1_4_1[] = $row['l1_4_1'];
    $l1_4_2[] = $row['l1_4_2'];
    $l1_5_1[] = $row['l1_5_1'];
    $l1_5_2[] = $row['l1_5_2'];
    $l1_6_1[] = $row['l1_6_1'];
    $l1_6_2[] = $row['l1_6_2'];
    $l1_7_1[] = $row['l1_7_1'];
    $l1_7_2[] = $row['l1_7_2'];

    $l2_1_1[] = $row['l2_1_1']; 
    $l2_1_2[] = $row['l2_1_2'];
    $l2_2_1[] = $row['l2_2_1'];
    $l2_2_2[] = $row['l2_2_2'];
    $l2_3_1[] = $row['l2_3_1'];
    $l2_3_2[] = $row['l2_3_2'];
    $l2_4_1[] = $row['l2_4_1'];
    $l2_4_2[] = $row['l2_4_2'];
    $l2_5_1[] = $row['l2_5_1'];
    $l2_5_2[] = $row['l2_5_2'];
    $l2_6_1[] = $row['l2_6_1'];
    $l2_6_2[] = $row['l2_6_2'];
    $l2_7_1[] = $row['l2_7_1'];
    $l2_7_2[] = $row['l2_7_2'];

    $l3_1_1[] = $row['l3_1_1']; 
    $l3_1_2[] = $row['l3_1_2'];
    $l3_2_1[] = $row['l3_2_1'];
    $l3_2_2[] = $row['l3_2_2'];
    $l3_3_1[] = $row['l3_3_1'];
    $l3_3_2[] = $row['l3_3_2'];
    $l3_4_1[] = $row['l3_4_1'];
    $l3_4_2[] = $row['l3_4_2'];
    $l3_5_1[] = $row['l3_5_1'];
    $l3_5_2[] = $row['l3_5_2'];
    $l3_6_1[] = $row['l3_6_1'];
    $l3_6_2[] = $row['l3_6_2'];
    $l3_7_1[] = $row['l3_7_1'];
    $l3_7_2[] = $row['l3_7_2'];
    $data[] = $row['data'];
}
if(!isset($_GET['wszystko']) || $wyswietl_wszystkie_dane != 'true') {

    // Wysyła dane procentowe dla pary czujników
    // Dla czujnika 1_1 zwróci to samo co dla czujnika 1_2
echo json_encode([
    'l1_1_1'=>policzProcentDlaParyCzujnikow($l1_1_1, $l1_1_2),
    'l1_1_2'=>policzProcentDlaParyCzujnikow($l1_1_1, $l1_1_2),
    'l1_2_1'=>policzProcentDlaParyCzujnikow($l1_2_1, $l1_2_2),
    'l1_2_2'=>policzProcentDlaParyCzujnikow($l1_2_1, $l1_2_2),
    'l1_3_1'=>policzProcentDlaParyCzujnikow($l1_3_1, $l1_3_2),
    'l1_3_2'=>policzProcentDlaParyCzujnikow($l1_3_1, $l1_3_2),
    'l1_4_1'=>policzProcentDlaParyCzujnikow($l1_4_1, $l1_4_2),
    'l1_4_2'=>policzProcentDlaParyCzujnikow($l1_4_1, $l1_4_2),
    'l1_5_1'=>policzProcentDlaParyCzujnikow($l1_5_1, $l1_5_2),
    'l1_5_2'=>policzProcentDlaParyCzujnikow($l1_5_1, $l1_5_2),
    'l1_6_1'=>policzProcentDlaParyCzujnikow($l1_6_1, $l1_6_2),   
    'l1_6_2'=>policzProcentDlaParyCzujnikow($l1_6_1, $l1_6_2),
    'l1_7_1'=>policzProcentDlaParyCzujnikow($l1_7_1, $l1_7_2),
    'l1_7_2'=>policzProcentDlaParyCzujnikow($l1_7_1, $l1_7_2),

    'l2_1_1'=>policzProcentDlaParyCzujnikow($l2_1_1, $l2_1_2),
    'l2_1_2'=>policzProcentDlaParyCzujnikow($l2_1_1, $l2_1_2),
    'l2_2_1'=>policzProcentDlaParyCzujnikow($l2_2_1, $l2_2_2),
    'l2_2_2'=>policzProcentDlaParyCzujnikow($l2_2_1, $l2_2_2),
    'l2_3_1'=>policzProcentDlaParyCzujnikow($l2_3_1, $l2_3_2),
    'l2_3_2'=>policzProcentDlaParyCzujnikow($l2_3_1, $l2_3_2),
    'l2_4_1'=>policzProcentDlaParyCzujnikow($l2_4_1, $l2_4_2),
    'l2_4_2'=>policzProcentDlaParyCzujnikow($l2_4_1, $l2_4_2),
    'l2_5_1'=>policzProcentDlaParyCzujnikow($l2_5_1, $l2_5_2),
    'l2_5_2'=>policzProcentDlaParyCzujnikow($l2_5_1, $l2_5_2),
    'l2_6_1'=>policzProcentDlaParyCzujnikow($l2_6_1, $l2_6_2),   
    'l2_6_2'=>policzProcentDlaParyCzujnikow($l2_6_1, $l2_6_2),
    'l2_7_1'=>policzProcentDlaParyCzujnikow($l2_7_1, $l2_7_2),
    'l2_7_2'=>policzProcentDlaParyCzujnikow($l2_7_1, $l2_7_2),

    'l3_1_1'=>policzProcentDlaParyCzujnikow($l3_1_1, $l3_1_2),
    'l3_1_2'=>policzProcentDlaParyCzujnikow($l3_1_1, $l3_1_2),
    'l3_2_1'=>policzProcentDlaParyCzujnikow($l3_2_1, $l3_2_2),
    'l3_2_2'=>policzProcentDlaParyCzujnikow($l3_2_1, $l3_2_2),
    'l3_3_1'=>policzProcentDlaParyCzujnikow($l3_3_1, $l3_3_2),
    'l3_3_2'=>policzProcentDlaParyCzujnikow($l3_3_1, $l3_3_2),
    'l3_4_1'=>policzProcentDlaParyCzujnikow($l3_4_1, $l3_4_2),
    'l3_4_2'=>policzProcentDlaParyCzujnikow($l3_4_1, $l3_4_2),
    'l3_5_1'=>policzProcentDlaParyCzujnikow($l3_5_1, $l3_5_2),
    'l3_5_2'=>policzProcentDlaParyCzujnikow($l3_5_1, $l3_5_2),
    'l3_6_1'=>policzProcentDlaParyCzujnikow($l3_6_1, $l3_6_2),   
    'l3_6_2'=>policzProcentDlaParyCzujnikow($l3_6_1, $l3_6_2),
    'l3_7_1'=>policzProcentDlaParyCzujnikow($l3_7_1, $l3_7_2),
    'l3_7_2'=>policzProcentDlaParyCzujnikow($l3_7_1, $l3_7_2),
    'data'=>$data
]);
} else {
    $response = [];

    if ($pietro == '1') {
        $response = [
            'l1_1_1' => $l1_1_1,
            'l1_1_2' => $l1_1_2,
            'l1_2_1' => $l1_2_1,
            'l1_2_2' => $l1_2_2,
            'l1_3_1' => $l1_3_1,
            'l1_3_2' => $l1_3_2,
            'l1_4_1' => $l1_4_1,
            'l1_4_2' => $l1_4_2,
            'l1_5_1' => $l1_5_1,
            'l1_5_2' => $l1_5_2,
            'l1_6_1' => $l1_6_1,
            'l1_6_2' => $l1_6_2,
            'l1_7_1' => $l1_7_1,
            'l1_7_2' => $l1_7_2
        ];
    } elseif ($pietro == 2) {
        $response = [
            'l2_1_1' => $l2_1_1,
            'l2_1_2' => $l2_1_2,
            'l2_2_1' => $l2_2_1,
            'l2_2_2' => $l2_2_2,
            'l2_3_1' => $l2_3_1,
            'l2_3_2' => $l2_3_2,
            'l2_4_1' => $l2_4_1,
            'l2_4_2' => $l2_4_2,
            'l2_5_1' => $l2_5_1,
            'l2_5_2' => $l2_5_2,
            'l2_6_1' => $l2_6_1,
            'l2_6_2' => $l2_6_2,
            'l2_7_1' => $l2_7_1,
            'l2_7_2' => $l2_7_2
        ];
    } elseif ($pietro == 3) {
        $response = [
            'l3_1_1' => $l3_1_1,
            'l3_1_2' => $l3_1_2,
            'l3_2_1' => $l3_2_1,
            'l3_2_2' => $l3_2_2,
            'l3_3_1' => $l3_3_1,
            'l3_3_2' => $l3_3_2,
            'l3_4_1' => $l3_4_1,
            'l3_4_2' => $l3_4_2,
            'l3_5_1' => $l3_5_1,
            'l3_5_2' => $l3_5_2,
            'l3_6_1' => $l3_6_1,
            'l3_6_2' => $l3_6_2,
            'l3_7_1' => $l3_7_1,
            'l3_7_2' => $l3_7_2
        ];
    }

    $response['data'] = $data;
    echo json_encode($response);
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