<?php
include 'common/header.part';
include 'common/connect.part';
display_header($country_name ." Visa - Travel Visa Processing");
?>
  
  <div class="section main-content">
    <div class="w-container">
      <h1 class="page-heading">Choose a Destination</h1>
      <div class="page-content">
        
        <div class="datagrid">
        		<table>
							<tbody>
								<?php 
									$alphabet = range('A', 'Z');
									$stmt_country = $db->query("SELECT * FROM country");
									$allCountries = $stmt_country->fetchAll(PDO::FETCH_ASSOC);
									$stylecount = 0;
									
									foreach ($alphabet as $letter) {
										$totCols = 0;
										
										$countriesByLetter = array();
										foreach ($allCountries as $country) {
											if ($country["name"][0] == $letter) {
												$countriesByLetter[] = $country;
											}
										}
										
										$numCountries = count($countriesByLetter);
										if ($numCountries > 0) {
											$stylecount++;
											$COLUMNS = 4;
											$maxPerCol = (int)ceil($numCountries / $COLUMNS);
											$count = 0;
											if ($stylecount % 2 == 0) {  // alternating row styles
												echo '<tr class="alt">';
											} else {
												echo '<tr>';
											}
											echo '<td>';
											$column_count = 0;
											foreach ($countriesByLetter as $country) {
												$totCols++;
												$count++;
												echo '<a href="visa.php?destination='.$country["ID"].'">'.$country["name"].'</a><br />';
												if ( ($count >= $maxPerCol) AND ($COLUMNS > 1) ) {
													echo '</td><td>';
													$count = 0;
													$COLUMNS--;
													$numCountries -= $maxPerCol;
													$maxPerCol = (int)ceil($numCountries / $COLUMNS);
												}
											}
											echo '</td>';
											
											while ($totCols < $COLUMNS) { //filling in rest of the columns in this row
												echo '<td></td>';
												$totCols++;
											}
											echo '</tr>';
										}
									}
								?>
							</tbody>
					</table>
				</div>
      </div>
    </div>
  </div>
  
<?php include 'common/footer.part'; ?>