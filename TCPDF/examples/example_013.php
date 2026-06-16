<?php




















require_once('tcpdf_include.php');


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 013');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');


$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 013', PDF_HEADER_STRING);


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

$pdf->Write(0, 'Graphic Transformations', '', 0, 'C', 1, 0, false, false, 0);


$pdf->SetFont('helvetica', '', 10);


$pdf->SetDrawColor(200);
$pdf->SetTextColor(200);
$pdf->Rect(50, 70, 40, 10, 'D');
$pdf->Text(50, 66, 'Scale');
$pdf->SetDrawColor(0);
$pdf->SetTextColor(0);

$pdf->StartTransform();

$pdf->ScaleXY(150, 50, 80);
$pdf->Rect(50, 70, 40, 10, 'D');
$pdf->Text(50, 66, 'Scale');

$pdf->StopTransform();


$pdf->SetDrawColor(200);
$pdf->SetTextColor(200);
$pdf->Rect(125, 70, 40, 10, 'D');
$pdf->Text(125, 66, 'Translate');
$pdf->SetDrawColor(0);
$pdf->SetTextColor(0);

$pdf->StartTransform();

$pdf->Translate(7, 5);
$pdf->Rect(125, 70, 40, 10, 'D');
$pdf->Text(125, 66, 'Translate');

$pdf->StopTransform();


$pdf->SetDrawColor(200);
$pdf->SetTextColor(200);
$pdf->Rect(70, 100, 40, 10, 'D');
$pdf->Text(70, 96, 'Rotate');
$pdf->SetDrawColor(0);
$pdf->SetTextColor(0);

$pdf->StartTransform();

$pdf->Rotate(20, 70, 110);
$pdf->Rect(70, 100, 40, 10, 'D');
$pdf->Text(70, 96, 'Rotate');

$pdf->StopTransform();


$pdf->SetDrawColor(200);
$pdf->SetTextColor(200);
$pdf->Rect(125, 100, 40, 10, 'D');
$pdf->Text(125, 96, 'Skew');
$pdf->SetDrawColor(0);
$pdf->SetTextColor(0);

$pdf->StartTransform();

$pdf->SkewX(30, 125, 110);
$pdf->Rect(125, 100, 40, 10, 'D');
$pdf->Text(125, 96, 'Skew');

$pdf->StopTransform();


$pdf->SetDrawColor(200);
$pdf->SetTextColor(200);
$pdf->Rect(70, 130, 40, 10, 'D');
$pdf->Text(70, 126, 'MirrorH');
$pdf->SetDrawColor(0);
$pdf->SetTextColor(0);

$pdf->StartTransform();

$pdf->MirrorH(70);
$pdf->Rect(70, 130, 40, 10, 'D');
$pdf->Text(70, 126, 'MirrorH');

$pdf->StopTransform();


$pdf->SetDrawColor(200);
$pdf->SetTextColor(200);
$pdf->Rect(125, 130, 40, 10, 'D');
$pdf->Text(125, 126, 'MirrorV');
$pdf->SetDrawColor(0);
$pdf->SetTextColor(0);

$pdf->StartTransform();

$pdf->MirrorV(140);
$pdf->Rect(125, 130, 40, 10, 'D');
$pdf->Text(125, 126, 'MirrorV');

$pdf->StopTransform();


$pdf->SetDrawColor(200);
$pdf->SetTextColor(200);
$pdf->Rect(70, 160, 40, 10, 'D');
$pdf->Text(70, 156, 'MirrorP');
$pdf->SetDrawColor(0);
$pdf->SetTextColor(0);

$pdf->StartTransform();

$pdf->MirrorP(70,170);
$pdf->Rect(70, 160, 40, 10, 'D');
$pdf->Text(70, 156, 'MirrorP');

$pdf->StopTransform();


$angle=-20;
$px=120;
$py=170;



$pdf->SetDrawColor(200);
$pdf->Line($px-1,$py-1,$px+1,$py+1);
$pdf->Line($px-1,$py+1,$px+1,$py-1);
$pdf->StartTransform();
$pdf->Rotate($angle, $px, $py);
$pdf->Line($px-5, $py, $px+60, $py);
$pdf->StopTransform();

$pdf->SetDrawColor(200);
$pdf->SetTextColor(200);
$pdf->Rect(125, 160, 40, 10, 'D');
$pdf->Text(125, 156, 'MirrorL');
$pdf->SetDrawColor(0);
$pdf->SetTextColor(0);

$pdf->StartTransform();

$pdf->MirrorL($angle, $px, $py);
$pdf->Rect(125, 160, 40, 10, 'D');
$pdf->Text(125, 156, 'MirrorL');

$pdf->StopTransform();




$pdf->Output('example_013.pdf', 'I');




