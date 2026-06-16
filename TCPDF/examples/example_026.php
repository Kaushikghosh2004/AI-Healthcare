<?php




















require_once('tcpdf_include.php');


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 026');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');


$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 026', PDF_HEADER_STRING);


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




$pdf->SetFont('helvetica', '', 22);


$pdf->AddPage();


$pdf->SetDrawColor(255,0,0);


$pdf->setTextRenderingMode($stroke=0, $fill=true, $clip=false);
$pdf->Write(0, 'Fill text', '', 0, '', true, 0, false, false, 0);

$pdf->setTextRenderingMode($stroke=0.2, $fill=false, $clip=false);
$pdf->Write(0, 'Stroke text', '', 0, '', true, 0, false, false, 0);

$pdf->setTextRenderingMode($stroke=0.2, $fill=true, $clip=false);
$pdf->Write(0, 'Fill, then stroke text', '', 0, '', true, 0, false, false, 0);

$pdf->setTextRenderingMode($stroke=0, $fill=false, $clip=false);
$pdf->Write(0, 'Neither fill nor stroke text (invisible)', '', 0, '', true, 0, false, false, 0);




$pdf->StartTransform();
$pdf->setTextRenderingMode($stroke=0, $fill=true, $clip=true);
$pdf->Write(0, 'Fill text and add to path for clipping', '', 0, '', true, 0, false, false, 0);
$pdf->Image('images/image_demo.jpg', 15, 65, 170, 10, '', '', '', true, 72);
$pdf->StopTransform();

$pdf->StartTransform();
$pdf->setTextRenderingMode($stroke=0.3, $fill=false, $clip=true);
$pdf->Write(0, 'Stroke text and add to path for clipping', '', 0, '', true, 0, false, false, 0);
$pdf->Image('images/image_demo.jpg', 15, 75, 170, 10, '', '', '', true, 72);
$pdf->StopTransform();

$pdf->StartTransform();
$pdf->setTextRenderingMode($stroke=0.3, $fill=true, $clip=true);
$pdf->Write(0, 'Fill, then stroke text and add to path for clipping', '', 0, '', true, 0, false, false, 0);
$pdf->Image('images/image_demo.jpg', 15, 85, 170, 10, '', '', '', true, 72);
$pdf->StopTransform();

$pdf->StartTransform();
$pdf->setTextRenderingMode($stroke=0, $fill=false, $clip=true);
$pdf->Write(0, 'Add text to path for clipping', '', 0, '', true, 0, false, false, 0);
$pdf->Image('images/image_demo.jpg', 15, 95, 170, 10, '', '', '', true, 72);
$pdf->StopTransform();


$pdf->setTextRenderingMode($stroke=0, $fill=true, $clip=false);










$html  = '<span stroke="0" fill="true">HTML Fill text</span><br />';
$html .= '<span stroke="0.2" fill="false">HTML Stroke text</span><br />';
$html .= '<span stroke="0.2" fill="true" strokecolor="#FF0000" color="#FFFF00">HTML Fill, then stroke text</span><br />';
$html .= '<span stroke="0" fill="false">HTML Neither fill nor stroke text (invisible)</span><br />';


$pdf->writeHTML($html, true, 0, true, 0);




$pdf->Output('example_026.pdf', 'I');




