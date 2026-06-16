<?php




















require_once('tcpdf_include.php');


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 044');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');


$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 044', PDF_HEADER_STRING);


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




$pdf->SetFont('helvetica', 'B', 40);


$pdf->AddPage();
$pdf->Cell(0, 10, 'PAGE: A', 0, 1, 'L');


$pdf->Ln(10);


$pdf->SetFont('times', 'I', 16);
$txt = 'TCPDF allows you to Copy, Move and Delete pages.';
$pdf->Write(0, $txt, '', 0, 'L', true, 0, false, false, 0);

$pdf->SetFont('helvetica', 'B', 40);

$pdf->AddPage();
$pdf->Cell(0, 10, 'PAGE: B', 0, 1, 'L');

$pdf->AddPage();
$pdf->Cell(0, 10, 'PAGE: D', 0, 1, 'L');

$pdf->AddPage();
$pdf->Cell(0, 10, 'PAGE: E', 0, 1, 'L');

$pdf->AddPage();
$pdf->Cell(0, 10, 'PAGE: E-2', 0, 1, 'L');

$pdf->AddPage();
$pdf->Cell(0, 10, 'PAGE: F', 0, 1, 'L');

$pdf->AddPage();
$pdf->Cell(0, 10, 'PAGE: C', 0, 1, 'L');

$pdf->AddPage();
$pdf->Cell(0, 10, 'PAGE: G', 0, 1, 'L');


$pdf->movePage(7, 3);


$pdf->deletePage(6);

$pdf->AddPage();
$pdf->Cell(0, 10, 'PAGE: H', 0, 1, 'L');


$pdf->copyPage(2);






$pdf->Output('example_044.pdf', 'I');




