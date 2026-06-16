<?php




















require_once('tcpdf_include.php');


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 025');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');


$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 025', PDF_HEADER_STRING);


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




$pdf->SetFont('helvetica', '', 12);


$pdf->AddPage();

$txt = 'You can set the transparency of PDF objects using the setAlpha() method.';
$pdf->Write(0, $txt, '', 0, '', true, 0, false, false, 0);



$pdf->SetLineWidth(2);


$pdf->SetFillColor(255, 0, 0);
$pdf->SetDrawColor(127, 0, 0);
$pdf->Rect(30, 40, 60, 60, 'DF');


$pdf->SetAlpha(0.5);


$pdf->SetFillColor(0, 255, 0);
$pdf->SetDrawColor(0, 127, 0);
$pdf->Rect(50, 60, 60, 60, 'DF');


$pdf->SetFillColor(0, 0, 255);
$pdf->SetDrawColor(0, 0, 127);
$pdf->Rect(70, 80, 60, 60, 'DF');


$pdf->Image('images/image_demo.jpg', 90, 100, 60, 60, '', 'http://www.tcpdf.org', '', true, 72);


$pdf->SetAlpha(1);




$pdf->Output('example_025.pdf', 'I');




