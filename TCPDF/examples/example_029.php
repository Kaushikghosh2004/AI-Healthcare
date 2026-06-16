<?php




















require_once('tcpdf_include.php');


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 029');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');


$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 029', PDF_HEADER_STRING);


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




$preferences = array(
	'HideToolbar' => true,
	'HideMenubar' => true,
	'HideWindowUI' => true,
	'FitWindow' => true,
	'CenterWindow' => true,
	'DisplayDocTitle' => true,
	'NonFullScreenPageMode' => 'UseNone', 
	'ViewArea' => 'CropBox', 
	'ViewClip' => 'CropBox', 
	'PrintArea' => 'CropBox', 
	'PrintClip' => 'CropBox', 
	'PrintScaling' => 'AppDefault', 
	'Duplex' => 'DuplexFlipLongEdge', 
	'PickTrayByPDFSize' => true,
	'PrintPageRange' => array(1,1,2,3),
	'NumCopies' => 2
);




$pdf->setViewerPreferences($preferences);


$pdf->SetFont('times', '', 14);


$pdf->AddPage();


$pdf->Cell(0, 12, 'DISPLAY PREFERENCES - PAGE 1', 1, 1, 'C');

$pdf->Ln(5);

$pdf->Write(0, 'You can use the setViewerPreferences() method to change viewer preferences.', '', 0, 'L', true, 0, false, false, 0);


$pdf->AddPage();

$pdf->Cell(0, 12, 'DISPLAY PREFERENCES - PAGE 2', 0, 0, 'C');


$pdf->AddPage();

$pdf->Cell(0, 12, 'DISPLAY PREFERENCES - PAGE 3', 0, 0, 'C');




$pdf->Output('example_029.pdf', 'D');




