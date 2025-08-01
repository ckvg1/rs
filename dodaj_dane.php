<?php 
header('Content-type: text/html; charset=utf-8');

?>

<?php
	/*
http://localhost/rs/dodaj_dane.php?
t1_1=12.1&t1_2=-3.2&t1_3=5.5&t1_4=17.3&t1_5=30.0&t1_6=21.2&t1_7=3.3&
t2_1=4.1&t2_2=7.7&t2_3=-2.2&t2_4=9.9&t2_5=11.0&t2_6=0.0&t2_7=6.6&
t3_1=19.0&t3_2=2.2&t3_3=-5.5&t3_4=23.1&t3_5=1.1&t3_6=18.8&t3_7=7.7&
l1=01001010110011&
l2=11001100011101&
l3=00110110101010&t_zewn=10.5
*/
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "plc_database";

	// Establish a connection
	$connect = mysqli_connect($servername, $username, $password, $dbname);

	// Check the connection
	if (!$connect) {
		die("Connection failed: " . mysqli_connect_error());
	}

	// Perform database operations here
	
	
	$t1_1 = trim($_GET["t1_1"]);
	$t1_2 = trim($_GET["t1_2"]);
	$t1_3 = trim($_GET["t1_3"]);
	$t1_4 = trim($_GET["t1_4"]);
	$t1_5 = trim($_GET["t1_5"]);
	$t1_6 = trim($_GET["t1_6"]);
	$t1_7 = trim($_GET["t1_7"]);
	
	$t2_1 = trim($_GET["t2_1"]);
	$t2_2 = trim($_GET["t2_2"]);
	$t2_3 = trim($_GET["t2_3"]);
	$t2_4 = trim($_GET["t2_4"]);
	$t2_5 = trim($_GET["t2_5"]);
	$t2_6 = trim($_GET["t2_6"]);
	$t2_7 = trim($_GET["t2_7"]);
	
	
	$t3_1 = trim($_GET["t3_1"]);
	$t3_2 = trim($_GET["t3_2"]);
	$t3_3 = trim($_GET["t3_3"]);
	$t3_4 = trim($_GET["t3_4"]);
	$t3_5 = trim($_GET["t3_5"]);
	$t3_6 = trim($_GET["t3_6"]);
	$t3_7 = trim($_GET["t3_7"]);
	
	$t_zewn = trim($_GET["t_zewn"]);
	
	$l3 = array_map('intval', str_split((string)$_GET["l3"]));
	$l2 = array_map('intval', str_split((string)$_GET["l2"]));
	$l1 = array_map('intval', str_split((string)$_GET["l1"]));

// Sprawdzenie długości
if (count($l1) !== 14 || count($l2) !== 14 || count($l3) !== 14) {
    $l1 = array_fill(0, 14, 0);
    $l2 = array_fill(0, 14, 0);
    $l3 = array_fill(0, 14, 0);
}
	
	// SQL query to insert data into the table
$sql = "INSERT INTO temperatura (
    t3_1, t3_2, t3_3, t3_4, t3_5, t3_6, t3_7,
    t2_1, t2_2, t2_3, t2_4, t2_5, t2_6, t2_7,
    t1_1, t1_2, t1_3, t1_4, t1_5, t1_6, t1_7,
    t_zewn
) VALUES (
    $t3_1, $t3_2, $t3_3, $t3_4, $t3_5, $t3_6, $t3_7,
    $t2_1, $t2_2, $t2_3, $t2_4, $t2_5, $t2_6, $t2_7,
    $t1_1, $t1_2, $t1_3, $t1_4, $t1_5, $t1_6, $t1_7,
    $t_zewn
);";
	// Execute the query
	if (mysqli_query($connect, $sql)) {
		echo "\n Data inserted successfully \n";
	} else {
		echo "\n Error inserting data: " . mysqli_error($connect);
	}
	
	$sql = "INSERT INTO light 
	(
	l3_1_1, l3_1_2, l3_2_1, l3_2_2, l3_3_1, l3_3_2, l3_4_1, l3_4_2, l3_5_1, l3_5_2, l3_6_1, l3_6_2 , l3_7_1, l3_7_2, 
	l2_1_1, l2_1_2, l2_2_1, l2_2_2, l2_3_1, l2_3_2, l2_4_1, l2_4_2, l2_5_1, l2_5_2, l2_6_1, l2_6_2 , l2_7_1, l2_7_2, 
	l1_1_1, l1_1_2, l1_2_1, l1_2_2, l1_3_1, l1_3_2, l1_4_1, l1_4_2, l1_5_1, l1_5_2, l1_6_1, l1_6_2 , l1_7_1, l1_7_2) 
	VALUES (
	$l3[0], $l3[1], $l3[2], $l3[3], $l3[4], $l3[5], $l3[6], $l3[7], $l3[8], $l3[9], $l3[10], $l3[11], $l3[12], $l3[13], 
	$l2[0], $l2[1], $l2[2], $l2[3], $l2[4], $l2[5], $l2[6], $l2[7], $l2[8], $l2[9], $l2[10], $l2[11], $l2[12], $l2[13], 
	$l1[0], $l1[1], $l1[2], $l1[3], $l1[4], $l1[5], $l1[6], $l1[7], $l1[8], $l1[9], $l1[10], $l1[11], $l1[12], $l1[13] );";
	
	// Execute the query
	if (mysqli_query($connect, $sql)) {
		echo "\n Data inserted successfully \n";
	} else {
		echo "\n Error inserting data: " . mysqli_error($connect);
	}
	
	// Close the connection
	mysqli_close($connect);

?>
