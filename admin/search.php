<?php
	//DB Connection details
	include '../common/connect.part';
	include '../common/header.part';
	display_header("Admin Options - TVP");
?>
  
  <div class="section main-content">
    <div class="w-container">
      <h1 class="page-heading">Admin Options - Search</h1>
      <div class="page-content">
        
        
				<form name="search" id="search" method="post" action="search.php">
					Search By: 	
					<select class="w-select" id="search_by" name="search_by" required="required">
						<option value="order_num" <?php if ($_POST["search_by"] == "order_num") { echo " selected='selected' ";}?> >Order Number</option>
						<option value="name" <?php if ($_POST["search_by"] == "name") { echo " selected='selected' ";}?> >Last Name</option>
					</select>
					<input class="w-input" id="query" type="text" placeholder="Enter search term here" name="query" data-name="query" required="required" value="<?php if (isset($_POST["query"])) { echo $_POST["query"]; }?>">
					<div class="application-submission">
            <input class="w-button application-submit" type="submit" value="Submit">
          </div>
				</form>
				
				
				<?php
        	if (isset($_POST["query"])) {
        		$stmt = 0; //declaring variable so it can be used later
        		if ($_POST["search_by"] == "order_num") {
        			$stmt = $db->query("SELECT * FROM orders WHERE orderNum = '".$_POST["query"]."'");
        		} elseif ($_POST["search_by"] == "name") {
        			$stmt = $db->query("SELECT * FROM orders WHERE lastName LIKE '%".$_POST["query"]."%'");
        		}
        
					echo "<br>".$db->last_row_count()." result(s) found.";
					
					if ($db->last_row_count() != 0) {
						while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
							echo '<div class="result">';
							echo 'Order Number: '.$row["orderNum"].'<br>';
							echo 'Name: '.$row["lastName"].', '.$row["firstName"].' '.$row["middleName"].' <br>';
							echo 'Email: '.$row["email"].'<br>';
							echo '<br> <a class="button download" href="update.php?orderNum='.$row["orderNum"].'"> UPDATE STATUS </a> - <a class="button download" href="print_application.php?orderNum='.$row["orderNum"].'"> PRINT INVOICE </a>';
							echo '</div>';
						}
					}
				
				
					} // close if
				?>
				
				
      </div>
    </div>
  </div>
  
<?php include './common/footer.part'; ?>