<?php
   session_start();
   if(!isset($_SESSION["username"])) {
    header("location:./login.php");
  }
  $loggedinuser = $_SESSION["username"];

  require("include.php");

  // If we're loading this form, it's expected that the loggedinuser is editing their own timesheet
  // based on the contents of $_GET["fne"]
  // error handling
  if (!isset($_GET["fne"])) {
    header('Refresh: 1; URL = /index.php');
    exit("You must select a fortnight!");
  }
  if (!preg_match('/\d{4}-\d{2}-\d{2}/',$_GET["fne"])) {
    exit("Invalid timesheet format.");
  } else {
    $fne = $_GET["fne"];
    $sql = "SELECT employees.id as employees_id, employees.novellname, timesheets.id as timesheets_id, timesheets.employee, timesheets.fne_date, timesheets.submitted 
            FROM `employees`
            LEFT JOIN `timesheets` ON employees.id = timesheets.employee 
            WHERE employees.novellname = ? AND timesheets.fne_date = ?";
    $userStatement = mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($userStatement, 'ss', $loggedinuser, $fne);
    mysqli_stmt_execute($userStatement);
    $result = mysqli_stmt_get_result($userStatement);
    if (mysqli_num_rows($result) == 1) {
      $getData = mysqli_fetch_assoc($result);
      $timesheet = $getData["timesheets_id"];
      if (!$getData["submitted"] == NULL) {
        header("Location: /timesheet-view.php?fne=".$fne);
        exit("Header redirect no worky");
      }
    } else {
      exit("Unable to find timesheet.");
    }
  }



  if($_SERVER['REQUEST_METHOD'] == "POST") { 
    if(!empty($_POST["home"])) {
      header("Location: /");
      exit("Header redirect no worky");
    }
    if(!empty($_POST["logout"])) {
      header("Location: /logout.php");
      exit("Logout Redirect");
    }

   
    $vars = array("day1start","day2start","day3start","day4start","day5start","day6start","day7start","day8start","day9start","day10start","day11start","day12start","day13start","day14start");
      array_push($vars,"day1stop","day2stop","day3stop","day4stop","day5stop","day6stop","day7stop","day8stop","day9stop","day10stop","day11stop","day12stop","day13stop","day14stop");
      array_push($vars,"day1break1start","day2break1start","day3break1start","day4break1start","day5break1start","day6break1start","day7break1start","day8break1start","day9break1start","day10break1start","day11break1start","day12break1start","day13break1start","day14break1start");
      array_push($vars,"day1break1stop","day2break1stop","day3break1stop","day4break1stop","day5break1stop","day6break1stop","day7break1stop","day8break1stop","day9break1stop","day10break1stop","day11break1stop","day12break1stop","day13break1stop","day14break1stop");
      array_push($vars,"day1break2start","day2break2start","day3break2start","day4break2start","day5break2start","day6break2start","day7break2start","day8break2start","day9break2start","day10break2start","day11break2start","day12break2start","day13break2start","day14break2start");
      array_push($vars,"day1break2stop","day2break2stop","day3break2stop","day4break2stop","day5break2stop","day6break2stop","day7break2stop","day8break2stop","day9break2stop","day10break2stop","day11break2stop","day12break2stop","day13break2stop","day14break2stop");
      array_push($vars,"day1break3start","day2break3start","day3break3start","day4break3start","day5break3start","day6break3start","day7break3start","day8break3start","day9break3start","day10break3start","day11break3start","day12break3start","day13break3start","day14break3start");
      array_push($vars,"day1break3stop","day2break3stop","day3break3stop","day4break3stop","day5break3stop","day6break3stop","day7break3stop","day8break3stop","day9break3stop","day10break3stop","day11break3stop","day12break3stop","day13break3stop","day14break3stop");
      array_push($vars,"day1lvannual","day2lvannual","day3lvannual","day4lvannual","day5lvannual","day6lvannual","day7lvannual","day8lvannual","day9lvannual","day10lvannual","day11lvannual","day12lvannual","day13lvannual","day14lvannual");
      array_push($vars,"day1lvsick","day2lvsick","day3lvsick","day4lvsick","day5lvsick","day6lvsick","day7lvsick","day8lvsick","day9lvsick","day10lvsick","day11lvsick","day12lvsick","day13lvsick","day14lvsick");
      array_push($vars,"day1lvtoil","day2lvtoil","day3lvtoil","day4lvtoil","day5lvtoil","day6lvtoil","day7lvtoil","day8lvtoil","day9lvtoil","day10lvtoil","day11lvtoil","day12lvtoil","day13lvtoil","day14lvtoil");
      array_push($vars,"day1lvphcon","day2lvphcon","day3lvphcon","day4lvphcon","day5lvphcon","day6lvphcon","day7lvphcon","day8lvphcon","day9lvphcon","day10lvphcon","day11lvphcon","day12lvphcon","day13lvphcon","day14lvphcon");
      array_push($vars,"day1lvflex","day2lvflex","day3lvflex","day4lvflex","day5lvflex","day6lvflex","day7lvflex","day8lvflex","day9lvflex","day10lvflex","day11lvflex","day12lvflex","day13lvflex","day14lvflex");
      array_push($vars,"day1oc1start","day2oc1start","day3oc1start","day4oc1start","day5oc1start","day6oc1start","day7oc1start","day8oc1start","day9oc1start","day10oc1start","day11oc1start","day12oc1start","day13oc1start","day14oc1start");
      array_push($vars,"day1oc1stop","day2oc1stop","day3oc1stop","day4oc1stop","day5oc1stop","day6oc1stop","day7oc1stop","day8oc1stop","day9oc1stop","day10oc1stop","day11oc1stop","day12oc1stop","day13oc1stop","day14oc1stop");
      array_push($vars,"day1oc2start","day2oc2start","day3oc2start","day4oc2start","day5oc2start","day6oc2start","day7oc2start","day8oc2start","day9oc2start","day10oc2start","day11oc2start","day12oc2start","day13oc2start","day14oc2start");
      array_push($vars,"day1oc2stop","day2oc2stop","day3oc2stop","day4oc2stop","day5oc2stop","day6oc2stop","day7oc2stop","day8oc2stop","day9oc2stop","day10oc2stop","day11oc2stop","day12oc2stop","day13oc2stop","day14oc2stop");
      array_push($vars,"day1oc3start","day2oc3start","day3oc3start","day4oc3start","day5oc3start","day6oc3start","day7oc3start","day8oc3start","day9oc3start","day10oc3start","day11oc3start","day12oc3start","day13oc3start","day14oc3start");
      array_push($vars,"day1oc3stop","day2oc3stop","day3oc3stop","day4oc3stop","day5oc3stop","day6oc3stop","day7oc3stop","day8oc3stop","day9oc3stop","day10oc3stop","day11oc3stop","day12oc3stop","day13oc3stop","day14oc3stop");
      array_push($vars,"day1ot1start","day2ot1start","day3ot1start","day4ot1start","day5ot1start","day6ot1start","day7ot1start","day8ot1start","day9ot1start","day10ot1start","day11ot1start","day12ot1start","day13ot1start","day14ot1start");
      array_push($vars,"day1ot1stop","day2ot1stop","day3ot1stop","day4ot1stop","day5ot1stop","day6ot1stop","day7ot1stop","day8ot1stop","day9ot1stop","day10ot1stop","day11ot1stop","day12ot1stop","day13ot1stop","day14ot1stop");
      array_push($vars,"day1ot2start","day2ot2start","day3ot2start","day4ot2start","day5ot2start","day6ot2start","day7ot2start","day8ot2start","day9ot2start","day10ot2start","day11ot2start","day12ot2start","day13ot2start","day14ot2start");
      array_push($vars,"day1ot2stop","day2ot2stop","day3ot2stop","day4ot2stop","day5ot2stop","day6ot2stop","day7ot2stop","day8ot2stop","day9ot2stop","day10ot2stop","day11ot2stop","day12ot2stop","day13ot2stop","day14ot2stop");
      array_push($vars,"day1ot3start","day2ot3start","day3ot3start","day4ot3start","day5ot3start","day6ot3start","day7ot3start","day8ot3start","day9ot3start","day10ot3start","day11ot3start","day12ot3start","day13ot3start","day14ot3start");
      array_push($vars,"day1ot3stop","day2ot3stop","day3ot3stop","day4ot3stop","day5ot3stop","day6ot3stop","day7ot3stop","day8ot3stop","day9ot3stop","day10ot3stop","day11ot3stop","day12ot3stop","day13ot3stop","day14ot3stop");
      array_push($vars,"day1toil1start","day2toil1start","day3toil1start","day4toil1start","day5toil1start","day6toil1start","day7toil1start","day8toil1start","day9toil1start","day10toil1start","day11toil1start","day12toil1start","day13toil1start","day14toil1start");
      array_push($vars,"day1toil1stop","day2toil1stop","day3toil1stop","day4toil1stop","day5toil1stop","day6toil1stop","day7toil1stop","day8toil1stop","day9toil1stop","day10toil1stop","day11toil1stop","day12toil1stop","day13toil1stop","day14toil1stop");
      array_push($vars,"day1toil2start","day2toil2start","day3toil2start","day4toil2start","day5toil2start","day6toil2start","day7toil2start","day8toil2start","day9toil2start","day10toil2start","day11toil2start","day12toil2start","day13toil2start","day14toil2start");
      array_push($vars,"day1toil2stop","day2toil2stop","day3toil2stop","day4toil2stop","day5toil2stop","day6toil2stop","day7toil2stop","day8toil2stop","day9toil2stop","day10toil2stop","day11toil2stop","day12toil2stop","day13toil2stop","day14toil2stop");
      array_push($vars,"day1toil3start","day2toil3start","day3toil3start","day4toil3start","day5toil3start","day6toil3start","day7toil3start","day8toil3start","day9toil3start","day10toil3start","day11toil3start","day12toil3start","day13toil3start","day14toil3start");
      array_push($vars,"day1toil3stop","day2toil3stop","day3toil3stop","day4toil3stop","day5toil3stop","day6toil3stop","day7toil3stop","day8toil3stop","day9toil3stop","day10toil3stop","day11toil3stop","day12toil3stop","day13toil3stop","day14toil3stop");      
    foreach ($vars as $var) {
      if (preg_match('/\d{2}:\d{2}/',$_POST[$var])) {
        ${$var} = $_POST[$var] . ":00";
      } else { 
        ${$var} = NULL;
      }
    }

    $vars = array("day1oc","day2oc","day3oc","day4oc","day5oc","day6oc","day7oc","day8oc","day9oc","day10oc","day11oc","day12oc","day13oc","day14oc");
    foreach ($vars as $var) {
      if ($_POST[$var] == "On") {
        ${$var} = 1;
      } elseif ($_POST[$var] == "24") {
        ${$var} = 2;      
      } else {
        ${$var} = NULL;
      }
    } 
  
    $sql = "UPDATE `days` SET `daystart`= ?, `daystop` = ?, `break1start` = ?, `break1stop` = ?, `break2start` = ?, `break2stop` = ?, `break3start` = ?, `break3stop` = ?, `oncall` = ?, `lvannual` = ?, `lvsick` = ?, `lvtoil` = ?, `lvphcon` = ?, `lvflex` = ?, `oc1start` = ?, `oc1stop` = ?, `oc2start` = ?, `oc2stop` = ?, `oc3start` = ?, `oc3stop` = ?, `ot1start` = ?, `ot1stop` = ?, `ot2start` = ?, `ot2stop` = ?, `ot3start` = ?, `ot3stop` = ?, `toil1start` = ?, `toil1stop` = ?, `toil2start` = ?, `toil2stop` = ?, `toil3start` = ?, `toil3stop` = ? WHERE `dof` = ? AND `timesheet_id` = ?";
    $updateStatement = mysqli_prepare($conn, $sql);
    for ($x = 1; $x <= 14; $x++) {
      mysqli_stmt_bind_param($updateStatement,"ssssssssisssssssssssssssssssssssii",${"day".$x."start"},${"day".$x."stop"},${"day".$x."break1start"},${"day".$x."break1stop"},${"day".$x."break2start"},${"day".$x."break2stop"},${"day".$x."break3start"},${"day".$x."break3stop"},${"day".$x."oc"},${"day".$x."lvannual"},${"day".$x."lvsick"},${"day".$x."lvtoil"},${"day".$x."lvphcon"},${"day".$x."lvflex"},${"day".$x."oc1start"},${"day".$x."oc1stop"},${"day".$x."oc2start"},${"day".$x."oc2stop"},${"day".$x."oc3start"},${"day".$x."oc3stop"},${"day".$x."ot1start"},${"day".$x."ot1stop"},${"day".$x."ot2start"},${"day".$x."ot2stop"},${"day".$x."ot3start"},${"day".$x."ot3stop"},${"day".$x."toil1start"},${"day".$x."toil1stop"},${"day".$x."toil2start"},${"day".$x."toil2stop"},${"day".$x."toil3start"},${"day".$x."toil3stop"},$x,$timesheet);
      mysqli_stmt_execute($updateStatement);
    }

    if(!empty($_POST["submit"])) {
      $sql = "UPDATE `timesheets` SET `submitted` = CONVERT_TZ(NOW(),'SYSTEM','Australia/Brisbane') WHERE `fne_date` = ? and `id` = ?";
      $updateStatement = mysqli_prepare($conn, $sql);
      mysqli_stmt_bind_param($updateStatement,"si",$fne,$timesheet);
      mysqli_stmt_execute($updateStatement);
      header("Location: /timesheet-view.php?fne=".$fne);
      exit("Header redirect no worky");
    }
  } // endif method = POST


  $sql = "SELECT employees.id as employees_id, employees.first, employees.last, employees.email, employees.flexcf, employees.toilcf, employees.novellname,
    timesheets.id as timesheets_id, timesheets.employee, timesheets.fne_date, 
  days.timesheet_id, days.dof, .days.datex as datex, days.daystart, days.daystop, days.break1start, days.break1stop, days.break2start, days.break2stop, days.break3start, days.break3stop, days.oncall, days.lvannual, days.lvsick, days.lvtoil, days.lvphcon, days.lvflex, days.oc1start, days.oc1stop, days.oc2start, days.oc2stop, days.oc3start, days.oc3stop, days.ot1start, days.ot1stop, days.ot2start, days.ot2stop, days.ot3start, days.ot3stop, days.toil1start, days.toil1stop, days.toil2start, days.toil2stop, days.toil3start, days.toil3stop
  FROM `employees`
  LEFT JOIN `timesheets` ON employees.id = timesheets.employee 
  LEFT JOIN `days` ON timesheets.id = days.timesheet_id
  WHERE employees.novellname = '".$loggedinuser."' AND timesheets.fne_date = '".$fne."'";
  $result = mysqli_query($conn, $sql);
  while($row = mysqli_fetch_assoc($result)) {
      $first = $row["first"];
      $last = $row["last"];
      $email = $row["email"];
      $flexcf = $row["flexcf"] == "" ? "00:00" : substr($row["flexcf"],0,-3);
      $toilcf = $row["toilcf"] == "" ? "00:00" : substr($row["toilcf"],0,-3);
      $novellname = $row["novellname"];
      ${"day" . $row["dof"] . "start"} = substr($row["daystart"],0,-3);
      ${"day" . $row["dof"] . "stop"} = substr($row["daystop"],0,-3);
      ${"day" . $row["dof"] . "break1start"} = substr($row["break1start"],0,-3);
      ${"day" . $row["dof"] . "break1stop"} = substr($row["break1stop"],0,-3);
      ${"day" . $row["dof"] . "break2start"} = substr($row["break2start"],0,-3);
      ${"day" . $row["dof"] . "break2stop"} = substr($row["break2stop"],0,-3);
      ${"day" . $row["dof"] . "break3start"} = substr($row["break3start"],0,-3);
      ${"day" . $row["dof"] . "break3stop"} = substr($row["break3stop"],0,-3);
      ${"day" . $row["dof"] . "oncall"} = $row["oncall"];
      ${"day" . $row["dof"] . "lvannual"} = substr($row["lvannual"],0,-3);
      ${"day" . $row["dof"] . "lvsick"} = substr($row["lvsick"],0,-3);
      ${"day" . $row["dof"] . "lvtoil"} = substr($row["lvtoil"],0,-3);
      ${"day" . $row["dof"] . "lvphcon"} = substr($row["lvphcon"],0,-3);
      ${"day" . $row["dof"] . "lvflex"} = substr($row["lvflex"],0,-3);
      ${"day" . $row["dof"] . "oc1start"} = substr($row["oc1start"],0,-3);
      ${"day" . $row["dof"] . "oc1stop"} = substr($row["oc1stop"],0,-3);
      ${"day" . $row["dof"] . "oc2start"} = substr($row["oc2start"],0,-3);
      ${"day" . $row["dof"] . "oc2stop"} = substr($row["oc2stop"],0,-3);
      ${"day" . $row["dof"] . "oc3start"} = substr($row["oc3start"],0,-3);
      ${"day" . $row["dof"] . "oc3stop"} = substr($row["oc3stop"],0,-3);
      ${"day" . $row["dof"] . "ot1start"} = substr($row["ot1start"],0,-3);
      ${"day" . $row["dof"] . "ot1stop"} = substr($row["ot1stop"],0,-3);
      ${"day" . $row["dof"] . "ot2start"} = substr($row["ot2start"],0,-3);
      ${"day" . $row["dof"] . "ot2stop"} = substr($row["ot2stop"],0,-3);
      ${"day" . $row["dof"] . "ot3start"} = substr($row["ot3start"],0,-3);
      ${"day" . $row["dof"] . "ot3stop"} = substr($row["ot3stop"],0,-3);
      ${"day" . $row["dof"] . "toil1start"} = substr($row["toil1start"],0,-3);
      ${"day" . $row["dof"] . "toil1stop"} = substr($row["toil1stop"],0,-3);
      ${"day" . $row["dof"] . "toil2start"} = substr($row["toil2start"],0,-3);
      ${"day" . $row["dof"] . "toil2stop"} = substr($row["toil2stop"],0,-3);
      ${"day" . $row["dof"] . "toil3start"} = substr($row["toil3start"],0,-3);
      ${"day" . $row["dof"] . "toil3stop"} = substr($row["toil3stop"],0,-3);
  }
?>
<form action="<?=$_SERVER['PHP_SELF']; ?>?<?=$_SERVER['argv'][0]; ?>" method="post">
<input type="submit" value="Home" name="home">
<input type="submit" value="save it" name="save">
<input type="submit" value="submit it" name="submit">
<input type="submit" value="logout" name="logout">
<script src="./cleave.min.js"></script>
<h1>
    <?=$first; ?>
    <?=$last; ?>
</h1>
    <h2><?=$email; ?></h2>


<style type="text/css">
  .tg  {border-collapse:collapse;border-spacing:0;}
  .tg td{border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;overflow:hidden;padding:10px 5px;word-break:normal;}
  .tg th{border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;font-weight:normal;overflow:hidden;padding:10px 5px;word-break:normal;}
  .tg .tg-0x09{background-color:#9b9b9b;text-align:left;vertical-align:top}
  .tg .tg-266k{background-color:#9b9b9b;border-color:inherit;text-align:left;vertical-align:top}
  .tg .tg-7od5{background-color:#9aff99;border-color:inherit;text-align:left;vertical-align:top}
  .tg .tg-0pky{border-color:inherit;text-align:left;vertical-align:top}
  .tg .tg-ncd7{background-color:#ffffc7;border-color:inherit;text-align:left;vertical-align:top}
  .tg .tg-0lax{text-align:left;vertical-align:top}
  .tg .tg-c3ow{border-color:inherit;text-align:center;vertical-align:top}
  .tg .tg-c66k{background-color:#9b9b9b;border-color:inherit;text-align:center;vertical-align:top}
  .input-time {width: 5em;}
</style>
<table class="tg">
<thead>
  <tr>
    <th class="tg-0lax">Flex C/F: <?=$flexcf; ?><br>TOIL C/F: <?=$toilcf; ?></th>
    <th class="tg-c3ow"><?=date('D<\b\r>d-m', strtotime($fne . ' -13 day')); ?><br><input type="button" value="[X]" style="width: 40px;" onclick="clearDay(1);"></th>
    <th class="tg-c3ow"><?=date('D<\b\r>d-m', strtotime($fne . ' -12 day')); ?><br><input type="button" value="[X]" style="width: 40px;" onclick="clearDay(2);"></th>
    <th class="tg-c3ow"><?=date('D<\b\r>d-m', strtotime($fne . ' -11 day')); ?><br><input type="button" value="[X]" style="width: 40px;" onclick="clearDay(3);"></th>
    <th class="tg-c3ow"><?=date('D<\b\r>d-m', strtotime($fne . ' -10 day')); ?><br><input type="button" value="[X]" style="width: 40px;" onclick="clearDay(4);"></th>
    <th class="tg-c3ow"><?=date('D<\b\r>d-m', strtotime($fne . ' -9 day')); ?><br><input type="button" value="[X]" style="width: 40px;" onclick="clearDay(5);"></th>
    <th class="tg-c66k"><?=date('D<\b\r>d-m', strtotime($fne . ' -8 day')); ?><br><input type="button" value="[X]" style="width: 40px;" onclick="clearDay(6);"></th>
    <th class="tg-c66k"><?=date('D<\b\r>d-m', strtotime($fne . ' -7 day')); ?><br><input type="button" value="[X]" style="width: 40px;" onclick="clearDay(7);"></th>
    <th class="tg-c3ow"><?=date('D<\b\r>d-m', strtotime($fne . ' -6 day')); ?><br><input type="button" value="[X]" style="width: 40px;" onclick="clearDay(8);"></th>
    <th class="tg-c3ow"><?=date('D<\b\r>d-m', strtotime($fne . ' -5 day')); ?><br><input type="button" value="[X]" style="width: 40px;" onclick="clearDay(9);"></th>
    <th class="tg-c3ow"><?=date('D<\b\r>d-m', strtotime($fne . ' -4 day')); ?><br><input type="button" value="[X]" style="width: 40px;" onclick="clearDay(10);"></th>
    <th class="tg-c3ow"><?=date('D<\b\r>d-m', strtotime($fne . ' -3 day')); ?><br><input type="button" value="[X]" style="width: 40px;" onclick="clearDay(11);"></th>
    <th class="tg-c3ow"><?=date('D<\b\r>d-m', strtotime($fne . ' -2 day')); ?><br><input type="button" value="[X]" style="width: 40px;" onclick="clearDay(12);"></th>
    <th class="tg-c66k"><?=date('D<\b\r>d-m', strtotime($fne . ' -1 day')); ?><br><input type="button" value="[X]" style="width: 40px;" onclick="clearDay(13);"></th>
    <th class="tg-c66k"><?=date('D<\b\r>d-m', strtotime($fne)); ?><br><input type="button" value="[X]" style="width: 40px;" onclick="clearDay(14);"></th>
  </tr>
</thead>
<tbody>
<?php

  function writeRow2($bg,$linename,$shortdesc,$a,$b) {
    $x = 0;
    global $day1lvannual,$day2lvannual,$day3lvannual,$day4lvannual,$day5lvannual,$day6lvannual,$day7lvannual,$day8lvannual,$day9lvannual,$day10lvannual,$day11lvannual,$day12lvannual,$day13lvannual,$day14lvannual;
    global $day1lvsick,$day2lvsick,$day3lvsick,$day4lvsick,$day5lvsick,$day6lvsick,$day7lvsick,$day8lvsick,$day9lvsick,$day10lvsick,$day11lvsick,$day12lvsick,$day13lvsick,$day14lvsick;
    global $day1lvtoil,$day2lvtoil,$day3lvtoil,$day4lvtoil,$day5lvtoil,$day6lvtoil,$day7lvtoil,$day8lvtoil,$day9lvtoil,$day10lvtoil,$day11lvtoil,$day12lvtoil,$day13lvtoil,$day14lvtoil;
    global $day1lvphcon,$day2lvphcon,$day3lvphcon,$day4lvphcon,$day5lvphcon,$day6lvphcon,$day7lvphcon,$day8lvphcon,$day9lvphcon,$day10lvphcon,$day11lvphcon,$day12lvphcon,$day13lvphcon,$day14lvphcon;
    global $day1lvflex,$day2lvflex,$day3lvflex,$day4lvflex,$day5lvflex,$day6lvflex,$day7lvflex,$day8lvflex,$day9lvflex,$day10lvflex,$day11lvflex,$day12lvflex,$day13lvflex,$day14lvflex;
    global $day1oc1start,$day2oc1start,$day3oc1start,$day4oc1start,$day5oc1start,$day6oc1start,$day7oc1start,$day8oc1start,$day9oc1start,$day10oc1start,$day11oc1start,$day12oc1start,$day13oc1start,$day14oc1start;
    global $day1oc1stop,$day2oc1stop,$day3oc1stop,$day4oc1stop,$day5oc1stop,$day6oc1stop,$day7oc1stop,$day8oc1stop,$day9oc1stop,$day10oc1stop,$day11oc1stop,$day12oc1stop,$day13oc1stop,$day14oc1stop;
    global $day1oc2start,$day2oc2start,$day3oc2start,$day4oc2start,$day5oc2start,$day6oc2start,$day7oc2start,$day8oc2start,$day9oc2start,$day10oc2start,$day11oc2start,$day12oc2start,$day13oc2start,$day14oc2start;
    global $day1oc2stop,$day2oc2stop,$day3oc2stop,$day4oc2stop,$day5oc2stop,$day6oc2stop,$day7oc2stop,$day8oc2stop,$day9oc2stop,$day10oc2stop,$day11oc2stop,$day12oc2stop,$day13oc2stop,$day14oc2stop;
    global $day1oc3start,$day2oc3start,$day3oc3start,$day4oc3start,$day5oc3start,$day6oc3start,$day7oc3start,$day8oc3start,$day9oc3start,$day10oc3start,$day11oc3start,$day12oc3start,$day13oc3start,$day14oc3start;
    global $day1oc3stop,$day2oc3stop,$day3oc3stop,$day4oc3stop,$day5oc3stop,$day6oc3stop,$day7oc3stop,$day8oc3stop,$day9oc3stop,$day10oc3stop,$day11oc3stop,$day12oc3stop,$day13oc3stop,$day14oc3stop;
    global $day1ot1start,$day2ot1start,$day3ot1start,$day4ot1start,$day5ot1start,$day6ot1start,$day7ot1start,$day8ot1start,$day9ot1start,$day10ot1start,$day11ot1start,$day12ot1start,$day13ot1start,$day14ot1start;
    global $day1ot1stop,$day2ot1stop,$day3ot1stop,$day4ot1stop,$day5ot1stop,$day6ot1stop,$day7ot1stop,$day8ot1stop,$day9ot1stop,$day10ot1stop,$day11ot1stop,$day12ot1stop,$day13ot1stop,$day14ot1stop;
    global $day1ot2start,$day2ot2start,$day3ot2start,$day4ot2start,$day5ot2start,$day6ot2start,$day7ot2start,$day8ot2start,$day9ot2start,$day10ot2start,$day11ot2start,$day12ot2start,$day13ot2start,$day14ot2start;
    global $day1ot2stop,$day2ot2stop,$day3ot2stop,$day4ot2stop,$day5ot2stop,$day6ot2stop,$day7ot2stop,$day8ot2stop,$day9ot2stop,$day10ot2stop,$day11ot2stop,$day12ot2stop,$day13ot2stop,$day14ot2stop;
    global $day1ot3start,$day2ot3start,$day3ot3start,$day4ot3start,$day5ot3start,$day6ot3start,$day7ot3start,$day8ot3start,$day9ot3start,$day10ot3start,$day11ot3start,$day12ot3start,$day13ot3start,$day14ot3start;
    global $day1ot3stop,$day2ot3stop,$day3ot3stop,$day4ot3stop,$day5ot3stop,$day6ot3stop,$day7ot3stop,$day8ot3stop,$day9ot3stop,$day10ot3stop,$day11ot3stop,$day12ot3stop,$day13ot3stop,$day14ot3stop;
    global $day1toil1start,$day2toil1start,$day3toil1start,$day4toil1start,$day5toil1start,$day6toil1start,$day7toil1start,$day8toil1start,$day9toil1start,$day10toil1start,$day11toil1start,$day12toil1start,$day13toil1start,$day14toil1start;
    global $day1toil1stop,$day2toil1stop,$day3toil1stop,$day4toil1stop,$day5toil1stop,$day6toil1stop,$day7toil1stop,$day8toil1stop,$day9toil1stop,$day10toil1stop,$day11toil1stop,$day12toil1stop,$day13toil1stop,$day14toil1stop;
    global $day1toil2start,$day2toil2start,$day3toil2start,$day4toil2start,$day5toil2start,$day6toil2start,$day7toil2start,$day8toil2start,$day9toil2start,$day10toil2start,$day11toil2start,$day12toil2start,$day13toil2start,$day14toil2start;
    global $day1toil2stop,$day2toil2stop,$day3toil2stop,$day4toil2stop,$day5toil2stop,$day6toil2stop,$day7toil2stop,$day8toil2stop,$day9toil2stop,$day10toil2stop,$day11toil2stop,$day12toil2stop,$day13toil2stop,$day14toil2stop;
    global $day1toil3start,$day2toil3start,$day3toil3start,$day4toil3start,$day5toil3start,$day6toil3start,$day7toil3start,$day8toil3start,$day9toil3start,$day10toil3start,$day11toil3start,$day12toil3start,$day13toil3start,$day14toil3start;
    global $day1toil3stop,$day2toil3stop,$day3toil3stop,$day4toil3stop,$day5toil3stop,$day6toil3stop,$day7toil3stop,$day8toil3stop,$day9toil3stop,$day10toil3stop,$day11toil3stop,$day12toil3stop,$day13toil3stop,$day14toil3stop;
    for ($x = 1; $x <= 14; $x++) {
      if (${"day".$x.$shortdesc} != NULL) {
        $style = "show";
      }
    }
    if ($a == 1 && isset($style)) {
      echo "  <tr id=\"".$shortdesc."\">\n";
      echo "    <td class=\"tg-0pky\">$linename</td>\n";
    } elseif ($a == 1 && !isset($style)) {
      echo "  <tr id=\"".$shortdesc."\" style=\"display: none;\">\n";
      echo "    <td class=\"tg-0pky\">$linename</td>\n";
    }
    for ($x = $a; $x <= $b; $x++) {
      echo "    <td class=\"$bg\"><input type=\"text\" class=\"input-time\" id=\"day".$x.$shortdesc."\" name=\"day".$x.$shortdesc."\" value=\"".${"day".$x.$shortdesc}."\" onchange=\"myInit()\"></td>\n";
    }
    if ($b == 14) {
      echo "  </tr>\n";
    }
  }

  function writeRow3($bg,$linename,$shortdesc,$a,$b) {
    $x = 0;
    global $day1start,$day2start,$day3start,$day4start,$day5start,$day6start,$day7start,$day8start,$day9start,$day10start,$day11start,$day12start,$day13start,$day14start;
    global $day1stop,$day2stop,$day3stop,$day4stop,$day5stop,$day6stop,$day7stop,$day8stop,$day9stop,$day10stop,$day11stop,$day12stop,$day13stop,$day14stop;
    global $day1break1start,$day2break1start,$day3break1start,$day4break1start,$day5break1start,$day6break1start,$day7break1start,$day8break1start,$day9break1start,$day10break1start,$day11break1start,$day12break1start,$day13break1start,$day14break1start;
    global $day1break1stop,$day2break1stop,$day3break1stop,$day4break1stop,$day5break1stop,$day6break1stop,$day7break1stop,$day8break1stop,$day9break1stop,$day10break1stop,$day11break1stop,$day12break1stop,$day13break1stop,$day14break1stop;
    global $day1break2start,$day2break2start,$day3break2start,$day4break2start,$day5break2start,$day6break2start,$day7break2start,$day8break2start,$day9break2start,$day10break2start,$day11break2start,$day12break2start,$day13break2start,$day14break2start;
    global $day1break2stop,$day2break2stop,$day3break2stop,$day4break2stop,$day5break2stop,$day6break2stop,$day7break2stop,$day8break2stop,$day9break2stop,$day10break2stop,$day11break2stop,$day12break2stop,$day13break2stop,$day14break2stop;
    global $day1break3start,$day2break3start,$day3break3start,$day4break3start,$day5break3start,$day6break3start,$day7break3start,$day8break3start,$day9break3start,$day10break3start,$day11break3start,$day12break3start,$day13break3start,$day14break3start;
    global $day1break3stop,$day2break3stop,$day3break3stop,$day4break3stop,$day5break3stop,$day6break3stop,$day7break3stop,$day8break3stop,$day9break3stop,$day10break3stop,$day11break3stop,$day12break3stop,$day13break3stop,$day14break3stop;
    
    if ($a == 1) {
      echo "  <tr id=\"".$shortdesc."\">\n";
      echo "    <td class=\"tg-0pky\">$linename</td>\n";
    }
    for ($x = $a; $x <= $b; $x++) {
      echo "    <td class=\"$bg\"><input type=\"text\" class=\"input-time\" id=\"day".$x.$shortdesc."\" name=\"day".$x.$shortdesc."\" value=\"".${"day".$x.$shortdesc}."\" onchange=\"myInit()\"></td>\n";
    }
    if ($b == 14) {
      echo "  </tr>\n";
    }
  }

  function writeBasicRow($linename,$shortdesc,$initialvalue,$a,$b) {
    $x = 0;
    if ($a == 1) {
      echo "  <tr>\n";
      echo "    <td class=\"tg-0lax\">$linename</td>\n";
    }
    for ($x = $a; $x <= $b; $x++) {
      echo "    <td class=\"tg-0lax\"><p id=\"day".$x.$shortdesc."\" name=\"day".$x.$shortdesc."\">$initialvalue</p></td>\n";
    }
    if ($b == 14) {
      echo "  </tr>\n";
    }
  }

  writeRow3("tg-0lax","Day Start","start",1,5);
  writeRow3("tg-266k","Day Start","start",6,7);
  writeRow3("tg-0lax","Day Start","start",8,12);
  writeRow3("tg-266k","Day Start","start",13,14);
  writeRow3("tg-7od5","Break1 Start","break1start",1,5);
  writeRow3("tg-266k","Break1 Start","break1start",6,7);
  writeRow3("tg-7od5","Break1 Start","break1start",8,12);
  writeRow3("tg-266k","Break1 Start","break1start",13,14);
  writeRow3("tg-7od5","Break1 Stop","break1stop",1,5);
  writeRow3("tg-266k","Break1 Stop","break1stop",6,7);
  writeRow3("tg-7od5","Break1 Stop","break1stop",8,12);
  writeRow3("tg-266k","Break1 Stop","break1stop",13,14);
  writeRow3("tg-ncd7","Break2 Start","break2start",1,5);
  writeRow3("tg-266k","Break2 Start","break2start",6,7);
  writeRow3("tg-ncd7","Break2 Start","break2start",8,12);
  writeRow3("tg-266k","Break2 Start","break2start",13,14);
  writeRow3("tg-ncd7","Break2 Stop","break2stop",1,5);
  writeRow3("tg-266k","Break2 Stop","break2stop",6,7);
  writeRow3("tg-ncd7","Break2 Stop","break2stop",8,12);
  writeRow3("tg-266k","Break2 Stop","break2stop",13,14);
  writeRow3("tg-7od5","Break3 Start","break3start",1,5);
  writeRow3("tg-266k","Break3 Start","break3start",6,7);
  writeRow3("tg-7od5","Break3 Start","break3start",8,12);
  writeRow3("tg-266k","Break3 Start","break3start",13,14);
  writeRow3("tg-7od5","Break3 Stop","break3stop",1,5);
  writeRow3("tg-266k","Break3 Stop","break3stop",6,7);
  writeRow3("tg-7od5","Break3 Stop","break3stop",8,12);
  writeRow3("tg-266k","Break3 Stop","break3stop",13,14);
  writeRow3("tg-0pky","Day Stop","stop",1,5);
  writeRow3("tg-266k","Day Stop","stop",6,7);
  writeRow3("tg-0pky","Day Stop","stop",8,12);
  writeRow3("tg-266k","Day Stop","stop",13,14);
  echo "<tr>";
  echo "  <td class=\"tg-0lax\">Total</td>";
  for ($x = 1; $x <= 14; $x++) {
    echo "<td class=\"tg-0lax\"><p id=\"day".$x."tot\">00:00</p></td>";
  }
  echo "</tr>";

  echo "<tr>";
  echo "<td class=\"tg-0pky\">Oncall</td>";
  for ($x = 1; $x <= 14; $x++) {
    // echo ${"day".$x."oncall"};
    echo "<td class=\"tg-0lax\">";
    echo "<input type=\"radio\" id=\"day".$x."oc_no\" name=\"day".$x."oc\" value=\"No\" ";
      if (${"day".$x."oncall"} == NULL) {
        echo "checked";
      }
    echo ">No<br>";
    echo "<input type=\"radio\" id=\"day".$x."oc_onight\" name=\"day".$x."oc\" value=\"On\" ";
    if (${"day".$x."oncall"} == 1) {
      echo "checked";
    }    
    echo ">O/N<br>";
    echo "<input type=\"radio\" id=\"day".$x."oc_247\" name=\"day".$x."oc\" value=\"24\" ";
    if (${"day".$x."oncall"} == 2) {
      echo "checked";
    }   
    echo ">24/7<br>";
    echo "</td>";
  }


  echo "</tr>";
  echo "<tr><td colspan=\"15\">";
  echo "<input type=\"button\" value=\"Unhide Leave\" style=\"width: 100px;\" onclick=\"unhide('lv')\">";
  echo "<input type=\"button\" value=\"Rehide Leave\" style=\"width: 100px;\" onclick=\"rehide('lv')\">";
  echo "</td></tr>";
  writeRow2("tg-ncd7","Annual Leave","lvannual",1,5);
  writeRow2("tg-266k","Annual Leave","lvannual",6,7);
  writeRow2("tg-ncd7","Annual Leave","lvannual",8,12);
  writeRow2("tg-266k","Annual Leave","lvannual",13,14);
  writeRow2("tg-ncd7","Sick Leave","lvsick",1,5);
  writeRow2("tg-266k","Sick Leave","lvsick",6,7);
  writeRow2("tg-ncd7","Sick Leave","lvsick",8,12);
  writeRow2("tg-266k","Sick Leave","lvsick",13,14);
  writeRow2("tg-ncd7","TOIL Taken","lvtoil",1,5);
  writeRow2("tg-266k","TOIL Taken","lvtoil",6,7);
  writeRow2("tg-ncd7","TOIL Taken","lvtoil",8,12);
  writeRow2("tg-266k","TOIL Taken","lvtoil",13,14);
  writeRow2("tg-ncd7","PH/Concessional","lvphcon",1,5);
  writeRow2("tg-266k","PH/Concessional","lvphcon",6,7);
  writeRow2("tg-ncd7","PH/Concessional","lvphcon",8,12);
  writeRow2("tg-266k","PH/Concessional","lvphcon",13,14);
  writeRow2("tg-ncd7","Flex Taken","lvflex",1,5);
  writeRow2("tg-266k","Flex Taken","lvflex",6,7);
  writeRow2("tg-ncd7","Flex Taken","lvflex",8,12);
  writeRow2("tg-266k","Flex Taken","lvflex",13,14);
  echo "<tr id=\"lvtotal\" style=\"display: none;\">";
  echo "  <td class=\"tg-0lax\">Leave Total</td>";
  for ($x = 1; $x <= 14; $x++) {
    echo "<td class=\"tg-0lax\"><p id=\"day".$x."lvtot\">00:00</p></td>";
  }
  echo "</tr>";


  echo "<tr><td colspan=\"15\">";
  echo "<input type=\"button\" value=\"Unhide Oncall\" style=\"width: 100px;\" onclick=\"unhide('oc')\">";
  echo "<input type=\"button\" value=\"Rehide Oncall\" style=\"width: 100px;\" onclick=\"rehide('oc')\">";
  echo "</td></tr>";
  writeRow2("tg-7od5","OC1 Start","oc1start",1,5);
  writeRow2("tg-266k","OC1 Start","oc1start",6,7);
  writeRow2("tg-7od5","OC1 Start","oc1start",8,12);
  writeRow2("tg-266k","OC1 Start","oc1start",13,14);
  writeRow2("tg-7od5","OC1 Stop","oc1stop",1,5);
  writeRow2("tg-266k","OC1 Stop","oc1stop",6,7);
  writeRow2("tg-7od5","OC1 Stop","oc1stop",8,12);
  writeRow2("tg-266k","OC1 Stop","oc1stop",13,14);

  writeRow2("tg-ncd7","OC2 Start","oc2start",1,5);
  writeRow2("tg-266k","OC2 Start","oc2start",6,7);
  writeRow2("tg-ncd7","OC2 Start","oc2start",8,12);
  writeRow2("tg-266k","OC2 Start","oc2start",13,14);
  writeRow2("tg-ncd7","OC2 Stop","oc2stop",1,5);
  writeRow2("tg-266k","OC2 Stop","oc2stop",6,7);
  writeRow2("tg-ncd7","OC2 Stop","oc2stop",8,12);
  writeRow2("tg-266k","OC2 Stop","oc2stop",13,14);

  writeRow2("tg-7od5","OC3 Start","oc3start",1,5);
  writeRow2("tg-266k","OC3 Start","oc3start",6,7);
  writeRow2("tg-7od5","OC3 Start","oc3start",8,12);
  writeRow2("tg-266k","OC3 Start","oc3start",13,14);
  writeRow2("tg-7od5","OC3 Stop","oc3stop",1,5);
  writeRow2("tg-266k","OC3 Stop","oc3stop",6,7);
  writeRow2("tg-7od5","OC3 Stop","oc3stop",8,12);
  writeRow2("tg-266k","OC3 Stop","oc3stop",13,14);
  echo "<tr id=\"octotal\" style=\"display: none;\">";
  echo "  <td class=\"tg-0lax\">Oncall Total</td>";
  for ($x = 1; $x <= 14; $x++) {
    echo "<td class=\"tg-0lax\"><p id=\"day".$x."octot\">00:00</p></td>";
  }
  echo "</tr>";

  echo "<tr><td colspan=\"15\">";
  echo "<input type=\"button\" value=\"Unhide OT\" style=\"width: 100px;\" onclick=\"unhide('ot')\">";
  echo "<input type=\"button\" value=\"Rehide OT\" style=\"width: 100px;\" onclick=\"rehide('ot')\">";
  echo "</td></tr>";
  //function writeRow2($bg,$linename,$shortdesc,$longdesc,$a,$b) {
  writeRow2("tg-7od5","OT1 Start","ot1start",1,5);
  writeRow2("tg-266k","OT1 Start","ot1start",6,7);
  writeRow2("tg-7od5","OT1 Start","ot1start",8,12);
  writeRow2("tg-266k","OT1 Start","ot1start",13,14);
  writeRow2("tg-7od5","OT1 Stop","ot1stop",1,5);
  writeRow2("tg-266k","OT1 Stop","ot1stop",6,7);
  writeRow2("tg-7od5","OT1 Stop","ot1stop",8,12);
  writeRow2("tg-266k","OT1 Stop","ot1stop",13,14);

  writeRow2("tg-ncd7","OT2 Start","ot2start",1,5);
  writeRow2("tg-266k","OT2 Start","ot2start",6,7);
  writeRow2("tg-ncd7","OT2 Start","ot2start",8,12);
  writeRow2("tg-266k","OT2 Start","ot2start",13,14);
  writeRow2("tg-ncd7","OT2 Stop","ot2stop",1,5);
  writeRow2("tg-266k","OT2 Stop","ot2stop",6,7);
  writeRow2("tg-ncd7","OT2 Stop","ot2stop",8,12);
  writeRow2("tg-266k","OT2 Stop","ot2stop",13,14);

  writeRow2("tg-7od5","OT3 Start","ot3start",1,5);
  writeRow2("tg-266k","OT3 Start","ot3start",6,7);
  writeRow2("tg-7od5","OT3 Start","ot3start",8,12);
  writeRow2("tg-266k","OT3 Start","ot3start",13,14);
  writeRow2("tg-7od5","OT3 Stop","ot3stop",1,5);
  writeRow2("tg-266k","OT3 Stop","ot3stop",6,7);
  writeRow2("tg-7od5","OT3 Stop","ot3stop",8,12);
  writeRow2("tg-266k","OT3 Stop","ot3stop",13,14);
  echo "<tr id=\"ottotal\" style=\"display: none;\">";
  echo "  <td class=\"tg-0lax\">OT Total</td>";
  for ($x = 1; $x <= 14; $x++) {
    echo "<td class=\"tg-0lax\"><p id=\"day".$x."ottot\">00:00</p></td>";
  }
  echo "</tr>";


  echo "<tr><td colspan=\"15\">";
  echo "<input type=\"button\" value=\"Unhide TOIL\" style=\"width: 100px;\" onclick=\"unhide('toil');\">";
  echo "<input type=\"button\" value=\"Rehide TOIL\" style=\"width: 100px;\" onclick=\"rehide('toil');\">";
  echo "</td></tr>";
  //function writeRow2($bg,$linename,$shortdesc,$longdesc,$a,$b) {
  writeRow2("tg-7od5","TOIL1 Start","toil1start",1,5);
  writeRow2("tg-266k","TOIL1 Start","toil1start",6,7);
  writeRow2("tg-7od5","TOIL1 Start","toil1start",8,12);
  writeRow2("tg-266k","TOIL1 Start","toil1start",13,14);
  writeRow2("tg-7od5","TOIL1 Stop","toil1stop",1,5);
  writeRow2("tg-266k","TOIL1 Stop","toil1stop",6,7);
  writeRow2("tg-7od5","TOIL1 Stop","toil1stop",8,12);
  writeRow2("tg-266k","TOIL1 Stop","toil1stop",13,14);

  writeRow2("tg-ncd7","TOIL2 Start","toil2start",1,5);
  writeRow2("tg-266k","TOIL2 Start","toil2start",6,7);
  writeRow2("tg-ncd7","TOIL2 Start","toil2start",8,12);
  writeRow2("tg-266k","TOIL2 Start","toil2start",13,14);
  writeRow2("tg-ncd7","TOIL2 Stop","toil2stop",1,5);
  writeRow2("tg-266k","TOIL2 Stop","toil2stop",6,7);
  writeRow2("tg-ncd7","TOIL2 Stop","toil2stop",8,12);
  writeRow2("tg-266k","TOIL2 Stop","toil2stop",13,14);

  writeRow2("tg-7od5","TOIL3 Start","toil3start",1,5);
  writeRow2("tg-266k","TOIL3 Start","toil3start",6,7);
  writeRow2("tg-7od5","TOIL3 Start","toil3start",8,12);
  writeRow2("tg-266k","TOIL3 Start","toil3start",13,14);
  writeRow2("tg-7od5","TOIL3 Stop","toil3stop",1,5);
  writeRow2("tg-266k","TOIL3 Stop","toil3stop",6,7);
  writeRow2("tg-7od5","TOIL3 Stop","toil3stop",8,12);
  writeRow2("tg-266k","TOIL3 Stop","toil3stop",13,14);
  echo "<tr id=\"toiltotal\" style=\"display: none;\">";
  echo "  <td class=\"tg-0lax\">TOIL Earned</td>";
  for ($x = 1; $x <= 14; $x++) {
    echo "<td class=\"tg-0lax\"><p id=\"day".$x."toiltot\">00:00</p></td>";
  }
  echo "</tr>";
  echo "<tr id=\"toilobal\" style=\"display: none;\">";
  echo "  <td class=\"tg-0lax\">TOIL Opening Balance</td>";
  for ($x = 1; $x <= 14; $x++) {
    echo "<td class=\"tg-0lax\"><p id=\"day".$x."toilobal\">00:00</p></td>";
  }
  echo "</tr>";
  echo "<tr id=\"toiltkn\" style=\"display: none;\">";
  echo "  <td class=\"tg-0lax\">TOIL Taken</td>";
  for ($x = 1; $x <= 14; $x++) {
    echo "<td class=\"tg-0lax\"><p id=\"day".$x."toiltkn\">00:00</p></td>";
  }
  echo "</tr>";
  echo "<tr id=\"toilcbal\" style=\"display: none;\">";
  echo "  <td class=\"tg-0lax\">TOIL Closing Balance</td>";
  for ($x = 1; $x <= 14; $x++) {
    echo "<td class=\"tg-0lax\"><p id=\"day".$x."toilcbal\">00:00</p></td>";
  }
  echo "</tr>";


  // (expr1) ? (value if true) : (value if false)
  $flexm = preg_match('/^[\d:\+]+$/',$flexcf) ? 1 : -1;
  $time   = explode(":", $flexcf);
  $hour   = abs($time[0]) * 60 * 60 * 1000;
  $minute = abs($time[1]) * 60 * 1000;
  $flexcf = ($hour + $minute) * $flexm;

  $toilm = preg_match('/^[\d:\+]+$/',$toilcf) ? 1 : -1;
  $time   = explode(":", $toilcf);  
  $hour   = abs($time[0]) * 60 * 60 * 1000;
  $minute = abs($time[1]) * 60 * 1000;
  $toilcf = ($hour + $minute) * $toilm;

  echo "<tr>";
  echo "  <td colspan=\"15\">";
  echo "    <div id=\"day0flexcbal\" style=\"display: none;\">" . $flexcf . "</div>";
  echo "    <div id=\"day0toilcbal\" style=\"display: none;\">" . $toilcf . "</div>";
  echo "  </td>";
  echo "</tr>";

  writeBasicRow("Flex Opening Balance","flexobal","00:00",1,14);
  writeBasicRow("Ordinary Hours","tota","00:00",1,14);
  writeBasicRow("Full Time Std","fulltime","07:36",1,5);
  writeBasicRow("Full Time Std","fulltime","00:00",6,7);
  writeBasicRow("Full Time Std","fulltime","07:36",8,12);
  writeBasicRow("Full Time Std","fulltime","00:00",13,14);
  writeBasicRow("Flex Hours Taken","flextaken","00:00",1,14);
  writeBasicRow("Flex +/- Worked","flexearned","00:00",1,14);
  writeBasicRow("Flex Closing Balance","flexcbal","00:00",1,14);
?>

</tbody>
</table>
</form>




<script>
window.onload = function() {
  var timesCollection = document.getElementsByClassName("input-time");
  var times = Array.from(timesCollection);

  times.forEach(function (time) {
    new Cleave(time, {
      time: true,
      timePattern: ['h', 'm']    
    })
  });
};

window.addEventListener("load", myInit, true); 
  function myInit(){  // call your functions here.... 
    var values = calcday(1,parseInt(document.getElementById("day0flexcbal").innerHTML),parseInt(document.getElementById("day0toilcbal").innerHTML));
    var values = calcday(2,values[0],values[1]);
    var values = calcday(3,values[0],values[1]);
    var values = calcday(4,values[0],values[1]);
    var values = calcday(5,values[0],values[1]);
    var values = calcday(6,values[0],values[1]);
    var values = calcday(7,values[0],values[1]);
    var values = calcday(8,values[0],values[1]);
    var values = calcday(9,values[0],values[1]);
    var values = calcday(10,values[0],values[1]);
    var values = calcday(11,values[0],values[1]);
    var values = calcday(12,values[0],values[1]);
    var values = calcday(13,values[0],values[1]);
    var values = calcday(14,values[0],values[1]);
}; 

function clearDay(x) {
  document.getElementById("day" + x + "start").value = "";
  document.getElementById("day" + x + "stop").value = "";
  document.getElementById("day" + x + "break1start").value = "";
  document.getElementById("day" + x + "break1stop").value = "";
  document.getElementById("day" + x + "break2start").value = "";
  document.getElementById("day" + x + "break2stop").value = "";
  document.getElementById("day" + x + "break3start").value = "";
  document.getElementById("day" + x + "break3stop").value = "";
  myInit();
}

function unhide(id) { 
  if (id == 'lv') { 
    var result_style = document.getElementById(id+'annual').style;
    result_style.display = 'table-row';
    var result_style = document.getElementById(id+'sick').style;
    result_style.display = 'table-row';
    var result_style = document.getElementById(id+'toil').style;
    result_style.display = 'table-row';
    var result_style = document.getElementById(id+'phcon').style;
    result_style.display = 'table-row';
    var result_style = document.getElementById(id+'flex').style;
    result_style.display = 'table-row';
    var result_style = document.getElementById(id+'total').style;
    result_style.display = 'table-row';
  } else { 
    var result_style = document.getElementById(id+'1start').style;
    result_style.display = 'table-row';
    var result_style = document.getElementById(id+'1stop').style;
    result_style.display = 'table-row';
    var result_style = document.getElementById(id+'2start').style;
    result_style.display = 'table-row';
    var result_style = document.getElementById(id+'2stop').style;
    result_style.display = 'table-row';
    var result_style = document.getElementById(id+'3start').style;
    result_style.display = 'table-row';
    var result_style = document.getElementById(id+'3stop').style;
    result_style.display = 'table-row';
    var result_style = document.getElementById(id+'total').style;
    result_style.display = 'table-row';
  }
  if (id == 'toil') { 
    var result_style = document.getElementById(id+'obal').style;
    result_style.display = 'table-row';
    var result_style = document.getElementById(id+'tkn').style;
    result_style.display = 'table-row';
    var result_style = document.getElementById(id+'cbal').style;
    result_style.display = 'table-row';
  }
} 

function rehide(id) { 
  if (id == 'lv') { 
    var result_style = document.getElementById(id+'annual').style;
    result_style.display = 'none';
    var result_style = document.getElementById(id+'sick').style;
    result_style.display = 'none';
    var result_style = document.getElementById(id+'toil').style;
    result_style.display = 'none';
    var result_style = document.getElementById(id+'phcon').style;
    result_style.display = 'none';
    var result_style = document.getElementById(id+'flex').style;
    result_style.display = 'none';
    var result_style = document.getElementById(id+'total').style;
    result_style.display = 'none';
  } else { 
    var result_style = document.getElementById(id+'1start').style;
    result_style.display = 'none';
    var result_style = document.getElementById(id+'1stop').style;
    result_style.display = 'none';
    var result_style = document.getElementById(id+'2start').style;
    result_style.display = 'none';
    var result_style = document.getElementById(id+'2stop').style;
    result_style.display = 'none';
    var result_style = document.getElementById(id+'3start').style;
    result_style.display = 'none';
    var result_style = document.getElementById(id+'3stop').style;
    result_style.display = 'none';
    var result_style = document.getElementById(id+'total').style;
    result_style.display = 'none';
  }
  if (id == 'toil') { 
    var result_style = document.getElementById(id+'obal').style;
    result_style.display = 'none';
    var result_style = document.getElementById(id+'tkn').style;
    result_style.display = 'none';
    var result_style = document.getElementById(id+'cbal').style;
    result_style.display = 'none';
  }
} 

// 2220000
function calcday(x,flexopening,toilopening) {
  //  var x = condition ? exprIfTrue : exprIfFalse
  var dayxstart = document.getElementById("day" + x + "start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "start").value + ":00.000Z";
  var dayxstop  = document.getElementById("day" + x + "stop").value  == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "stop").value  + ":00.000Z";
  var dayxbreak1start = document.getElementById("day" + x + "break1start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "break1start").value + ":00.000Z";
  var dayxbreak1stop  = document.getElementById("day" + x + "break1stop").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "break1stop").value + ":00.000Z";
  var dayxbreak2start = document.getElementById("day" + x + "break2start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "break2start").value + ":00.000Z";
  var dayxbreak2stop  = document.getElementById("day" + x + "break2stop").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "break2stop").value + ":00.000Z";
  var dayxbreak3start = document.getElementById("day" + x + "break3start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "break3start").value + ":00.000Z";
  var dayxbreak3stop  = document.getElementById("day" + x + "break3stop").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "break3stop").value + ":00.000Z";
  break1 = (new Date(dayxbreak1stop) - new Date(dayxbreak1start))
  break2 = (new Date(dayxbreak2stop) - new Date(dayxbreak2start))   
  break3 = (new Date(dayxbreak3stop) - new Date(dayxbreak3start))   
  tot = (new Date(dayxstop) - new Date(dayxstart)) - (break1 + break2 + break3);
  maxhrs = Math.abs(((9*60)+00)*60000);
  tot = Math.min(tot, maxhrs);
  var diffHrs = Math.floor((tot % 86400000) / 3600000); // hours
  var diffMins = Math.round(((tot % 86400000) % 3600000) / 60000); // minutes
  document.getElementById("day" + x + "tot").innerHTML = ('0000'+diffHrs).slice(-2) + ":" + ('0000'+diffMins).slice(-2);
  document.getElementById("day" + x + "tota").innerHTML = ('0000'+diffHrs).slice(-2) + ":" + ('0000'+diffMins).slice(-2);



  // leave totals
  var lvtot = 0;
  var fltot = 0;
  var lvannual = document.getElementById("day" + x + "lvannual").value;
  var lvsick = document.getElementById("day" + x + "lvsick").value;
  var lvtoil = document.getElementById("day" + x + "lvtoil").value;
  var lvphcon = document.getElementById("day" + x + "lvphcon").value;
  var lvflex = document.getElementById("day" + x + "lvflex").value;

  fltothrs = Number(lvflex.slice(0,2));
  fltotmins = Number(lvflex.slice(-2));
  fltot = Math.abs(((fltothrs*60)+fltotmins)*60000); 
  var fltotalhrs = Math.floor((fltot % 86400000) / 3600000); // hours
  var fltotalmins = Math.round(((fltot % 86400000) % 3600000) / 60000); // minutes
  if (fltot > 0) {
    document.getElementById("day" + x + "flextaken").innerHTML = "-" + ('0000'+fltotalhrs).slice(-2) + ":" + ('0000'+fltotalmins).slice(-2);
  } else {
    document.getElementById("day" + x + "flextaken").innerHTML = ('0000'+fltotalhrs).slice(-2) + ":" + ('0000'+fltotalmins).slice(-2);
  }

  toiltakentothrs = Number(lvtoil.slice(0,2));
  toiltakentotmins = Number(lvtoil.slice(-2));
  toiltakentot = Math.abs(((toiltakentothrs*60)+toiltakentotmins)*60000); 
  var toiltakentotalhrs = Math.floor((toiltakentot % 86400000) / 3600000); // hours
  var toiltakentotalmins = Math.round(((toiltakentot % 86400000) % 3600000) / 60000); // minutes
  if (toiltakentot > 0) {
    document.getElementById("day" + x + "toiltkn").innerHTML = "-" + ('0000'+toiltakentotalhrs).slice(-2) + ":" + ('0000'+toiltakentotalmins).slice(-2);
  } else { 
    document.getElementById("day" + x + "toiltkn").innerHTML = ('0000'+toiltakentotalhrs).slice(-2) + ":" + ('0000'+toiltakentotalmins).slice(-2);
  }
  lvtothrs = Number(lvannual.slice(0,2)) + Number(lvsick.slice(0,2)) + Number(lvtoil.slice(0,2)) + Number(lvphcon.slice(0,2)) + Number(lvflex.slice(0,2));
  lvtotmins = Number(lvannual.slice(-2)) + Number(lvsick.slice(-2)) + Number(lvtoil.slice(-2)) + Number(lvphcon.slice(-2)) + Number(lvflex.slice(-2));
  lvtot = Math.abs(((lvtothrs*60)+lvtotmins)*60000); 
  var lvtotalhrs = Math.floor((lvtot % 86400000) / 3600000); // hours
  var lvtotalmins = Math.round(((lvtot % 86400000) % 3600000) / 60000); // minutes
  document.getElementById("day" + x + "lvtot").innerHTML = ('0000'+lvtotalhrs).slice(-2) + ":" + ('0000'+lvtotalmins).slice(-2);



  // oncall totals
  var dayxoc1start = document.getElementById("day" + x + "oc1start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "oc1start").value + ":00.000Z";
  var dayxoc1stop  = document.getElementById("day" + x + "oc1stop").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "oc1stop").value + ":00.000Z";
  var dayxoc2start = document.getElementById("day" + x + "oc2start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "oc2start").value + ":00.000Z";
  var dayxoc2stop  = document.getElementById("day" + x + "oc2stop").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "oc2stop").value + ":00.000Z";
  var dayxoc3start = document.getElementById("day" + x + "oc3start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "oc3start").value + ":00.000Z";
  var dayxoc3stop  = document.getElementById("day" + x + "oc3stop").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "oc3stop").value + ":00.000Z";
  oc1 = (new Date(dayxoc1stop) - new Date(dayxoc1start))
  oc2 = (new Date(dayxoc2stop) - new Date(dayxoc2start))   
  oc3 = (new Date(dayxoc3stop) - new Date(dayxoc3start))   
  octot = oc1 + oc2 + oc3;
  var ocdiffHrs = Math.floor((octot % 86400000) / 3600000); // hours
  var ocdiffMins = Math.round(((octot % 86400000) % 3600000) / 60000); // minutes
  document.getElementById("day" + x + "octot").innerHTML = ('0000'+ocdiffHrs).slice(-2) + ":" + ('0000'+ocdiffMins).slice(-2);



  // OT totals
  var dayxot1start = document.getElementById("day" + x + "ot1start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "ot1start").value + ":00.000Z";
  var dayxot1stop  = document.getElementById("day" + x + "ot1stop").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "ot1stop").value + ":00.000Z";
  var dayxot2start = document.getElementById("day" + x + "ot2start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "ot2start").value + ":00.000Z";
  var dayxot2stop  = document.getElementById("day" + x + "ot2stop").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "ot2stop").value + ":00.000Z";
  var dayxot3start = document.getElementById("day" + x + "ot3start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "ot3start").value + ":00.000Z";
  var dayxot3stop  = document.getElementById("day" + x + "ot3stop").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "ot3stop").value + ":00.000Z";
  ot1 = (new Date(dayxot1stop) - new Date(dayxot1start))
  ot2 = (new Date(dayxot2stop) - new Date(dayxot2start))   
  ot3 = (new Date(dayxot3stop) - new Date(dayxot3start))   
  ottot = ot1 + ot2 + ot3;
  var otdiffHrs = Math.floor((ottot % 86400000) / 3600000); // hours
  var otdiffMins = Math.round(((ottot % 86400000) % 3600000) / 60000); // minutes
  document.getElementById("day" + x + "ottot").innerHTML = ('0000'+otdiffHrs).slice(-2) + ":" + ('0000'+otdiffMins).slice(-2);



  // TOIL totals
  var toilobal = toilopening;
  var toilodiffHrs = Math.floor((Math.abs(toilobal) % 86400000) / 3600000); // hours
  var toilodiffMins = Math.round(((Math.abs(toilobal) % 86400000) % 3600000) / 60000); // minutes
  if (toilobal >= 0) {
    document.getElementById("day" + x + "toilobal").innerHTML = ('0000'+toilodiffHrs).slice(-2) + ":" + ('0000'+toilodiffMins).slice(-2);
  } else { 
    document.getElementById("day" + x + "toilobal").innerHTML = "-" + ('0000'+toilodiffHrs).slice(-2) + ":" + ('0000'+toilodiffMins).slice(-2);
  }
  var dayxtoil1start = document.getElementById("day" + x + "toil1start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "toil1start").value + ":00.000Z";
  var dayxtoil1stop  = document.getElementById("day" + x + "toil1stop").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "toil1stop").value + ":00.000Z";
  var dayxtoil2start = document.getElementById("day" + x + "toil2start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "toil2start").value + ":00.000Z";
  var dayxtoil2stop  = document.getElementById("day" + x + "toil2stop").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "toil2stop").value + ":00.000Z";
  var dayxtoil3start = document.getElementById("day" + x + "toil3start").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "toil3start").value + ":00.000Z";
  var dayxtoil3stop  = document.getElementById("day" + x + "toil3stop").value == "" ? "2021-12-13T00:00:00.000Z" : "2021-12-13T" + document.getElementById("day" + x + "toil3stop").value + ":00.000Z";
  toil1 = (new Date(dayxtoil1stop) - new Date(dayxtoil1start))
  toil2 = (new Date(dayxtoil2stop) - new Date(dayxtoil2start))   
  toil3 = (new Date(dayxtoil3stop) - new Date(dayxtoil3start))   
  toiltot = toil1 + toil2 + toil3;
  var toildiffHrs = Math.floor((toiltot % 86400000) / 3600000); // hours
  var toildiffMins = Math.round(((toiltot % 86400000) % 3600000) / 60000); // minutes
  document.getElementById("day" + x + "toiltot").innerHTML = ('0000'+toildiffHrs).slice(-2) + ":" + ('0000'+toildiffMins).slice(-2);
  var toilcbal = toilobal + toiltot - toiltakentot; // hh:mm
  var ttoildiffHrs = Math.floor((Math.abs(toilcbal) % 86400000) / 3600000); // hours
  var ttoildiffMins = Math.round((((Math.abs(toilcbal)) % 86400000) % 3600000) / 60000); // minutes
  if (toilcbal >= 0) {
    document.getElementById("day" + x + "toilcbal").innerHTML = ('0000'+ttoildiffHrs).slice(-2) + ":" + ('0000'+ttoildiffMins).slice(-2);
  } else { 
    document.getElementById("day" + x + "toilcbal").innerHTML = "-" + ('0000'+ttoildiffHrs).slice(-2) + ":" + ('0000'+ttoildiffMins).slice(-2);
  }



  // Flex calculations
  var flexobal = flexopening;
  console.log(flexobal);
  var flexodiffHrs = Math.floor((Math.abs(flexobal) % 86400000) / 3600000); // hours
  var flexodiffMins = Math.round(((Math.abs(flexobal) % 86400000) % 3600000) / 60000); // minutes
  if (flexobal >= 0) {
    document.getElementById("day" + x + "flexobal").innerHTML = ('0000'+flexodiffHrs).slice(-2) + ":" + ('0000'+flexodiffMins).slice(-2);
  } else { 
    document.getElementById("day" + x + "flexobal").innerHTML = "-" + ('0000'+flexodiffHrs).slice(-2) + ":" + ('0000'+flexodiffMins).slice(-2);
  }
  standardhrs = [6,7,13,14].includes(x) ? 0 : Math.abs(((7*60)+36)*60000); // 27,360,000 
  flextot =  tot - standardhrs + lvtot;
  var flexdiffHrs = Math.floor((Math.abs(flextot) % 86400000) / 3600000); // hours
  var flexdiffMins = Math.round(((Math.abs(flextot) % 86400000) % 3600000) / 60000); // minutes
  //  var x = condition ? exprIfTrue : exprIfFalse
  if (flextot >= 0) {
    document.getElementById("day" + x + "flexearned").innerHTML = ('0000'+flexdiffHrs).slice(-2) + ":" + ('0000'+flexdiffMins).slice(-2);
  } else { 
    document.getElementById("day" + x + "flexearned").innerHTML = "-" + ('0000'+flexdiffHrs).slice(-2) + ":" + ('0000'+flexdiffMins).slice(-2);
  }
  var flexcbal = flexobal + flextot - fltot; // hh:mm
  maxflex = Math.abs(((7*60)+36)*3*60000);
  flexcbal2 = Math.min(Math.abs(flexcbal), maxflex);
  var tflexdiffHrs = Math.floor((Math.abs(flexcbal2) % 86400000) / 3600000); // hours
  var tflexdiffMins = Math.round((((Math.abs(flexcbal2)) % 86400000) % 3600000) / 60000); // minutes
  if (flexcbal >= 0) {
    document.getElementById("day" + x + "flexcbal").innerHTML = ('0000'+tflexdiffHrs).slice(-2) + ":" + ('0000'+tflexdiffMins).slice(-2);
  } else { 
    document.getElementById("day" + x + "flexcbal").innerHTML = "-" + ('0000'+tflexdiffHrs).slice(-2) + ":" + ('0000'+tflexdiffMins).slice(-2);
  }
  // return flexcbal;
  return [flexcbal, toilcbal];
}
</script>