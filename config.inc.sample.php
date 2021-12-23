<?php
    $servername = "mysql";
    $username = "root";
    $password = "secret";
    $dbname = "timesheet";


// Set Timzone
date_default_timezone_set('Australia/Brisbane');

global $now;
$sql = "SELECT id, CONVERT_TZ(NOW(),'SYSTEM','Australia/Brisbane') FROM employees WHERE 1";
if ($result=mysqli_query($conn,$sql)) {
  if (mysqli_num_rows($result) > 0) {
    $now = "CONVERT_TZ(NOW(),'SYSTEM','Australia/Brisbane')";
  } else {
    $now = "NOW()";
  }
}

?>