<?php 
header('Content-type: text/html; charset=utf-8');

/*
?t2_1=99.0&t2_2=99.0&t2_3=99.0&t2_4=99.0&t2_5=99.0&t2_6=99.0&
t2_7=99.0&l2=11111111111111&t3_1=27.3&t3_2=99.0&t3_3=99.0&
t3_4=99.0&t3_5=99.0&t3_6=99.0&t3_7=99.0&t_zewn=0.0&l3=11111111111111
*/

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "plc_database";

$connect = mysqli_connect($servername, $username, $password, $dbname);

if (!$connect) {
 
    die("Connection failed: " . mysqli_connect_error());
}



function parse_temperatures($key) {
    $raw = isset($_GET[$key]) ? trim($_GET[$key]) : str_repeat("990", 7); // np. "990990990990990990990"
    
    if (strlen($raw) !== 21 || !ctype_digit($raw)) {
        // Niepoprawna długość lub zawiera niecyfry
        return array_fill(0, 7, 99.0);
    }

    $temperatures = [];
    for ($i = 0; $i < 7; $i++) {
        $segment = substr($raw, $i * 3, 3); // 3 cyfry
        $int_part = substr($segment, 0, 2); // np. "23"
        $decimal_part = substr($segment, 2, 1); // np. "5"
        $temperatures[] = floatval($int_part . "." . $decimal_part); // np. "23.5"
    }

    return $temperatures;
}

// Pobieranie danych l1-l3 jako ciągi bitów
function get_light_array($key) {
    $val = isset($_GET[$key]) ? $_GET[$key] : str_repeat("0", 14);
    $arr = array_map('intval', str_split((string)$val));
    return count($arr) === 14 ? $arr : array_fill(0, 14, 0);
}
$t_zewn = $_GET['t_zewn'] ?? 99.0;
$t1 = parse_temperatures("t1");
$t2 = parse_temperatures("t2");
$t3 = parse_temperatures("t3");

$l1 = get_light_array("l1");
$l2 = get_light_array("l2");
$l3 = get_light_array("l3");

// Wstawianie temperatur
$sql = "INSERT INTO temperatura (
    t3_1, t3_2, t3_3, t3_4, t3_5, t3_6, t3_7,
    t2_1, t2_2, t2_3, t2_4, t2_5, t2_6, t2_7,
    t1_1, t1_2, t1_3, t1_4, t1_5, t1_6, t1_7,
    t_zewn
) VALUES (
    $t3[0], $t3[1], $t3[2], $t3[3], $t3[4], $t3[5], $t3[6], 
    $t2[0], $t2[1], $t2[2], $t2[3], $t2[4], $t2[5], $t2[6], 
    $t1[0], $t1[1], $t1[2], $t1[3], $t1[4], $t1[5], $t1[6], 
    $t_zewn
);";

if (mysqli_query($connect, $sql)) {
    echo "Temperatura: dane wstawione poprawnie";
} else {
    echo "Błąd przy wstawianiu temperatury: " . mysqli_error($connect);
}

// Wstawianie świateł
$sql = "INSERT INTO light (
    l3_1_1, l3_1_2, l3_2_1, l3_2_2, l3_3_1, l3_3_2, l3_4_1, l3_4_2, l3_5_1, l3_5_2, l3_6_1, l3_6_2 , l3_7_1, l3_7_2, 
    l2_1_1, l2_1_2, l2_2_1, l2_2_2, l2_3_1, l2_3_2, l2_4_1, l2_4_2, l2_5_1, l2_5_2, l2_6_1, l2_6_2 , l2_7_1, l2_7_2, 
    l1_1_1, l1_1_2, l1_2_1, l1_2_2, l1_3_1, l1_3_2, l1_4_1, l1_4_2, l1_5_1, l1_5_2, l1_6_1, l1_6_2 , l1_7_1, l1_7_2
) VALUES (
    $l3[0], $l3[1], $l3[2], $l3[3], $l3[4], $l3[5], $l3[6], $l3[7], $l3[8], $l3[9], $l3[10], $l3[11], $l3[12], $l3[13],
    $l2[0], $l2[1], $l2[2], $l2[3], $l2[4], $l2[5], $l2[6], $l2[7], $l2[8], $l2[9], $l2[10], $l2[11], $l2[12], $l2[13],
    $l1[0], $l1[1], $l1[2], $l1[3], $l1[4], $l1[5], $l1[6], $l1[7], $l1[8], $l1[9], $l1[10], $l1[11], $l1[12], $l1[13]
);";

if (mysqli_query($connect, $sql)) {
    echo "Light: dane wstawione poprawnie";
} else {
    echo "Błąd przy wstawianiu świateł: " . mysqli_error($connect);
}

mysqli_close($connect);
?>