<?php




















require_once('tcpdf_include.php');


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 062');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');


$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 062', PDF_HEADER_STRING);


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




$pdf->SetFont('helvetica', 'B', 20);


$pdf->AddPage();

$pdf->Write(0, 'XObject Templates', '', 0, 'C', 1, 0, false, false, 0);





$template_id = $pdf->startTemplate(60, 60, true);




$pdf->StartTransform();


$pdf->StarPolygon(30, 30, 29, 10, 3, 0, 1, 'CNZ');


$pdf->Image('images/image_demo.jpg', 0, 0, 60, 60, '', '', '', true, 72, '', false, false, 0, false, false, false);


$pdf->StopTransform();

$pdf->SetXY(0, 0);

$pdf->SetFont('times', '', 40);

$pdf->SetTextColor(255, 0, 0);


$pdf->Cell(60, 60, 'Template', 0, 0, 'C', false, '', 0, false, 'T', 'M');



$pdf->endTemplate();




$pdf->SetAlpha(0.4);
$pdf->printTemplate($template_id, 15, 50, 20, 20, '', '', false);

$pdf->SetAlpha(0.6);
$pdf->printTemplate($template_id, 27, 62, 40, 40, '', '', false);

$pdf->SetAlpha(0.8);
$pdf->printTemplate($template_id, 55, 85, 60, 60, '', '', false);

$pdf->SetAlpha(1);
$pdf->printTemplate($template_id, 95, 125, 80, 80, '', '', false);




$pdf->Output('example_062.pdf', 'I');




