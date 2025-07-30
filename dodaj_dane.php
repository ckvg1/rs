<?php

$t1_1 = $_GET['t1_1'];
$t1_2 = $_GET['t1_2'];
$t1_3 = $_GET['t1_3'];
$t1_4 = $_GET['t1_4'];
$t1_5 = $_GET['t1_5'];
$t1_6 = $_GET['t1_6'];
$t1_7 = $_GET['t1_7'];

$t2_1 = $_GET['t2_1'];
$t2_2 = $_GET['t2_2'];
$t2_3 = $_GET['t2_3'];
$t2_4 = $_GET['t2_4'];
$t2_5 = $_GET['t2_5'];
$t2_6 = $_GET['t2_6'];
$t2_7 = $_GET['t2_7'];

$t3_1 = $_GET['t3_1'];
$t3_2 = $_GET['t3_2'];
$t3_3 = $_GET['t3_3'];
$t3_4 = $_GET['t3_4'];
$t3_5 = $_GET['t3_5'];
$t3_6 = $_GET['t3_6'];
$t3_7 = $_GET['t3_7'];

$t_zewn = $_GET['t_zewn'];

$conn = mysqli_connect('localhost','root','','plc_database');

$query = "INSERT INTO temperatura (
    t1_1, t1_2, t1_3, t1_4, t1_5, t1_6, t1_7,
    t2_1, t2_2, t2_3, t2_4, t2_5, t2_6, t2_7,
    t3_1, t3_2, t3_3, t3_4, t3_5, t3_6, t3_7,
    zewnetrzna
) VALUES (
    $t1_1, $t1_2, $t1_3, $t1_4, $t1_5, $t1_6, $t1_7,
    $t2_1, $t2_2, $t2_3, $t2_4, $t2_5, $t2_6, $t2_7,
    $t3_1, $t3_2, $t3_3, $t3_4, $t3_5, $t3_6, $t3_7,
    $t_zewn
)";

mysqli_query($conn, $query);
?>
