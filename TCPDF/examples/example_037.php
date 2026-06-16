<?php




















require_once('tcpdf_include.php');


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 037');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');


$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 037', PDF_HEADER_STRING);


$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));


$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);


$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);


$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);


$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}




$pdf->SetFont('helvetica', '', 11);


$pdf->AddPage();

$html = '<h1>Example of Spot Colors</h1>Spot colors are single ink colors, rather than colors produced by four (CMYK), six (CMYKOG) or more inks in the printing process (process colors). They can be obtained by special vendors, but often the printers have found their own way of mixing inks to match defined colors.<br /><br />As long as no open standard for spot colours exists, TCPDF users will have to buy a colour book by one of the colour manufacturers and insert the values and names of spot colours directly into the $spotcolor array in <b><em>include/tcpdf_colors.php</em></b> file, or define them using the <b><em>AddSpotColor()</em></b> method.<br /><br />Common industry standard spot colors are:<br /><span color="#008800">ANPA-COLOR, DIC, FOCOLTONE, GCMI, HKS, PANTONE, TOYO, TRUMATCH</span>.';


$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'J', true);


$pdf->SetFont('helvetica', '', 10);





$pdf->AddSpotColor('My TCPDF Dark Green', 100, 50, 80, 45);
$pdf->AddSpotColor('My TCPDF Light Yellow', 0, 0, 55, 0);
$pdf->AddSpotColor('My TCPDF Black', 0, 0, 0, 100);
$pdf->AddSpotColor('My TCPDF Red', 30, 100, 90, 10);
$pdf->AddSpotColor('My TCPDF Green', 100, 30, 100, 0);
$pdf->AddSpotColor('My TCPDF Blue', 100, 60, 10, 5);
$pdf->AddSpotColor('My TCPDF Yellow', 0, 20, 100, 0);







$pdf->SetTextSpotColor('My TCPDF Black', 100);
$pdf->SetDrawSpotColor('My TCPDF Black', 100);

$starty = 100;



$pdf->SetFillSpotColor('My TCPDF Dark Green', 100);
$pdf->Rect(30, $starty, 40, 20, 'DF');
$pdf->Text(73, $starty + 8, 'My TCPDF Dark Green');

$starty += 24;
$pdf->SetFillSpotColor('My TCPDF Light Yellow', 100);
$pdf->Rect(30, $starty, 40, 20, 'DF');
$pdf->Text(73, $starty + 8, 'My TCPDF Light Yellow');




$starty += 24;
$pdf->SetFillSpotColor('My TCPDF Red', 100);
$pdf->Rect(30, $starty, 40, 20, 'DF');
$pdf->Text(73, $starty + 8, 'My TCPDF Red');

$starty += 24;
$pdf->SetFillSpotColor('My TCPDF Green', 100);
$pdf->Rect(30, $starty, 40, 20, 'DF');
$pdf->Text(73, $starty + 8, 'My TCPDF Green');

$starty += 24;
$pdf->SetFillSpotColor('My TCPDF Blue', 100);
$pdf->Rect(30, $starty, 40, 20, 'DF');
$pdf->Text(73, $starty + 8, 'My TCPDF Blue');

$starty += 24;
$pdf->SetFillSpotColor('My TCPDF Yellow', 100);
$pdf->Rect(30, $starty, 40, 20, 'DF');
$pdf->Text(73, $starty + 8, 'My TCPDF Yellow');




$pdf->Output('example_037.pdf', 'I');




