<?php
$conn = mysqli_connect("localhost", "root", "", "plc_database");

$sql = "SELECT 
t1_1, t1_2, t1_3, t1_4, t1_5, t1_6, t1_7, 
t2_1, t2_2, t2_3, t2_4, t2_5, t2_6, t2_7,
t3_1, t3_2, t3_3, t3_4, t3_5, t3_6, t3_7,
zewnetrzna, czas_dodania 
FROM temperatura 
WHERE czas_dodania > NOW() - INTERVAL 7 DAY 
ORDER BY czas_dodania DESC";

$czas = [];
$t1_1 = $t1_2 = $t1_3 = $t1_4 = $t1_5 = $t1_6 = $t1_7 = [];
$t2_1 = $t2_2 = $t2_3 = $t2_4 = $t2_5 = $t2_6 = $t2_7 = [];
$t3_1 = $t3_2 = $t3_3 = $t3_4 = $t3_5 = $t3_6 = $t3_7 = [];
$temp_zewn = [];

if (isset($_GET["data_od"]) && isset($_GET["data_do"])) {
    $data_od = $_GET["data_od"];
    $data_do = $_GET["data_do"];

    $sql = "SELECT 
    t1_1, t1_2, t1_3, t1_4, t1_5, t1_6, t1_7, 
    t2_1, t2_2, t2_3, t2_4, t2_5, t2_6, t2_7,
    t3_1, t3_2, t3_3, t3_4, t3_5, t3_6, t3_7,
    zewnetrzna, czas_dodania 
    FROM temperatura 
    WHERE czas_dodania BETWEEN '$data_od' AND '$data_do' 
    ORDER BY czas_dodania DESC";
}

$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $czas[] = $row['czas_dodania'];
    $t1_1[] = round($row['t1_1'],1);
    $t1_2[] = round($row['t1_2'],1);
    $t1_3[] = round($row['t1_3'],1);
    $t1_4[] = round($row['t1_4'],1);
    $t1_5[] = round($row['t1_5'],1);
    $t1_6[] = round($row['t1_6'],1);
    $t1_7[] = round($row['t1_7'],1);

    $t2_1[] = round($row['t2_1'],1);
    $t2_2[] = round($row['t2_2'],1);
    $t2_3[] = round($row['t2_3'],1);
    $t2_4[] = round($row['t2_4'],1);
    $t2_5[] = round($row['t2_5'],1);
    $t2_6[] = round($row['t2_6'],1);
    $t2_7[] = round($row['t2_7'],1);

    $t3_1[] = round($row['t3_1'],1);
    $t3_2[] = round($row['t3_2'],1);
    $t3_3[] = round($row['t3_3'],1);
    $t3_4[] = round($row['t3_4'],1);
    $t3_5[] = round($row['t3_5'],1);
    $t3_6[] = round($row['t3_6'],1);
    $t3_7[] = round($row['t3_7'],1);

    $temp_zewn[] = round($row['zewnetrzna'],1);
}

$response = [];

if (isset($_GET["pietro"])) {
    $pietro = $_GET["pietro"];

    if ($pietro == 1) {
        $response = [
            'Temp. 1_1' => $t1_1,
            'Temp. 1_2' => $t1_2,
            'Temp. 1_3' => $t1_3,
            'Temp. 1_4' => $t1_4,
            'Temp. 1_5' => $t1_5,
            'Temp. 1_6' => $t1_6,
            'Temp. 1_7' => $t1_7,
        ];
    } elseif ($pietro == 2) {
        $response = [
            'Temp. 2_1' => $t2_1,
            'Temp. 2_2' => $t2_2,
            'Temp. 2_3' => $t2_3,
            'Temp. 2_4' => $t2_4,
            'Temp. 2_5' => $t2_5,
            'Temp. 2_6' => $t2_6,
            'Temp. 2_7' => $t2_7,
        ];
    } elseif ($pietro == 3) {
        $response = [
            'Temp. 3_1' => $t3_1,
            'Temp. 3_2' => $t3_2,
            'Temp. 3_3' => $t3_3,
            'Temp. 3_4' => $t3_4,
            'Temp. 3_5' => $t3_5,
            'Temp. 3_6' => $t3_6,
            'Temp. 3_7' => $t3_7,
        ];
    }
}

$response['Temp. zewn'] = $temp_zewn;
$response['Data'] = $czas;

echo json_encode($response);
mysqli_close($conn);
?>