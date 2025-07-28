<?php

$t1 = $_GET['t3_1'];
$t2 = $_GET['t3_2'];
$t3 = $_GET['t3_3'];
$t4 = $_GET['t3_4'];
$t5 = $_GET['t3_5'];
$t6 = $_GET['t3_6'];
$t7 = $_GET['t3_7'];
$temp_zew = $_GET['t_zewn'];

$conn = mysqli_connect('localhost','root','','plc_database');
$query = "insert into temperatura(t1,t2,t3,t4,t5,t6,t7,zewnetrzna) values ($t1, $t2, $t3, $t4, $t5, $t6, $t7, $temp_zew) ";
mysqli_query($conn, $query);
?>
