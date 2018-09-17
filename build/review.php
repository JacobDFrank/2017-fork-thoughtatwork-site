
<!--source /templates/layout-review.njk -->

<?php

  //print '<pre>';print_r($_POST); print '</pre>';

  // header('Location: register.php');
  use RIT\Prod\Nelnet as Nelnet;

  require_once('admin/config.php');
  require_once("nelnet-library/src/autoload.php");

  $proceed = false;
  $speaker = false;

  //print '<pre>';print_r($_POST); print '</pre>';

  if( isset($_POST['submit']) && !empty($_POST['orderNumber']) ){

    $orderID = $_POST['orderNumber'];
    $sql = "SELECT email, amount FROM $db_table WHERE trans_num = $orderID";

    $result = $mysqli->query($sql);
    print $sql;
    print_r($result);
    if ($result->num_rows == 1) {

      $row = $result->fetch_assoc();
      $orderAmount = $row['amount'];
      $orderEmail = $row['email'];
      if($orderAmount > 0){$proceed = true;}

    }



    if($proceed){

      $nelnet = new Nelnet\Nelnet();
      $nelnet->orderType = $paymentType;
      $nelnet->sharedSecret = $hashCode;
      $nelnet->redirectUrl = $redirectURL;
      $nelnet->setNelnetEnvironment('production');

      $params = array(
          "id" => $orderID,
          "amount" => ($orderAmount * 100),
          "email" => $orderEmail,
      );

      $request = $nelnet->buildRequest();
      $request->send($params);
    }else{



    }

  }


  $insertedID = '';

  function strip_tags_deep($value)
  {
    return is_array($value) ?
      array_map('strip_tags_deep', $value) :
      strip_tags($value);
  }
  $_POST = strip_tags_deep($_POST);

  function xss_clean($data)
  {
  	// Fix &entity\n;
  	$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
  	$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
  	$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
  	$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

  	// Remove any attribute starting with "on" or xmlns
  	$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

  	// Remove javascript: and vbscript: protocols
  	$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
  	$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
  	$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

  	// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
  	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
  	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
  	$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

  	// Remove namespaced elements (we do not need them)
  	$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

  	do
  	{
  			// Remove really unwanted tags
  			$old_data = $data;
  			$data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
  	}
  	while ($old_data !== $data);

  	// we are done...
  	return $data;
  }

  #CHANGE THIS TO MAKE MORE REQUIREMENTS
  function verifyFormData()
  {
    global $amount, $firstname,  $lastname,  $address, $city,  $state, $zip, $email, $phone, $university;

    if( $amount == '' || $firstname == '' ||  $lastname == '' ||  $email == '' || !filter_var($email, FILTER_VALIDATE_EMAIL))
    {
      return 0;
      }
    else
    {
          return 1;
      }
  }

  function writeToDB()
  {
  	global $mysqli, $db_table, $content;

  	global $adjustedAmount, $referenceId, $firstname, $lastname, $address, $city, $state, $zip, $email, $phone, $university, $title, $dietary, $interpreter, $insertedID;

  	$insertStatement = "INSERT INTO $db_table (first, last, address, city, state, zipcode, email, phone, university, title, dietary, interpreterNeeded, amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";

  	//echo $insertStatement;
  	$stmt = $mysqli->prepare($insertStatement);

  	if (! $stmt) {
        echo "Error: ".$mysqli->error;
        exit;
    }

  	$stmt->bind_param('sssssssssssss', $firstname, $lastname, $address, $city, $state, $zip, $email, $phone, $university, $title, $dietary, $interpreter, $adjustedAmount);
  	$stmt->execute();


  	$numRows = $stmt->affected_rows;
  	//echo $stmt->error;
  	if($numRows < 0){
      $content .=   "there was an error inserting your data";
    }else{

      $insertedID = $mysqli->insert_id;
      //print $insertedID;
    }
  	$stmt->close();
  }

  function sendError($vError)
  {
  	global $content;
  	$content .=  '<br />
  	<div style=\"text-align:left\">
  		<div style="padding: 20px;">';
  	$content .=   $vError;
  	$content .=   '
  		</div>
  	</div>';
  }




  $adjustedAmount = '40';



  $amount = number_format($adjustedAmount,2);
  $firstname = isset($_POST['first-name']) ? $_POST['first-name'] : "";
  $lastname = isset($_POST['last-name']) ? $_POST['last-name'] : "";
  $address = isset($_POST['address']) ? $_POST['address'] : "";
  $city = isset($_POST['city']) ? $_POST['city'] : "";
  $state = isset($_POST['state']) ? $_POST['state'] : "";
  $zip = isset($_POST['zip']) ? $_POST['zip'] : "";
  $email = isset($_POST['email']) ? $_POST['email'] : "";
  $phone = isset($_POST['phone']) ? $_POST['phone'] : "";
  $university = isset($_POST['employer']) ? $_POST['employer'] : "";
  $title = isset($_POST['title']) ? $_POST['title'] : "";
  $dietary = isset($_POST['dietary']) ? $_POST['dietary'] : "";
  $interpreter = isset($_POST['interpret']) ? $_POST['interpret'] : "No";
  $coupon = isset($_POST['coupon']) ? $_POST['coupon'] : "";

  $query = "SELECT `refID` FROM `registrations` WHERE `refID` != ''";
	$numOrders = mysqli_num_rows($mysqli->query( $query ));



  if(strtolower($coupon) == strtolower('free_bird_18') ){

    $adjustedAmount = '0';

  }else if(strtolower($coupon) == strtolower('taw_early_bird_2018')){

    $adjustedAmount = '30';

  }else if($numOrders < 100 && strtotime("1 October 2018") > strtotime('now')){

    $adjustedAmount = '35';

  }



  if(verifyFormData()){


  	writeToDB();



    $transNum = $insertedID;

    if($adjustedAmount === '0'){

      header("Status: 301 Moved Permanently");
      header("Location:./success.php?freeSuccess=". $transNum);


    }
  	//processMail( "Credit Card");

  	//$redirectURL = "https://".$_SERVER['HTTP_HOST']."/success.php";
  	$formData = "<form action=\"\" name=\"form\" method=\"POST\" id=\"payment\"  style = \"vertical-align:middle;margin:0;margin-bottom:2em;font-size: 1.2em;
      line-height: 1.5em;\">";


  	$formData .= "<input type=\"hidden\" name=\"orderType\" value=\"ThoughtAtWork\">";


  	$formData .= "<input type=\"hidden\" name=\"orderNumber\" value=\"$transNum\">
  	<input type=\"hidden\" name=\"amount\" value=\"".str_replace(array(".",","),"",$adjustedAmount)."\">
  	<input type=\"hidden\" name=\"redirectUrl\" value=\"$redirectURL\">
  	<input type=\"hidden\" name=\"email\" value=\"$email\">
  	<input type=\"hidden\" name=\"redirectUrlParameters\" value=\"transactionStatus,transactionType,transactionId,originalTransactionId,orderNumber,transactionTotalAmount\">

  	<span class=\"elliot\"><strong></strong>Payment amount: \$$adjustedAmount</strong></span> <br /><br /><input type=\"submit\" name=\"submit\" value=\"Proceed to Payment\" id=\"submit\" class=\"btn contact-btn btn-effect elliot\" \>
  	</form>
  	";


  	$content .= $formData;

  }else{

    $errorMsg = '<b>The following fields are required and were not filled:</b><br />';
    $emailInvalid = '';
    if( $amount == '' ){ $errorMsg.="Amount<br />"; }
    if( $firstname == '' ){ $errorMsg.="First Name<br />"; }
    if( $lastname == '' ) { $errorMsg .= "Last Name<br />"; }
    if( $address == '' ){ $errorMsg.="Address<br />"; }
    if( $city == '' ){ $errorMsg.="City<br />"; }
    if( $state == '' ){ $errorMsg.="State<br />"; }
    if( $zip == '' ){ $errorMsg.="Zipcode<br />"; }
    if( $email == ''){ $errorMsg.="Email<br />"; }
    else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){$emailInvalid = '<p>The email you entered is invalid.</p>';}
    $errorMsg.= $emailInvalid.'<br /><a href="javascript:history.go(-1)">Go Back and Retry</a>';

    sendError($errorMsg);

  }

?>

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

<a href="register">
    <div id="flag"></div>
</a>

<div id="register" class="container form__margin-top gridish-container--complete gridish-grid">
    
    <h1>Registration review</h1>
    <br><br>
    <h4>Thank you for completing the first step in your registration.</h4> <br> <br>
    <p>To make your secure credit card payment, click the button below.</p>
    <br>
    
    <?php


    //print '<pre>';print_r($_POST);print '</pre>';
    echo $content;

      ?>
      
    <br><br>
<p>Immediately after the payment transaction is processed, you will receive an email verifying the information you provided.</p>
</div>

        
    </div>

	<!-- Load scripts. -->
</body>
</html>
