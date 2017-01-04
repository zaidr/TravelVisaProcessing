<?php
function non_service_message($country) {
	return '<div class="alert warning"><strong>ALERT</strong> &nbsp;&nbsp;&nbsp;Unfortunately, at this time Travel Visa Processing&nbsp;does not provide service for visas to '.$country.'</div>
		<h3>Your next steps:</h3>
		<p><strong>&middot;&nbsp; Make sure you have a valid passport&nbsp;and sufficient blank visa pages</strong><br />
		<br />
		All travelers will need a passport with validity of&nbsp;at least 90 days following their&nbsp;departure date from '.$country.', though it is&nbsp;strongly recommend that you&nbsp;travel&nbsp;with 6 months validity on your passport at all times. Citizens of Canada can check&nbsp;<a href="http://www.passport.gc.ca/">www.passport.gc.ca</a>&nbsp;for forms and instructions for Canadian passport renewals and new passport applications. Most destinations, including '.$country.', require you to have sufficient unused pages in your passport, for any necessary stamps when arriving&nbsp;and departing. Before any international travel, we suggest you&nbsp;have at least two (2) free pages in the&nbsp;Visas section of your passport. It is not possible for Canadian citizens to add extra passport pages to their passports. Please check&nbsp;<a href="http://www.passport.gc.ca/">www.passport.gc.ca</a>&nbsp;for policy, instructions and forms for&nbsp;Canadian passport renewals.</p>
		<br />
		<p><strong>&middot;&nbsp; Contact your nearest embassy or consulate of '.$country.'</strong><br />
		<br />
		Contact your&nbsp;nearest embassy or consulate of '.$country.', and confirm the required documents needed, as well as&nbsp;their processing time for the visa you require,&nbsp;and whether the consulate accepts applications by mail.</p>
		<br />
		<p><strong>&middot;&nbsp; Confirm if a&nbsp;transit visa is required for any connections on your trip</strong><br />
		<br />
		Check with your airline or travel agency if you have connecting flights overseas as part of your journey to '.$country.'. It may be that countries you pass through going to your&nbsp;destination might require&nbsp;a separate transit visa. You can check the&nbsp;<a href="http://www.travelvisaprocessing.ca/visas.php">visa requirements</a> of that specific country.</p>';
}


function non_service_message_tabs($country) {
	return '<div class="w-tab-pane w--tab-active" data-w-tab="Tourist Visa">'.
		non_service_message($country).
		'</div>
		<div class="w-tab-pane" data-w-tab="Business Visa">'.
		non_service_message($country).
		'</div>';
}

?>