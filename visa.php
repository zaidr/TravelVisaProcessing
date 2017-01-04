<?php 
include 'common/non_service_country.php';
include 'common/header.part';
include 'common/connect.part';
//Page Globals
$country_ID = $_GET["destination"];
$country_name = "";
$active_country = false;
$HST = 0.13;
//Get country name
foreach($db->query("SELECT * FROM country WHERE ID = ".$country_ID."") as $country) {
	$country_name = "".$country["name"];
	if ($country["active"] == 1) {
		$active_country = true;
	}
}
display_header($country_name ." Visa - Travel Visa Processing");
?>

  
  <div class="section main-content" id="top">
    <div class="w-container country-chooser-container">
      <div class="w-form">
        <form class="w-clearfix" id="country_chooser" name="country_chooser" action="visa.php" method="get">
          <select class="w-select selector" id="citizen" name="citizen" data-name="Citizen" required="required">
            <option value="36">I'm a Citizen of...Canada</option>
						<?php
							$stmt = $db->query("SELECT * FROM country");
							while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
								echo "<option value='".$row["ID"]."'>". $row['name'] ."</option>";
							}
						?>
          </select>
          <select class="w-select selector" id="destination" name="destination" data-name="Destination" required="required">
            <option value="">I'm Going to...</option>
            <?php
							$stmt = $db->query("SELECT * FROM country");
							while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
								echo "<option value='".$row["ID"]."'>". $row['name'] ."</option>";
							}
			 			?>
          </select>
          <input class="w-button button hollow" type="submit" value="Lets Go!" data-wait="Please wait...">
        </form>
      </div>
    </div>
    <div class="w-container alert-container">
      <div class="alert"><strong>ALERT</strong> &nbsp;&nbsp;&nbsp;Our office will be closed on July 1st in observance of Canada Day.</div>
    </div>
    <div class="w-container visa-info">
      <?php
				//display the flag
      	echo '<img class="flag-img" src="images/flags/'.$country["ID"].'.png" alt="Flag">';
      ?>
      <h1 class="country-name"><?php echo $country_name ?> Visa</h1>
      <div class="w-tabs" data-duration-in="300" data-duration-out="100">
        <div class="w-tab-menu">
        	<?php
        		foreach($db->query("SELECT * FROM visa WHERE country_ID = ".$country_ID."") as $visa) {
							echo '<a class="w-tab-link w--current w-inline-block visa-type" data-w-tab="'.$visa["type"].'">';
							echo '<div>'.$visa["type"].'</div></a>';
						}
        	?>
        </div>
        <div class="w-tab-content visa-reqs">
        
        	<?php
        		if ($active_country) {
							foreach($db->query("SELECT * FROM visa WHERE country_ID = ".$country_ID."") as $visa) {
								//Tab menu box
								echo '<div class="w-tab-pane w--tab-active" data-w-tab="'.$visa["type"].'">';
								//Title
								echo '<h3>'.$country_name.' '.$visa["type"].' Requirements</h3>';
								//Apply online button
								echo '<div class="visa-applications"><a class="button apply-online" href="apply.php?visa='.$visa["ID"].'">Apply Online</a></div>';
								//Supporting Docs reminder
								echo '<div class="visa-supporting-documents-reminder">and send in the following Supporting Documents to Travel Visa Processing:</div>';
								//Visa requirements
								echo $visa["reqs"];
								
								//Non-Canadian Citizen Message
								if ($_GET["citizen"] != "36") {
									echo "<br>Non-Canadian citizens must provide a copy of their Permanent Resident Card, valid Canadian Visa or valid Visa to the country of final destination (after ".$country_name.").";
								}
								
								//Pricing table
								echo "<p><br><br><h3>".$country_name." ".$visa["type"]." Pricing <h3></p>";
								echo '<div class="datagrid_visa">';
								echo '	<table>';
								echo '		<thead><tr><th>Visa Type</th><th>Validity</th><th>Processing</th><th>Embassy Fee</th><th>Service Fee</th><th>HST</th><th>Total</th></tr></thead>';
								echo '		<tbody>';
								$stmt_entry = $db->prepare("SELECT * FROM entry WHERE visa_ID = ?");
								$stmt_entry->execute( array ($visa["ID"]) );
								$stylecount = 0;
								while ($entry = $stmt_entry->fetch(PDO::FETCH_ASSOC)) {
									if ($stylecount % 2 == 0) {  // alternating row styles
										echo '<tr class="alt">';
									} else {
										echo '<tr>';
									}
									$stylecount++;
									echo '<td>'.$entry["type"].'</td>';
									echo '<td> up to '.$entry["validity"].' days</td>';
									echo '<td>'.$entry["processingTime"].' business days</td>';
									echo '<td>$'.$entry["embassyFee"].'</td>';
									echo '<td>$'.$entry["TVPfee"].'</td>';
									$tax = ($entry["TVPfee"]) * $HST;
									echo '<td>$'.number_format($tax,2).'</td>';
									echo '<td>$'.number_format(($entry["embassyFee"] + $entry["TVPfee"] + $tax),2).'</td></tr>';
								}		
								echo '		</tbody>';
								echo '	</table>';
								echo '</div>'; //Table closing div
								echo '<div class="disclaimer"> * Max stay: 90 days <br> * Processing times start the day following submission, and only reflect processing times under normal conditions. </div>';
								echo "</div>"; //Visa Type Tab closing div
							}
						} else { //inactive country
							echo "".non_service_message($country_name);
						}
        	?>
        </div>
      </div>
    </div>
    
    <div class="w-container steps-container">
      <div class="w-row">
        <div class="w-col w-col-3 w-clearfix step-column">
          <div class="step-number">1</div>
          <div class="step-header-text">Fill out Application</div>
          <div class="step-text">Apply online and download and fill out the Visa Form for the type of Visa you require.</div>
        </div>
        <div class="w-col w-col-3 w-clearfix step-column">
          <div class="step-number">2</div>
          <div class="step-header-text">Bring in Documents</div>
          <div class="step-text">Bring in or Mail your supporting documents and Visa Form.</div>
        </div>
        <div class="w-col w-col-3 w-clearfix step-column">
          <div class="step-number">3</div>
          <div class="step-header-text">Receive Your Visa</div>
          <div class="step-text">Choose to get your new Visa mailed to you, or come in to pick-up.</div>
        </div>
        <div class="w-col w-col-3">
          <div class="legal-blurb">Travel Visa Processing is a private visa agency, not affiliated with the government. Travel Visa Processing provides expediting services, including error-proofing and hand delivery to the Consulate&nbsp;for travel visas, and charges a service
            fee. Travel visas may also be obtained directly from Consulates at a lower cost of the respective government fee.</div>
        </div>
      </div>
    </div>
  </div>
  
<?php include 'common/footer.part'; ?>