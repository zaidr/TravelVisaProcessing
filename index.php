<?php
include 'common/header.part';
include 'common/connect.part';
display_header("Home - Travel Visa Processing");
?>

  <div class="section main" id="top">
    <div class="w-container">
      <h1 class="main-heading cursive">Get out of line and...</h1>
      <h1 class="main-heading main-bold-text">Over the Border</h1>
      <div class="w-row">
        <div class="w-col w-col-6">
          <div class="main-subtitle">Choose where you’re from and where you’d like to go, and let us take care of the details.</div>
        </div>
        <div class="w-col w-col-6">
          <div class="w-form">
            <form class="w-clearfix" id="country_chooser" name="country_chooser" action="visa.php" method="get">
              <select class="w-select" id="citizen" name="citizen" data-name="Citizen" required="required">
                <option value='36'>I'm a citizen of...Canada</option>
								<?php
 									$stmt = $db->query("SELECT * FROM country");
									while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
										echo "<option value='".$row["ID"]."'>". $row['name'] ."</option>";
									}
 								?>
              </select>
              <select class="w-select" id="destination" name="destination" data-name="Destination" required="required">
                <option value="">I'm Going to...</option>
               	<?php
 									$stmt = $db->query("SELECT * FROM country");
									while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
										echo "<option value='".$row["ID"]."'>". $row['name'] ."</option>";
									}
 								?>
              </select>
              <input class="w-button button" type="submit" value="Lets Go!" data-wait="Please wait...">
            </form>
            <div class="w-form-done">
              <p>Thank you! Your submission has been received!</p>
            </div>
            <div class="w-form-fail">
              <p>Oops! Something went wrong while submitting the form :(</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="section press">
    <div class="w-container popular-visas">
      <div class="small-text">MOST REQUESTED VISAS</div>
      <img class="flag-logo" src="images/China.png" width="24" alt="53a07deef36aadd40fe4e99b_China.png">
      <img class="flag-logo" src="images/Brazil.png" width="24" alt="53a07e8500c10e27222ad5ca_Brazil.png">
      <img class="flag-logo" src="images/Russian%20Federation.png" width="24" alt="53a07ed5fe5bebd30fe86f31_Russian%20Federation.png">
      <img class="flag-logo" src="images/India.png" width="24" alt="53a07ee3fe5bebd30fe86f32_India.png">
      <img class="flag-logo" src="images/Australia.png" width="24" alt="53a07fe2f36aadd40fe4e9ba_Australia.png">
      <img class="flag-logo" src="images/Viet%20Nam.png" width="24" alt="53a080c100c10e27222ad611_Viet%20Nam.png">
      <img class="flag-logo" src="images/Ghana.png" width="24" alt="53a08135f36aadd40fe4e9f2_Ghana.png">
      <img class="flag-logo" src="images/Myanmar(Burma).png" width="24" alt="53a0815500c10e27222ad61a_Myanmar(Burma).png">
      <img class="flag-logo" src="images/Cambodja.png" width="24" alt="53a0816f00c10e27222ad61c_Cambodja.png">
      <img class="flag-logo" src="images/Mozambique.png" width="24" alt="53a08182ecd63726223bf5d8_Mozambique.png">
      <div class="fine-print">Travel Visa Processing is a private visa agency, not affiliated with the government. Travel Visa Processing provides expediting services, including error-proofing and hand delivery to the Consulate&nbsp;for travel visas, and charges a service fee.
        Travel visas may also be obtained directly from Consulates at a lower cost of the respective government fee.</div>
    </div>
  </div>
  <div class="section" id="features">
    <div class="w-container">
      <h2 class="main-page-small-heading">Your next Travel Visa is as Easy as 1, 2, 3</h2>
      <div class="section-subtitle">Getting your next travel visa couldn’t be easier...</div>
      <div class="w-row feature-row">
        <div class="w-col w-col-6">
          <img src="images/tvp_screenshot.png" alt="53a0e25fa126a809292cca12_tvp_screenshot.png">
        </div>
        <div class="w-col w-col-6">
          <div class="main-feature-group">
            <img class="feature-icon" src="images/document_icon.png" height="30" alt="53a087eff36aadd40fe4ea77_document_icon.png">
            <h3>1. Complete your Application</h3>
            <p>Use Travel Visa Processing to find the exact application you need for the country you wish to visit, and the Travel Visa requirements that apply.</p>
          </div>
        </div>
      </div>
      <div class="w-row feature-row">
        <div class="w-col w-col-6">
          <div class="main-feature-group">
            <img class="feature-icon" src="images/mail_icon.png" height="30" alt="53a0880dfe5bebd30fe86fcc_mail_icon.png">
            <h3>2. Send your Supporting Documents</h3>
            <p>Mail your supporting documents along with your application to Travel Visa Processing offices, or come in to our location directly. We’ll be happy to see you.</p>
          </div>
        </div>
        <div class="w-col w-col-6">
          <img src="images/stamp.png" alt="53a0e511bee9db7a1abf0202_stamp.png">
        </div>
      </div>
      <div class="w-row feature-row">
        <div class="w-col w-col-6">
          <img src="images/suitcaset.png" alt="53a1184ab0f2e1e423d5715b_suitcaset.png">
        </div>
        <div class="w-col w-col-6">
          <div class="main-feature-group">
            <img class="feature-icon" src="images/travel_icon.png" height="30" alt="53a0c08af114db5c266212ec_travel_icon.png">
            <h3>3. Receive your Travel Visa</h3>
            <p>You have just taken the headache out of Travel Visa applications, and are on your way doing what matters: going on that trip.</p>
          </div>
        </div>
      </div>
      <div class="w-row small-features-row">
        <div class="w-col w-col-3">
          <div class="feature">
            <img class="feature-icon" src="images/icon-stopwatch.png" height="30" alt="53a1205f5f548f9132fd8783_icon-stopwatch.png">
            <h3>Save Time</h3>
            <p>Forget having to look up visa regulations, and studying embassy policies. We will submit the correct documents to ensure your visa on time.</p>
          </div>
        </div>
        <div class="w-col w-col-3">
          <div class="feature">
            <img class="feature-icon" src="images/iconmonstr-sitemap-7-icon.svg" height="30" alt="537d01f2661cf74612a2ef6c_iconmonstr-sitemap-7-icon.svg">
            <h3>Be Sure</h3>
            <p>We at TVP know exactly what forms to fill out and what documents are needed for your visa application. No more wondering, “what if?”</p>
          </div>
        </div>
        <div class="w-col w-col-3">
          <div class="feature">
            <img class="feature-icon" src="images/iconmonstr-save-9-icon.svg" height="30" alt="537d1e073d0e0acf48d07dd5_iconmonstr-save-9-icon.svg">
            <h3>Skip the Lines</h3>
            <p>No more rushing through traffic, fighting to find a parking space, or waiting in line at the embassy. We’ll do that for you.&nbsp;</p>
          </div>
        </div>
        <div class="w-col w-col-3">
          <div class="feature">
            <img class="feature-icon" src="images/simple.png" height="30" alt="53a121596f0a4de623d73504_simple.png">
            <h3>Simplicity</h3>
            <p>We let you avoid the hassle of keeping up with the ever-changing requirements of the embassies, and ensure accuracy every time.&nbsp;</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  
<?php include 'common/footer.part'; ?>