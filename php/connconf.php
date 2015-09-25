<?php
$servername = "localhost";
$username = "root";
$password = "c3rmat";
$dbname = "sms";

// Create connection
$conn = mysql_connect($servername, $username, $password, $dbname);
$select = mysql_select_db($dbname, $conn);
// Check connection
if (!$select) {
    die("Connection failed: " . mysql_connect_error());
}
?>