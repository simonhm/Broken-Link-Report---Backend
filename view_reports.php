<?php
  $lib = @$_GET["lib"];
  $year = @$_GET["year"];
  $filename = "No logs";

  if ($year == "") {
  $year = date("Y");
  }
  echo "Broken Link Reports for <b>" . $year . "</b><br><br>";
  if ($lib != "") {
  $i = 0;
  for ($m = 1; $m <= 12; $m++) {
    if ($m < 10) {
      $filename = "files/" . $lib . "_" . $year . "_0" . $m . ".csv";
    } else $filename = "files/" . $lib . "_" . $year . "_" . $m . ".csv";
    if (filesize($filename) != 0) {
      $i = $i + 1;
      $dateObj   = DateTime::createFromFormat('!m', $m);
      $monthName = $dateObj->format('F'); // March
      echo "<a target=_blank href=$filename>" . "Reports for $year - $monthName</a><br>";
    }
  }
  if ($i == 0) echo "There is no reports for " . $year . ".";
  }
  $y = date("Y");
  if ($y > 2022) {
  echo "<br><br><br>View reports for ";
  for ($i = 0; $i <= 2; $i++) {
    $vy = $y - $i;
    echo "<a href=view_reports.php?lib=$lib&year=$vy>" . $vy . "</a>  ";
  }
  }
  echo "<br><br><center>PALS@" . $y . " - Broken Link Report Logging</center>";
?>
