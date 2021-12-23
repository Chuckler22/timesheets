<?php
    $servername = "mysql";
    $username = "root";
    $password = "secret";
    $dbname = "timesheet";


// Set Timzone
date_default_timezone_set('Australia/Brisbane');

global $now;
$sql = "SELECT id, CONVERT_TZ(NOW(),'SYSTEM','Australia/Brisbane') as now FROM employees WHERE 1";
if ($result=mysqli_query($conn,$sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row["now"] == NULL) {
            $now = "NOW()";
        } else {
            $now = "CONVERT_TZ(NOW(),'SYSTEM','Australia/Brisbane')";
        }
    }
}
?>