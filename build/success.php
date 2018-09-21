<?php
  // header('Location: register.php');
  use RIT\Prod\Nelnet as Nelnet;

  require_once('admin/config.php');
  require_once("nelnet-library/src/autoload.php");
  $curDT = date('c');

  $freeOrder = false;
  $transNum = '';
  if(isset($_GET['freeSuccess'])){

    $transStatus = 10;
    $newTransNum = "0 - Free Order using Coupon Code free_bird_18";
    $originalTransNo = $_GET['freeSuccess'];


    $freeOrder = true;

  }else{

    $nelnet = new Nelnet\Nelnet();
    $nelnet->sharedSecret = $hashCode;
    $response = $nelnet->buildResponse($_GET);

    if( $response->validate()){

      //print '<pre>';print_r($response); print '</pre>';

      $originalTransNo = $response->orderNumber;
      $newTransNum = $response->transactionId;
      $transStatus = $response->transactionStatus;
      $transactionTotalAmount = $response->transactionTotalAmount;

    }



    /*$transStatus = isset($_POST['transactionStatus']) ? $_POST['transactionStatus'] : (isset($_GET['transactionStatus']) ? $_GET['transactionStatus'] : "");
    $newTransNum = isset($_POST['transactionId']) ? $_POST['transactionId'] : (isset($_GET['transactionId']) ? $_GET['transactionId'] : "");
    $originalTransNo = isset($_POST['orderNumber']) ? $_POST['orderNumber'] : (isset($_GET['orderNumber']) ? $_GET['orderNumber'] : "");*/

  }

  if($freeOrder){

    $transStatus = '10';
    $transText = "Free Order using Coupon Code free_bird_18";
    updateDB($transStatus, $transText);

  }else if(isset($transStatus)){

    switch($transStatus)
    {
    	case 1:
    		$status = "Accepted credit card payment/refund (successful).";
    		$transText = "1 - Accepted";
    		updateDB($transStatus, $transText);

    		break;
    	case 2:
    		$status = "Rejected credit card payment/refund (declined).";
    		$transText = "2 - Rejected";
    		updateDB($transStatus, $transText);

    		break;
    	case 3:
    		$status =  "Error credit car payment/refund (error).";
    		$transText = "3 - Error";
    		updateDB($transStatus, $transText);

    		break;
    	case 4:
    		$status  = "Unkown credit car payment/refund (unknown).";
    		$transText = "4 - Unknown credit card";
    		updateDB($transStatus, $transText);

    		break;
    	case 5:
    		$status =  "Accepted eCheck payment (successful).";
    		$transText = "5 - Accepted eCheck";
    		updateDB($transStatus, $transText);

    		break;
    	case 6:
    		$status = "Posted eCheck payment (successful).";
    		$transText = "6 - Posted eCheck";
    		updateDB($transStatus, $transText);
    		break;
    	case 7:
    		$status = "Returned eCheck payment (failed).";
    		$transText = "7 - Returned eCheck";
    		updateDB($transStatus, $transText);
    		break;
    	case 8:
    		$status = "NOC eCheck payment (successful).";
    		$transText = "8 - NOC";
    		updateDB($transStatus, $transText);

    		break;
    	default:
    		$status = "Unknown status.";
    		break;
    }

  }

  function updateDB($transStatus, $transText)
  {
  	global $mysqli, $db_table, $originalTransNo, $newTransNum;

  	if($transStatus == 1 || $transStatus == 5 || $transStatus == 6 || $transStatus == 8 || $transStatus == 10)
  	{
  		$updateStatement = "UPDATE $db_table SET refID = ? WHERE trans_num = ?";

  		$stmt = $mysqli->prepare($updateStatement);
  		$stmt->bind_param('ss', $newTransNum, $originalTransNo);
  		$stmt->execute();
  		//echo "<br />Errors: ".$stmt->error." <br />";
  		//if($stmt->affected_rows < 1) echo "<br />There was an error updating your data into the database.";
  		//else echo "updated ".$stmt->affected_rows." rows";
  		$stmt->close();
  		processMail("Credit Card");
  	}
  }
  function processMail($money_type)
  {
  	global $mysqli, $db_table, $originalTransNo, $newTransNum;

  	$selectQuery = "SELECT * FROM $db_table WHERE trans_num = ?";
  	//echo $selectQuery;
  	//echo "original transno: $originalTransNo";
  	$stmt = $mysqli->prepare($selectQuery);
  	$stmt->bind_param("s", $originalTransNo);
  	$stmt->execute();
  	$stmt->bind_result($trans_num, $timestamp, $refID, $recordId, $firstname, $lastname, $address, $city, $state, $zip, $email, $phone, $university, $title, $dietary, $interpreter, $amount, $mailSent);
  	$stmt->fetch();
  	$stmt->close();


  	$message = "Thank you for registering for Thought At Work, a design student conference to be held Oct 20-22, 2018, at Rochester Institute of Technology.\n\nThe following is the information you provided:\n\n";

  	$message .= "Name: " . $firstname .  " " . $lastname . "\n";
  	$message .= "Address:\n";
  	$message .= $address . "\n";
  	$message .= $city . ", " . $state . " " . $zip . "\n\n";
  	if($phone != "") $message .= "Phone: " . $phone . "\n";
  	if($email != "") $message .= "E-mail: " . $email . "\n";
  	if($university != "") $message .= "University or Employer: " . $university . "\n";
    if($title != "") $message .= "Major or Job Title: " . $title . "\n";
    if($dietary != "") $message .= "Dietary Needs: " . $dietary . "\n";
  	if($interpreter != "") $message .= "Interpreter Requested: " . $interpreter . "\n";
  	$message .= "Amount Paid: $".$amount."\n";
  	$message .= "\nVisit the conference website here: http://thoughtatwork.org";




  	//only send the email if it hasn't already been sent
  	if($mailSent != 1) {

  		$testerEmail = "gpltwc@rit.edu";
  		$adminEmail = "hello@thoughtatwork.org";
      $from = "Thought At Work <hello@thoughtatwork.org>";
      $headers = 'From: ' . $from . "\r\n";

  		# Customized Thank You Email

  		sendEmail( $email, $from, 'Thought At Work Registration', $message );
      sendEmail( $adminEmail, $from, 'Thought At Work Registration', $message );
      //sendEmail( $testerEmail, $from, 'Thought At Work Registration', $message );

  		$debug_info = print_r($_SERVER, true)."\n";
  		$debug_info .= print_r($_REQUEST, true);
  		global $curDT;
  		$debug_info .=  $curDT . '<hr />' . $debug_info;
  		//$debug_info .=  "<p>Previous page: $previousPage";
  		$debug_info .=  "<p style=\"color:#ffffff\">Message 1<br />$message";
  		//$debug_info .=  "<p style=\"color:#ffffff\">Message 2<br />$message2";
  		//echo $debug_info;
  		//sendEmail($testerEmail, $from, 'Thought at Work Debug '.$curDT, $debug_info);

  		//update the database to indicate that mail message has been sent
  		$stmt = $mysqli->stmt_init();
  		$updateStatement = "UPDATE $db_table SET mailSent = 1 WHERE trans_num = ?";
  		$stmt->prepare($updateStatement);
  		$stmt->bind_param('s', $originalTransNo);
  		$stmt->execute();
  		$stmt->close();
  	}
  }
  function sendEmail($toAddress, $fromAddress, $subject, $body)
  {

    $headers = 'From: ' . $fromAddress . "\r\n";
  	mail($toAddress, $subject, $body, $headers);
  }

?>

<!--source /templates/layout-success.njk -->


<!doctype html>
<html>
<head>
	<meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Thought At Work 2018</title>
  <meta name="keywords" content="sample, something">
  <meta name="format-detection" content="telephone=no">
  <meta charset="utf-8">
  <meta name="viewport" content="user-scalable=0, initial-scale=1.0, width=device-width, maximum-scale=1, minimum-scale=1"
  >
  <meta name="format-detection" content="telephone=no">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta property="og:url" content="http://thoughtatwork.cias.rit.edu">
  <meta property="og:type" content="article">
  <meta property="og:title" content="Thought At Work, a student-run design conference">
  <meta property="og:description" content="Thought At Work is a three-day, student-run, student-focused design conference that takes place every October at Rochester Institute of Technology."
  >
  <meta property="og:image" content="http://thoughtatwork.cias.rit.edu/assets/graphics/WebBanner_TAW2018.jpg"
  >
  <meta property="fb:app_id" content="486507185043060">
  <meta name="twitter:card" content="product">
  <meta name="twitter:site" content="@TAW_RIT">
  <meta name="twitter:title" content="Thought At Work, a student-run design conference">
  <meta name="twitter:description" content="Thought At Work is a three-day, student-run, student-focused design conference that takes place every October at Rochester Institute of Technology."
  >
  <meta name="twitter:creator" content="@TAW_RIT">
  <meta name="twitter:image" content="http://thoughtatwork.cias.rit.edu/assets/graphics/WebBanner_TAW2018.jpg"
  >
  <meta name="description" content="Student-Run Design Conference">
  <meta name="title" content="Thought at Work">
	<link rel="icon" type="image/ico" href="assets/graphics/faviconPurple2018.png">
	<base href="/">
	
	<link rel="stylesheet"
	      type="text/css"
	      href="styles/register.css">
	<script type="text/javascript"
	        src="vendors/jquery.min.js"></script>
</head>

<body class="dotGrid-background">
<div class="flex flex-justify-center flex-align-center register2018-nav">
  <img
    class="register2018-logo"
    src="http://thoughtatwork.cias.rit.edu/assets/graphics/icons/register2018-navLogo.svg"
  />
  <p class="register2018-link">Thought At Work 2018 Registration</p>
</div>
	

    

    <div class="container register2018-container">
        <script src="js/register.js"></script>

<div class="container form__margin-top gridish-container--complete gridish-grid">
    <?php if(isset($transStatus) && ($transStatus == 1 || $transStatus == 5 || $transStatus == 6 || $transStatus == 8 || $transStatus == 10)){ ?>

    <h1>Thank You For Registering!</h1>
    <br>
<br>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
<div class="">
    <label class="label">
        <input class="label__checkbox" type="checkbox" />
        <span class="label__text">
            <span class="label__check">
                <i class="fa fa-check icon"></i>
            </span>
        </span>
    </label>
</div>
<br><br>
    <p>You will be receiving a confirmation email from
        <a href="mailto:hello@thoughtatwork.org">hello@thoughtatwork.org</a>
    </p>
    <br>
    <!--  -->


    <?php }else{ ?>

    <div class="text-card-default">
        <br><br>
        <h2 class="confirm-heading">There was a problem processing your order.</h2>
        <br>
        <p class="confirm-email">Please contact
            <a href="mailto:hello@thoughtatwork.org">hello@thoughtatwork.org</a>
        </p>

    </div>

    <?php } ?>

</div>

        
    </div>

	<!-- Load scripts. -->
</body>
