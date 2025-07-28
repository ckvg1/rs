<?php
$conn = mysqli_connect("localhost","root","","plc_database");
$sql = "SELECT t1, t2, t3, t4, t5, t6, t7, zewnetrzna, czas_dodania FROM temperatura  WHERE czas_dodania > now() - INTERVAL 7 day ORDER BY czas_dodania DESC "; 

$data = array();
//$data[] = ['Czas' , 'T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'Zewnetrzna'];
$czas = [];
$t1 = []; 
$t2 = [];
$t3 = [];
$t4 = [];
$t5 = [];
$t6 = [];
$t7 = [];
$temp_zewn = [];
if(isset($_GET["data_od"])) {
    $data_od = $_GET["data_od"];
}
if(isset($_GET["data_do"])) {
    $data_do = $_GET["data_do"];
}

if (isset($data_do) && isset($data_od)) {
    $sql = "SELECT t1, t2, t3, t4, t5, t6, t7, zewnetrzna, czas_dodania FROM temperatura WHERE czas_dodania BETWEEN '$data_od' AND '$data_do' ORDER BY czas_dodania DESC"; 
}

$result = mysqli_query($conn, $sql);
while ($row=mysqli_fetch_assoc($result)) {
$czas [] = $row['czas_dodania'];
$t1 [] = (float)$row['t1'];
$t2 [] = (float)$row['t2'];
$t3 [] = (float)$row['t3'];
$t4 [] = (float)$row['t4'];
$t5 [] = (float)$row['t5'];
$t6 [] = (float)$row['t6'];
$t7 [] = (float)$row['t7'];
$temp_zewn [] = $row['zewnetrzna'];
}
echo json_encode([
    'Temp. 1'=> $t1,
    'Temp. 2'=> $t2,
    'Temp. 3'=> $t3,
    'Temp. 4'=> $t4,
    'Temp. 5'=> $t5,
    'Temp. 6'=> $t6,
    'Temp. 7'=> $t7,
    'Temp. zewn'=> $temp_zewn,
    'Data'=> $czas,
]);
mysqli_close($conn); 

?>