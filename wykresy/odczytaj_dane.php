<?php
$conn = mysqli_connect("localhost","root","","plc_database");
$sql = "SELECT 
t1_1, t1_2, t1_3, t1_4, t1_5, t1_6, t1_7, 
t2_1, t2_2, t2_3, t2_4, t2_5, t2_6, t2_7,
t3_1, t3_2, t3_3, t3_4, t3_5, t3_6, t3_7,
 zewnetrzna, czas_dodania FROM temperatura  WHERE czas_dodania > now() - INTERVAL 7 day ORDER BY czas_dodania DESC "; 

$data = array();
//$data[] = ['Czas' , 'T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'Zewnetrzna'];
$czas = [];
$t1_1 = []; 
$t1_2 = [];
$t1_3 = [];
$t1_4 = [];
$t1_5 = [];
$t1_6 = [];
$t1_7 = [];

$t2_1 = []; 
$t2_2 = [];
$t2_3 = [];
$t2_4 = [];
$t2_5 = [];
$t2_6 = [];
$t2_7 = [];

$t3_1 = []; 
$t3_2 = [];
$t3_3 = [];
$t3_4 = [];
$t3_5 = [];
$t3_6 = [];
$t3_7 = [];
$temp_zewn = [];
if(isset($_GET["data_od"])) {
    $data_od = $_GET["data_od"];
}
if(isset($_GET["data_do"])) {
    $data_do = $_GET["data_do"];
}

if (isset($data_do) && isset($data_od)) {
    $sql = "SELECT 
    t1_1, t1_2, t1_3, t1_4, t1_5, t1_6, t1_7, 
    t2_1, t2_2, t2_3, t2_4, t2_5, t2_6, t2_7,
    t3_1, t3_2, t3_3, t3_4, t3_5, t3_6, t3_7,
    zewnetrzna, czas_dodania FROM temperatura WHERE czas_dodania BETWEEN '$data_od' AND '$data_do' ORDER BY czas_dodania DESC"; 
}

$result = mysqli_query($conn, $sql);
while ($row=mysqli_fetch_assoc($result)) {
$czas [] = $row['czas_dodania'];
$t1_1 [] = (float)$row['t1_1'];
$t1_2 [] = (float)$row['t1_2'];
$t1_3 [] = (float)$row['t1_3'];
$t1_4 [] = (float)$row['t1_4'];
$t1_5 [] = (float)$row['t1_5'];
$t1_6 [] = (float)$row['t1_6'];
$t1_7 [] = (float)$row['t1_7'];

$t2_1 [] = (float)$row['t2_1'];
$t2_2 [] = (float)$row['t2_2'];
$t2_3 [] = (float)$row['t2_3'];
$t2_4 [] = (float)$row['t2_4'];
$t2_5 [] = (float)$row['t2_5'];
$t2_6 [] = (float)$row['t2_6'];
$t2_7 [] = (float)$row['t2_7'];

$t3_1 [] = (float)$row['t3_1'];
$t3_2 [] = (float)$row['t3_2'];
$t3_3 [] = (float)$row['t3_3'];
$t3_4 [] = (float)$row['t3_4'];
$t3_5 [] = (float)$row['t3_5'];
$t3_6 [] = (float)$row['t3_6'];
$t3_7 [] = (float)$row['t3_7'];
$temp_zewn [] = $row['zewnetrzna'];
}
echo json_encode([
    'Temp. 1_1'=> $t1_1,
    'Temp. 1_2'=> $t1_2,
    'Temp. 1_3'=> $t1_3,
    'Temp. 1_4'=> $t1_4,
    'Temp. 1_5'=> $t1_5,
    'Temp. 1_6'=> $t1_6,
    'Temp. 1_7'=> $t1_7,

    'Temp. 2_1'=> $t2_1,
    'Temp. 2_2'=> $t2_2,
    'Temp. 2_3'=> $t2_3,
    'Temp. 2_4'=> $t2_4,
    'Temp. 2_5'=> $t2_5,
    'Temp. 2_6'=> $t2_6,
    'Temp. 2_7'=> $t2_7,

    'Temp. 3_1'=> $t3_1,
    'Temp. 3_2'=> $t3_2,
    'Temp. 3_3'=> $t3_3,
    'Temp. 3_4'=> $t3_4,
    'Temp. 3_5'=> $t3_5,
    'Temp. 3_6'=> $t3_6,
    'Temp. 3_7'=> $t3_7,
    'Temp. zewn'=> $temp_zewn,
    'Data'=> $czas,
]);
mysqli_close($conn); 

?>