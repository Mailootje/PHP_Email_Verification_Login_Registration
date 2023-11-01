<?php

$host = "";
$username = "";
$password = "";
$database = "";

$con = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}

?>