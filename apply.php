<?php
	session_start();
	
	//DB Connection details
	include 'common/connect.part';
	include 'common/header.part';
	//Globals
	$HST = 0.13;
	//used in form validation
	$entry_error = $citizenship_error = $residency_error = $res_not_picked = false;
	$error = false;
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") { //got here after pressing the submit button
		//validate data
		if ($_POST["entry"] == "NONE") { $entry_error = $error = true; }
		if ($_POST["citizenship"] == "NONE") { $citizenship_error = $error = true; }
    if ($_POST["residency"] == "NONE") { $res_not_picked = $error = true; }
    if ($_POST["residency"] == "NOSERVICE") { $residency_error = $error = true; }
    
		//update database and set session vars
		$db_error = false;
		if (!$error) {
			//set session vars
			$_SESSION["email"] = $_POST["email"];
			$_SESSION["firstName"] = $_POST["firstName"];
			$_SESSION["middleName"] = $_POST["middleName"];
			$_SESSION["lastName"] = $_POST["lastName"];
			$_SESSION["company"] = $_POST["company"];
			$_SESSION["phone1"] = $_POST["phone1"];
			$_SESSION["phone2"] = $_POST["phone2"];
			$_SESSION["entry_ID"] = $_POST["entry"];
			
			//generate unique order number
			$stmt = $db->query("SELECT MAX(ID) FROM TravelVisaProcessing.orders");
			$maxID = "";
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$maxID = $row["MAX(ID)"];
			}
			$order_start_num = 113206389;
			$orderNum = $order_start_num + $maxID;
			$_SESSION["orderNum"] = $orderNum;
			
			$stmt = $db->query("SELECT * FROM entry WHERE ID = '".$_POST["entry"]."'");
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$_SESSION["entry_type"] = $row["type"];
				$_SESSION["entry_validity"] = $row["validity"];
				$_SESSION["entry_proccessingTime"] = $row["processingTime"];
				$_SESSION["entry_price"] = ($row['embassyFee'])+($row['TVPfee']*(1+$HST));
				$_SESSION["entry_price_before_tax"] = $row['embassyFee'] + $row['TVPfee'];
				$_SESSION["entry_tax_rate"] = $HST;
			}
			
			//update database with new order
			$stmt = $db->prepare("INSERT INTO orders(orderNum, email, firstName, middleName, lastName, company, phone1, phone2, address, city, province, postalcode, price, tax_rate, country_ID, visa_ID, entry_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
			try {
				$stmt->execute(array(	$orderNum,
															$_POST["email"],
															$_POST["firstName"],
															$_POST["middleName"],
															$_POST["lastName"],
															$_POST["company"],
															$_POST["phone1"],
															$_POST["phone2"],
															$_POST["address"],
															$_POST["city"],
															$_POST["province"],
															$_POST["postalcode"],
															$_SESSION["entry_price_before_tax"],
															$_SESSION["entry_tax_rate"],
															$_SESSION["country_ID"],
															$_SESSION["visa_ID"],
															$_SESSION["entry_ID"]));
			} catch (PDOException $ex) {
				$db_error = true;
			}
			
			//update order status			
			$orderID = "";
			$stmt = $db->query("SELECT ID FROM orders WHERE orderNum = '".$orderNum."'");
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$orderID = $row["ID"];
			}
			
			$stmt = $db->prepare("INSERT INTO order_status(curr_status, order_ID) VALUES (?, ?)");
			try {
				$stmt->execute( array( "Order Received. Waiting for Supporting Documents.", $orderID) );
			} catch (PDOException $ex) {
				$db_error = true;
			}
			
			//Order placed
			$_SESSION["order_placed"] = true;
			
			//redirect to confirmation page
			if (!$db_error) {
				header("Location: confirm.php");
				exit();
			}
			
		}
	} else { //got here through visa page, or direct URL
		if ( isset($_GET["visa"]) ) { //validating correct URL and GET vars
			$_SESSION["visa_ID"] = $_GET["visa"];
			$country_name = "";
			$visa_type = "";
			$country_ID = "";
			
			$stmt = $db->query("SELECT country_ID, type FROM visa WHERE ID = '".$_SESSION["visa_ID"]."'");
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$visa_type = $row["type"];
				$country_ID = $row["country_ID"];
			}
			$stmt = $db->query("SELECT name FROM country WHERE ID = '".$country_ID."'");
			while ($name_row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$country_name = $name_row["name"];
			}
			$_SESSION["country_name"] = $country_name;
			$_SESSION["visa_type"] = $visa_type;
			$_SESSION["country_ID"] = $country_ID;
			
		} else { //incorrect URL, take me back to index
			echo "<script type='text/javascript'>";
    	echo "	window.location = 'index.php' ";
  	  echo "</script>"; 
		}
	}
	
	display_header("Applying for ".$country_name." ".$visa_type." - Travel Visa Processing");
?>  
  <div class="section main-content">
    <div class="w-container">
      <h1 class="online-application-heading">Applying for <?php echo "". $_SESSION["country_name"] ." ". $_SESSION["visa_type"]; ?></h1>
      <div class="application-content">
        <?php //Error info
        	if ($db_error) {
        		echo "<div class='alert application'><strong>ALERT</strong> &nbsp;&nbsp;&nbsp; Travel Visa Processing is experiencing some technical difficulties at the moment. Please call us directy, or try back later.</div>";
        	}
        	elseif ($residency_error) {
        		echo "<div class='alert application'><strong>ALERT</strong> &nbsp;&nbsp;&nbsp; Travel Visa Processing is not currently servicing international clients.</div>";
        	} elseif ($error) {
        		echo "<div class='alert application'>";
        		if ($entry_error) { echo "* Please choose what kind of Visa Entry you would like."; }
        		if ($citizenship_error AND $entry_error) { echo "<br>"; }
        		if ($citizenship_error) { echo "* Please choose your citizenship."; }
        		if (($citizenship_error AND $res_not_picked) || ($entry_error AND $res_not_picked)) { echo "<br>"; }
        		if ($res_not_picked) { echo "* Please choose where you live under 'Residency'."; }
        		echo "</div>";
        	}
        ?>
        <div class="w-form">
        
          <form name="order" id="order" method="post" action="apply.php">
            <div class="entry">
              <select class="w-select entry-selector" id="entry" name="entry" required="required">
              	<option value="NONE">Entry Type</option>
                <?php
									$stmt = $db->query("SELECT * FROM entry WHERE visa_ID = '".$_SESSION["visa_ID"]."'");
									while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
										echo "<option value='".$row["ID"]."' ";
										if ($_SERVER["REQUEST_METHOD"] == "POST" AND $row["ID"] == $_POST["entry"]) {
											echo " selected='selected' ";
										}
										echo ">".$row['type'] ." - Up to ". $row['validity'] ." day stay - Processing Time: ". $row['processingTime']." business days - $".number_format(($row['embassyFee'])+($row['TVPfee']*(1+$HST)), 2)."</option>";
									}
								?>
              </select>
            </div>
            <div class="email">
              <input class="w-input email-field" id="email" type="email" placeholder="Email" name="email" data-name="email" required="required" value="<?php if ($error) { echo $_POST["email"]; }?>">
            </div>
            <div>
              <input class="w-input name" id="firstname" type="text" placeholder="First Name" name="firstName" required="required" data-name="firstName" value="<?php if ($error) { echo $_POST["firstName"]; }?>">
              <input class="w-input name" id="lastname" type="text" placeholder="Last Name" name="lastName" required="required" data-name="lastName" value="<?php if ($error) { echo $_POST["lastName"]; }?>">
              <input class="w-input name" id="middlename" type="text" placeholder="Middle Name" name="middleName" data-name="middleName" value="<?php if ($error) { echo $_POST["middleName"]; }?>">
              <input class="w-input name" id="company" type="text" placeholder="Company (If Applicable)" name="company" data-name="company" value="<?php if ($error) { echo $_POST["company"]; }?>">
              <input class="w-input name" id="phone1" type="number" placeholder="Phone Number 1 (no dashes or spaces)" name="phone1" required="required" data-name="phone1" value="<?php if ($error) { echo $_POST["phone1"]; }?>">
              <input class="w-input name" id="phone2" type="number" placeholder="Phone Number 2 (no dashes or spaces)" name="phone2" data-name="phone2" value="<?php if ($error) { echo $_POST["phone2"]; }?>">
              <input class="w-input name" id="phone2" type="text" placeholder="Street Address" name="address" required="required" data-name="address" value="<?php if ($error) { echo $_POST["address"]; }?>">
              <input class="w-input name" id="phone2" type="text" placeholder="City" name="city" required="required" data-name="city" value="<?php if ($error) { echo $_POST["city"]; }?>">
              <input class="w-input name" id="phone2" type="text" placeholder="Province" name="province" required="required" data-name="province" value="<?php if ($error) { echo $_POST["province"]; }?>">
              <input class="w-input name" id="phone2" type="text" placeholder="Postal Code" name="postalcode" required="required" data-name="postalcode" value="<?php if ($error) { echo $_POST["postalcode"]; }?>">
            </div>
            <div class="w-clearfix citizenship">
              <select class="w-select citizenship-picker" id="citizenship" name="citizenship" data-name="Citizenship" required="required" >
                <option value="NONE">Select one...</option>
                <option value="36">Canada</option>
              	<option disabled>──────────</option>
                <?php
									$stmt = $db->query("SELECT * FROM country");
									while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
										echo "<option value='".$row["ID"]."' ";
										if ($_SERVER["REQUEST_METHOD"] == "POST" AND $row["ID"] == $_POST["citizenship"]) {
											echo " selected='selected' ";
										}
										echo ">".$row['name'] ."</option>";
									}
								?>
              </select>
              <label class="form-label" for="citizenship">Citizenship:</label>
              <select class="w-select citizenship-picker" id="residency" name="residency" data-name="Residency" required="required">
                <option value="NONE">Select one...</option>
                <option value="Toronto" <?php if ($_SERVER["REQUEST_METHOD"] == "POST" AND $_POST["residency"] == "Toronto") { echo " selected='selected' "; } ?>>Toronto, Ontario, Canada</option>
                <option value="Ontario" <?php if ($_SERVER["REQUEST_METHOD"] == "POST" AND $_POST["residency"] == "Ontario") { echo " selected='selected' "; } ?>>Within Ontario, Canada</option>
                <option value="Canada" <?php if ($_SERVER["REQUEST_METHOD"] == "POST" AND $_POST["residency"] == "Canada") { echo " selected='selected' "; } ?>>Within Canada</option>
                <option disabled>──────────</option>
                <?php
									$stmt = $db->query("SELECT * FROM country");
									while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
										echo "<option value='NOSERVICE' ";
										if ($_SERVER["REQUEST_METHOD"] == "POST" AND $row["ID"] == $_POST["residency"]) {
											echo " selected='selected' ";
										}
										echo ">". $row['name'] ."</option>";
									}
								?>
              </select>
              <label class="form-label" for="residency">Residency:</label>
            </div>
            <div class="application-submission">
              <input class="w-button application-submit" type="submit" value="Submit" data-wait="Please wait...">
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

<?php include 'common/footer.part'; ?>