<?php

  if (!isset($_SESSION)) {
    session_start();
  }
  // anti flood protection
  if(@$_SESSION['last_session_request'] > time() - 300){
    die("Flood protection: Report again after 5 minutes please, thanks !");
    exit;
  }
  $_SESSION['last_session_request'] = time();

  // Read XML data reported from Primo
  // ... write this after Perry updates the template

  $info = "";
  $report_option = "";

  foreach ($_POST as $param_name => $param_val) {
    if ($param_name == 'badlink_report_option_id') {

      if (strlen($param_val) > 1) {
        $report_option = $param_val;
      } else {
        if ($param_val == 1) $report_option = "The PDF is blank/missing pages";
        elseif ($param_val == 2) $report_option = "I received a 404/page not found error";
        elseif ($param_val == 3) $report_option = "The website prompted me to pay to access the article";
        elseif ($param_val == 4) $report_option = "The link went to another website other than the selected article";
        elseif ($param_val == 5) $report_option = "Full text for the article was not available, only the abstract or citation";
        elseif ($param_val == 6) $report_option = "Something else went wrong, explain in the comments below";
      }
      $info = $info . "$param_name : $report_option<br />\n";

    } else {
      $info = $info . "$param_name : $param_val<br />\n";
    }
  }

  $refer_link = @$_POST["badlink_report_location"];

  // send email only from report
  if ($refer_link != "") {

    // detect what library where the broken link reported at
    $lib = "";
    $lines = file('config.ini');
    $found = false;

    foreach ($lines as $line) {
      $lib = trim(explode("=", $line)[0]);
      $temp = trim(explode("=", $line)[1]);
      $url_identify_list = trim(explode("|", $temp)[0]);
      $email_list = trim(explode("|", $temp)[1]);

      $url_identify_array = explode(",", $url_identify_list);
      foreach ($url_identify_array as $url_identify) {
        if (strpos($refer_link, trim($url_identify)) > 0) {
          $found = true;
          break;
        }
      }
      if ($found) {
        break;
      }
    }

    $to = @$email_list;

    if ($lib != "") {
      // save datetime reported
      $filename1 = "files/" . $lib . "_" . date("Y") . "_" .date("m") . ".csv";
      $fp1 = fopen($filename1, 'a');//opens file in append mode

      $filenameA = "files/ALL_" . date("Y") . "_" .date("m") . ".csv";
      $fpA = fopen($filenameA, 'a');//opens file in append mode

      if (filesize($filename1) == 0) {
        fwrite($fp1, "Date Reported,format,au,btitle,atitle,jtitle,badlink_report_option_id,badlink_report_email,badlink_report_comments,vid,badlink_report_location\n");
      }
      if (filesize($filenameA) == 0) {
        fwrite($fpA, "LIB,Date Reported,format,au,btitle,atitle,jtitle,badlink_report_option_id,badlink_report_email,badlink_report_comments,vid,badlink_report_location\n");
      }

      fwrite($fp1, '"' . date('M d, Y, h:i:sp', time()) . '"');
      fwrite($fpA, $lib . ',"' . date('M d, Y, h:i:sp', time()) . '"');

      $format = @$_POST["format"];
      $au = @$_POST["au"];
      $btitle = @$_POST["btitle"];
      $atitle = @$_POST["atitle"];
      $badlink_report_option_id = @$_POST["badlink_report_option_id"];
      $badlink_report_email = @$_POST["badlink_report_email"];
      $badlink_report_comments = @$_POST["badlink_report_comments"];
      $vid = @$_POST["vid"];
      $badlink_report_location = @$_POST["badlink_report_location"];

      $jtitle = @$_POST["jtitle"];

      foreach ($_POST as $param_name => $param_val) {
        if ($param_name == 'badlink_report_option_id') {

          if (strlen($param_val) > 1) {
            $report_option = $param_val;
          } else {
            if ($param_val == 1) $report_option = "The PDF is blank/missing pages";
            elseif ($param_val == 2) $report_option = "I received a 404/page not found error";
            elseif ($param_val == 3) $report_option = "The website prompted me to pay to access the article";
            elseif ($param_val == 4) $report_option = "The link went to another website other than the selected article";
            elseif ($param_val == 5) $report_option = "Full text for the article was not available, only the abstract or citation";
            elseif ($param_val == 6) $report_option = "Something else went wrong, explain in the comments below";
          }
          $info = $info . "$param_name : $report_option<br />\n";
          $badlink_report_option_id = $report_option;
        } else {
          $info = $info . "$param_name : $param_val<br />\n";
        }
      }

      // save info into log file
      fwrite($fp1, ',"' . $format . '"' . ',"' . $au . '"' . ',"' . $btitle . '"' . ',"' . $atitle . '"' . ',"' . $jtitle . '"' . ',"' . $badlink_report_option_id . '"' . ',"' . $badlink_report_email . '"' . ',"' . $badlink_report_comments . '"' . ',"' . $vid . '"' . ',"' . $badlink_report_location . '"');
      fwrite($fpA, ',"' . $format . '"' . ',"' . $au . '"' . ',"' . $btitle . '"' . ',"' . $atitle . '"' . ',"' . $jtitle . '"' . ',"' . $badlink_report_option_id . '"' . ',"' . $badlink_report_email . '"' . ',"' . $badlink_report_comments . '"' . ',"' . $vid . '"' . ',"' . $badlink_report_location . '"');

      // close files
      fwrite($fp1, "\n");
      fwrite($fpA, "\n");
      fclose($fp1);
      fclose($fpA);

    } // end lib != ""

    $subject = "Primo VE Broken Link Report on ".date('M d, Y, h:i:sp', time());
    $message = "There is a broken link report for the following article: <a href=$refer_link>click here</a> to view<br>";
    $message = $message . "Or copy & paste the below URL into your internet browser:<br>$refer_link<br><br>";

    if ($report_option != "") {
      $message = $message . "Report Option: " . $report_option . "<br><br>";
    }

    if (@$_POST["badlink_report_comments"] != "") {
      $message = $message . "Report Comment: " . $_POST["badlink_report_comments"] . "<br><br>";
    }

    if (@$_POST["badlink_report_email"] != "") {
      $message = $message . "Report Email: " . $_POST["badlink_report_email"] . "<br><br>";
    }

    $message = $message . "<br>Article details:<br>$info<br><br><br>Primo VE Broken Link Report.";

    if ($lib == "Special_LIB") {
      $headers = 'From: Special Person <special_person@your_domain.edu>' . "\n";
    } else {
      $headers = 'From: Primo VE Broken Link Report <noreply@your_domain.edu>' . "\n";
    }
    //$headers = 'From: Primo VE Broken Link Report <noreply@mnpals.net>' . "\n";

    if (@$_POST["cc_email"] == 1) {
      $cc_email = $_POST['badlink_report_email'];
      $headers .= "CC: $cc_email" . "\n";
    }

    // BCC Someone
    //$headers .= "Bcc: someone1@your_domain.edu, someone2@mnsu.edu\r\n";

    $headers .= 'MIME-Version: 1.0' . "\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";


    if ($lib == "Special_LIB") {
      $returnpath = '-f special_person@your_domain.edu';
    } else {
      $returnpath = '-f noreply@your_domain.edu';
    }

    if (mail($to,$subject,$message,$headers, $returnpath)) echo "Report was sent.";
    else echo "Failed";

    // Store log files
  } // end if $refer_link != ""

?>
