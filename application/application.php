<?php
session_start();
if (!isset($_SESSION["orderNum"])) { die("Incorrect link - taking you back to home page."); }

// CONSTANTS
$HST = 0.13;
// HTML style
$html = '<style type="text/css">
	table.tableizer-table {
	border: 1px solid #FFF; 
	font-family: Arial, Helvetica, sans-serif
	font-size: 12px;
} 
.tableizer-table td {
	padding: 4px;
	margin: 4px;
	height: 12px;
	border: 1px solid #999;
}
.tableizer-table th {
	color: #104E8B;
	font-weight: bold;
	font-size: 15px;
	border: 1px solid #999;
	height: 25px;
}
.tableizer-table li {
	list-style-type: none;
}
</style>';

// DB Connection
require_once('../common/connect.part');

// Include the main TCPDF library (search for installation path).
require_once('TCPDF/tcpdf_import.php');
require_once('FPDI/fpdi.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends FPDI {

    //Page header
    public function Header() {
        // Logo
        $image_file = K_PATH_IMAGES.'TVPlogo.jpg';
        $this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        // Title
        $this->Cell(0, 22, '  TRAVEL VISA PROCESSING', 0, 1, '', 0, '', 0, false, 'M', 'B');
    }
}

// ---------------------------------------------------------//
//        					PDF document creation									--//
// ---------------------------------------------------------//

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('TravelVisaProcessing.ca');
$pdf->SetTitle('');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('Travel, Visa, Supporting Documents, invoice, checklist, application');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------//
//        									INVOICE												--//
// ---------------------------------------------------------//

// *** Query DB for Order Details *** //
$country_name = $visa_type = "";
$entry_type = $processingTime = $validity = "";
$price = $tax_rate = 0;
$country_ID = $visa_ID = $entry_ID = 0;
$total = 0;
$reqs = "";
$form_type = $filename = "";
$service_fee = 0;
$order_ID = 0;
$customer_name = "";
$visa_form_type = "";
$order_number = $_SESSION["orderNum"];

//Get country
foreach($db->query("SELECT * FROM orders WHERE orderNum = '".$order_number."'") as $order) {
	$price = $order['price'];
	$tax_rate = $order['tax_rate'];
	
	$country_ID = $order['country_ID'];
	$visa_ID = $order["visa_ID"];
	$entry_ID = $order["entry_ID"];
	
	$customer_name = $order["firstName"] ." ";
	if (isset($order["middleName"])) {
		$customer_name .= $order["middleName"] . " ";
	}
	$customer_name .= $order["lastName"];
	
	$email = $order['email'];
	
	$order_ID = $order["ID"];
	
	$order_num = $order["orderNum"];
}

foreach($db->query("SELECT * FROM country WHERE ID = '".$country_ID."'") as $country) {
	$country_name = $country["name"];	
}

foreach($db->query("SELECT * FROM visa WHERE ID = '".$visa_ID."'") as $visa) {
	$visa_type = $visa["type"];
	$reqs = $visa["reqs"];
	$visa_form_type = $visa["form_type"];
	$offline_filename = $visa["offline_filename"];
}

foreach($db->query("SELECT * FROM entry WHERE ID = '".$entry_ID."'") as $entry) {
	$entry_type = $entry["type"];
	$processingTime = $entry["processingTime"];
	$validity = $entry["validity"];
	$service_fee = $entry["TVPfee"];
}

$stmt = $db->prepare("SELECT * FROM add_charges WHERE order_ID = '".$order_ID."'");
$stmt->execute();
$charges = $stmt->fetchAll();

//===== DB QUERY END ======//

// set font
$pdf->SetFont('helvetica', '', 12);

// add a page
$pdf->AddPage();


$html .= '
<p> </p>
<h2>INVOICE: '.$country_name .' '. $visa_type .'</h2>
<p> </p>

<table >
	<tr><td style="width:40%"><b>Travel Visa Processing</b> <br>www.TravelVisaProcessing.ca <br>123 Somewhere Street Unit 201 <br>Scarborough, Ontario <br>M1B 3B3</td> <td style="width:20%"></td> <td style="width:40%"><b>Order #:'.$order_num.'</b> <br>'.$customer_name.'<br>Email: '.$email.' </td> </tr>
</table>
<p> </p>
<table class="tableizer-table">
<tr class="tableizer-firstrow"><th style="width:55%">Description</th><th style="width:15%">Price</th><th style="width:15%">Tax (HST)</th><th style="width:15%">Subtotal</th></tr>
 <tr><td><p>'.$entry_type.' - Up to '.$validity." day stay - Processing Time: ". $processingTime.' business days </p>';

$html .= '</td><td>$'.number_format($price, 2).'</td><td>$'.number_format($service_fee*($tax_rate-1), 2).'</td><td>$'.number_format($price + $service_fee*$HST, 2).'</td></tr>';
$total = $price + $service_fee*$HST;
$rows = 0;
$max_rows = 8;
foreach ($charges as $charge) {
	$html .= '<tr><td><p>'.$charge["charge"].'</p></td><td>$'.number_format($charge["price"], 2).'</td><td>$'.number_format($charge["price"]*($tax_rate-1), 2).'</td><td>$'.number_format($charge["price"] + $charge["price"]*$HST, 2).'</td></tr>';
	$total += $charge["price"] + $charge["price"]*$HST;
	$rows++;
}
while ($rows <= $max_rows) {
	$html .= '<tr><td></td><td></td><td></td><td></td></tr>';
	$rows++;
}

$html .= '<tr><td style="border:0px solid #fff">&nbsp;</td><td style="border:0px solid #fff;">&nbsp;</td><td style="border:0px solid #fff; border-right:1px solid #999; text-align:center;">Total:</td><td>$'.number_format($total, 2).'</td></tr></table>';

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->writeHTML('<p></p><p></p><p></p><p></p><p>Thank you for your business!</p><p>
									<p style="font-size:7px"; >By using the services of TravelVisaProcessing.ca, I authorize you to handle my personal information and my passport/other documents and particulars to a foreign diplomatic/consular missions in Canada & US for the purpose of acquiring a visa or other consular documents as well as to receive information about my application from foreign diplomatic/consular missions and sign on my behalf whenever and wherever it is required for the purpose of obtaining visa. By signing this document, I accept in whole the following terms, conditions and limitations: TravelVisaProcessing.ca does not issue visas and can not and does not guarantee a visa will be issued or issued by a diplomatic/consular office, as this is the sole prerogative of the foreign government. Issuance of visa, processing timing, visa type, its length of validity, number of entries and other details are determined exclusively by the embassy/consulate on case by case basis. Once the application file has been submitted to the embassy/consulate, no changes of any kind, including the trip dates and processing timing, are possible or allowed. Processing timings and requirements mentioned on TravelVisaProcessing.ca web site is a general guide-line. Embassy/consulate has the right to request additional documentation and increase the processing timing if required. TravelVisaProcessing.ca does not bear liability for the safety or security of your passport/document once the passport has entered the diplomatic/consular or other authority grounds or passed into the control of a courier company for return delivery. TravelVisaProcessing.ca is not liable for any stolen or lost passports, while out of our hands, and holds no liability for late delivery of passports and visas, and TravelVisaProcessing.ca does not bear any financial, legal or other obligations whatsoever for client travel bookings or other purchases, down payments, or any kind of travel or other arrangements that were done prior to the issuance of visa which may be affected by processing times, visa details or denial of visa. TravelVisaProcessing.ca does not bear any financial, or otherwise, responsibility from issues and losses arising from errors and improper issuance of visas by the consulates and does not compensate for travel expenses arising from any of the above. Even when visa is issued, a traveler may be denied entry since in each country the local immigration officials make the final decision to grant the entry. No refund is possible once the documents submitted to foreign diplomatic/consular mission for processing. I understand and fully accept the above mentioned. BY SIGNING BELOW YOU AGREE TO THE ABOVE CONDITIONS & CHARGES PROCESSED, INCLUDING 2.5% CONVENIENCE FEE ON THE TOTAL IF PAYMENT IS BY CREDIT CARD. UNSIGNED FORMS WILL NOT BE PROCESSED.</p></p>', 
									true, false, true, false, '');
$pdf->Cell(35, 5, 'Signature: ');
$pdf->Cell(129, 8, '', 1);


// ---------------------------------------------------------//
//        		CREDIT CARD AUTHORIZATION FORM							--//
// ---------------------------------------------------------//

$pdf->lastPage();
$pdf->AddPage();

// set default form properties
$pdf->setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 200), 'strokeColor'=>array(255, 128, 128)));

$pdf->SetFont('helvetica', 'B', 18);
$pdf->Ln(10);
$pdf->Cell(0, 5, 'Credit Card Authorization Form', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('helvetica', 'B', 10);

// Authorization Amount
$pdf->Cell(35, 5, 'I authorize Travel Visa Processing to charge my credit card for the amount of $');
$pdf->Ln(10);

$pdf->SetFont('helvetica', 'B', 9);

// Name on CC
$pdf->Cell(35, 7, 'Name on Credit Card:');
$pdf->Cell(129, 7, ' ', 1);
$pdf->SetFont('helvetica', '', 12);
$pdf->TextField('name', 129, 7, array(), array(), 50, 65, false);
$pdf->Ln(8);

// CC Number
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(35, 7, 'Credit Card #: ');
$pdf->Cell(30, 7, ' ', 1);
$pdf->TextField('ccNum1', 30, 7, array('alignment'=>'center'), array(), 50, 73, false);
$pdf->MultiCell(30, 7, ' ', 1, 'J', false, 1, 83, 73);
$pdf->TextField('ccNum2', 30, 7, array('alignment'=>'center'), array(), 83, 73, false);
$pdf->MultiCell(30, 7, ' ', 1, 'J', false, 1, 116, 73);
$pdf->TextField('ccNum3', 30, 7, array('alignment'=>'center'), array(), 116, 73, false);
$pdf->MultiCell(30, 7, ' ', 1, 'J', false, 1, 149, 73);
$pdf->TextField('ccNum4', 30, 7, array('alignment'=>'center'), array(), 149, 73, false);
$pdf->Ln(3);

// Expiry
$pdf->Cell(35, 5, 'Expiry Date: ');
$pdf->MultiCell(30, 7, ' ', 1, 'J', false, 1, 50, 81);
$pdf->SetFont('helvetica', '', 13);
$pdf->ComboBox('expiryMonth', 30, 7, 	array(array('', '-'), 
																						array('01', '01'), 
																						array('02', '02'),
																						array('03', '03'),
																						array('04', '04'),
																						array('05', '05'),
																						array('06', '06'),
																						array('07', '07'),
																						array('08', '08'),
																						array('09', '09'),
																						array('10', '10'),
																						array('11', '11'),
																						array('12', '12')), array(), array(), 50, 81, false);
$pdf->MultiCell(3, 7, "/", 0, 'L', false, 0, 80, 81, true, 4, false, true, 7, "M", true);
$pdf->MultiCell(30, 7, ' ', 1, 'J', false, 1, 83, 81);
$pdf->ComboBox('expiryYear', 30, 7, array(array('', '-'), 
																					array('2014', '2014'),
																					array('2015', '2015'),
																					array('2016', '2016'),
																					array('2017', '2017'),
																					array('2018', '2018'),
																					array('2019', '2019'),
																					array('2020', '2020'),
																					array('2021', '2021'),
																					array('2022', '2022'),
																					array('2023', '2023'),
																					array('2024', '2024')), array(), array(), 83, 81, false);
$pdf->Ln(1);
$pdf->SetFont('helvetica', 'B', 9);

// CC Billing Address
$pdf->Cell(35, 7, 'Billing Address: ');
$pdf->MultiCell(129, 7, ' ', 1, 'J', false, 1, 50, 89);
$pdf->TextField('name', 129, 7, array(), array(), 50, 89, false);
$pdf->MultiCell(129, 7, ' ', 1, 'J', false, 1, 50, 97);
$pdf->TextField('name', 129, 7, array(), array(), 50, 97, false);
$pdf->Ln(1);

// Signature
$pdf->Cell(35, 5, 'Signature: ');
$pdf->Cell(129, 8, '', 1);
$pdf->Ln(10);

// Comments
$pdf->Cell(35, 5, 'Comments: ');
$pdf->MultiCell(129, 21, ' ', 1, 'J', false, 1, 50, 114);
$pdf->TextField('comments', 129, 21, array('multiline'=>true, 'lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 200), 'strokeColor'=>array(255, 128, 128)), array('v'=>'', 'dv'=>''), 50, 114);
$pdf->Ln(19);


// ---------------------------------------------------------//
//        			SUPPORTING DOCUMENTS CHECKLIST						--//
// ---------------------------------------------------------//


$pdf->lastPage();
$pdf->AddPage();
// set font
$pdf->SetFont('helvetica', '', 12);

$html = '<br><h2>'.$_SESSION["country_name"] .' '. $_SESSION["visa_type"] .' Supporting Document Checklist</h2>';
$html .= $reqs;

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');


// ---------------------------------------------------------//
//        							OFFLINE FORM											--//
// ---------------------------------------------------------//

$pdf->lastPage();

if ($visa_form_type == "offline") {
	$pdf->SetPrintHeader(false);
	$pdf->SetPrintFooter(false);

	$pageCount = $pdf->setSourceFile('forms/'.$offline_filename);
	for ($x = 1; $x <= $pageCount; $x++) {
		$pdf->AddPage();
		$tplIdx = $pdf->importPage($x);
		// use the imported page and place it at point 10,10 with a width of 100 mm
		$pdf->useTemplate($tplIdx, 0, 0, 210, 290);
	}
}

// ---------------------------------------------------------//
//        							OUTPUT FILE												--//
// ---------------------------------------------------------//

//Close and output PDF document
$pdf->Output('FirstName_LastName_Rus_Tourist_Visa.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+