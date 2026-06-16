<?php




















require_once('tcpdf_include.php');


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 030');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');


$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 030', PDF_HEADER_STRING);


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

$pdf->Cell(0, 0, 'TCPDF Gradients', 0, 1, 'C', 0, '', 0, false, 'T', 'M');


$red = array(255, 0, 0);
$blue = array(0, 0, 200);
$yellow = array(255, 255, 0);
$green = array(0, 255, 0);
$white = array(255);
$black = array(0);


$coords = array(0, 0, 1, 0);


$pdf->LinearGradient(20, 45, 80, 80, $red, $blue, $coords);


$pdf->Text(20, 130, 'LinearGradient()');


$coords = array(0.5, 0.5, 1, 1, 1.2);


$pdf->RadialGradient(110, 45, 80, 80, $white, $black, $coords);


$pdf->Text(110, 130, 'RadialGradient()');


$pdf->CoonsPatchMesh(20, 155, 80, 80, $yellow, $blue, $green, $red);


$pdf->Text(20, 240, 'CoonsPatchMesh()');


$coords = array(
	0.00,0.00, 0.33,0.20,             
	0.67,0.00, 1.00,0.00, 0.80,0.33,  
	0.80,0.67, 1.00,1.00, 0.67,0.80,  
	0.33,1.00, 0.00,1.00, 0.20,0.67,  
	0.00,0.33);                       
$coords_min = 0;   
$coords_max = 1;   


$pdf->CoonsPatchMesh(110, 155, 80, 80, $yellow, $blue, $green, $red, $coords, $coords_min, $coords_max);


$pdf->Text(110, 240, 'CoonsPatchMesh()');


$pdf->AddPage();


$patch_array[0]['f'] = 0;
$patch_array[0]['points'] = array(
	0.00,0.00, 0.33,0.00,
	0.67,0.00, 1.00,0.00, 1.00,0.33,
	0.8,0.67, 1.00,1.00, 0.67,0.8,
	0.33,1.80, 0.00,1.00, 0.00,0.67,
	0.00,0.33);
$patch_array[0]['colors'][0] = array('r' => 255, 'g' => 255, 'b' => 0);
$patch_array[0]['colors'][1] = array('r' => 0, 'g' => 0, 'b' => 255);
$patch_array[0]['colors'][2] = array('r' => 0, 'g' => 255,'b' => 0);
$patch_array[0]['colors'][3] = array('r' => 255, 'g' => 0,'b' => 0);


$patch_array[1]['f'] = 2;
$patch_array[1]['points'] = array(
	0.00,1.33,
	0.00,1.67, 0.00,2.00, 0.33,2.00,
	0.67,2.00, 1.00,2.00, 1.00,1.67,
	1.5,1.33);
$patch_array[1]['colors'][0]=array('r' => 0, 'g' => 0, 'b' => 0);
$patch_array[1]['colors'][1]=array('r' => 255, 'g' => 0, 'b' => 255);


$patch_array[2]['f'] = 3;
$patch_array[2]['points'] = array(
	1.33,0.80,
	1.67,1.50, 2.00,1.00, 2.00,1.33,
	2.00,1.67, 2.00,2.00, 1.67,2.00,
	1.33,2.00);
$patch_array[2]['colors'][0] = array('r' => 0, 'g' => 255, 'b' => 255);
$patch_array[2]['colors'][1] = array('r' => 0, 'g' => 0, 'b' => 0);


$patch_array[3]['f'] = 1;
$patch_array[3]['points'] = array(
	2.00,0.67,
	2.00,0.33, 2.00,0.00, 1.67,0.00,
	1.33,0.00, 1.00,0.00, 1.00,0.33,
	0.8,0.67);
$patch_array[3]['colors'][0] = array('r' => 0, 'g' => 0, 'b' => 0);
$patch_array[3]['colors'][1] = array('r' => 0, 'g' => 0, 'b' => 255);

$coords_min = 0;
$coords_max = 2;

$pdf->CoonsPatchMesh(10, 45, 190, 200, '', '', '', '', $patch_array, $coords_min, $coords_max);


$pdf->Text(10, 250, 'CoonsPatchMesh()');




$pdf->Output('example_030.pdf', 'D');




