<?php
$host = "localhost";
$user = "root";
$pass = "root";
$db = "tbl";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
	echo "Seems like you have not configured the database. Failed To Connect to database:" . $conn->connect_error;
}
