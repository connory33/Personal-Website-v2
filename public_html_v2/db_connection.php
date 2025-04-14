<?php 
$servername = "connoryoung.com";
$username = "connor";
$password = "PatrickRoy33";
$dbname = "NHL API";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (mysqli_connect_error()) {
    die("Connection failed: " . mysqli_connect_error());
}

?>