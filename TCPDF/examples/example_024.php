<?php




















require_once('tcpdf_include.php');


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 024');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');


$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 024', PDF_HEADER_STRING);


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




$pdf->SetFont('times', '', 18);


$pdf->AddPage();



$txt = 'You can limit the visibility of PDF objects to screen or printer by using the setVisibility() method.
Check the print preview of this document to display the alternative text.';

$pdf->Write(0, $txt, '', 0, '', true, 0, false, false, 0);


$pdf->SetFontSize(40);


$pdf->SetTextColor(0,63,127);


$pdf->setVisibility('screen');


$pdf->Write(0, '[This line is for display]', '', 0, 'C', true, 0, false, false, 0);


$pdf->setVisibility('print');


$pdf->SetTextColor(127,0,0);


$pdf->Write(0, '[This line is for printout]', '', 0, 'C', true, 0, false, false, 0);


$pdf->setVisibility('all');






$pdf->startLayer('layer1', true, true);


$pdf->SetFontSize(18);


$pdf->SetTextColor(0,127,0);

$txt = 'Using the startLayer() method you can group PDF objects into layers.
This text is on "layer1".';


$pdf->Write(0, $txt, '', 0, 'L', true, 0, false, false, 0);


$pdf->endLayer();




$pdf->Output('example_024.pdf', 'D');




