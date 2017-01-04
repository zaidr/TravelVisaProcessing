<?php
	session_start();
	//Redirect to index page if no order is available in session to confirm
	if (!isset($_SESSION["order_placed"])) {
		header("Location: index.php");
		exit();
	}
	
	//DB Connection details
	include 'common/connect.part';
	include 'common/header.part';
	display_header("".$country_name." ".$visa_type." Application - Travel Visa Processing");
?>

  <div class="section main-content">
    <div class="w-container">
      <h1 class="page-heading"><?php echo "Applying for ".$_SESSION["country_name"] ." ". $_SESSION["visa_type"]; ?></h1>
      <div class="page-content">
        <h2 class="confirmation-heading">Thank You for your Travel Visa order</h2>
        <p>Your <?php echo $_SESSION["country_name"] ." ". $_SESSION["visa_type"]; ?> order has been received, and will be processed soon.</p>
        <div class="confirmation">Order Number: <?php echo $_SESSION["orderNum"]; ?>
          <br>
          <br>Visa Type: <?php echo $_SESSION["country_name"] ." ". $_SESSION["visa_type"]; ?>
          <br>Entry Type: <?php echo $_SESSION["entry_type"] ." - Up to ". $_SESSION["entry_validity"] ." days"; ?>
          <br>Processing Time: <?php echo $_SESSION["entry_proccessingTime"] ." days"; ?>
          <br>
          <br>Name: <?php echo $_SESSION["firstName"] ." ";
          								if (isset($_SESSION["middleName"])) { echo $_SESSION[""] ." "; } 
          								echo $_SESSION["lastName"];
          					?>
          <br>Email: <?php echo $_SESSION["email"]; ?>
          <br>Phone 1: <?php echo $_SESSION["phone1"]; ?>
          <?php if (!empty($_SESSION["phone2"])) { echo "<br>Phone 2: ".$_SESSION["phone2"]; } ?>
          <br>
          <br>Price: <?php echo "$".number_format($_SESSION["entry_price"], 2); ?>
          <br>
          <br>Current Status: Waiting for Supporting Documents.</div>
        <h3 class="next-step-heading">Next Step:</h3>
        <p>Download, complete and <b>sign</b> the <?php echo $_SESSION["country_name"] ." ". $_SESSION["visa_type"] ?> Application Form here:</p><a class="button download" href="application/application.php">Download Application Form</a>
        <p>and Bring in or Courier the Application Form and Supporting Documents listed in the form above to:</p>
        <br>
        <p>
        Travel Visa Processing <br>
        123 Anywhere St. Suite 401-B <br>
        Scarborough, Ontario, M1J 3E1 <br>
        Phone: 416-555-5555 <br>
        </p>
        <div class="alert warning bottom"><strong>ALERT</strong> &nbsp;&nbsp;&nbsp; To avoid delays to your Visa Order, bring in or courier the Application Form and all Supporting Documents as soon as possible. Processing your Visa Order can only start once we have received all documents.</div>
      </div>
    </div>
  </div>
  
<?php include 'common/footer.part'; ?>