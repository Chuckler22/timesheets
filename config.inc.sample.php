<?php
    $servername = "mysql";
    $username = "root";
    $password = "secret";
    $dbname = "timesheet";

// Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) { 
  die("Connection failed: " . mysqli_connect_error());
}

// Set Timzone
date_default_timezone_set('Australia/Brisbane');

?>