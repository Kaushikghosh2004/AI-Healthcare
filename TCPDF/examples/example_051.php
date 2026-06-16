<?php




















require_once('tcpdf_include.php');



class MYPDF extends TCPDF {
	
	public function Header() {
		
		$bMargin = $this->getBreakMargin();
		
		$auto_page_break = $this->AutoPageBreak;
		
		$this->SetAutoPageBreak(false, 0);
		
		$img_file = K_PATH_IMAGES.'image_demo.jpg';
		$this->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
		
		$this->SetAutoPageBreak($auto_page_break, $bMargin);
		
		$this->setPageMark();
	}
}


$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 051');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');


$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));


$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);


$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);


$pdf->setPrintFooter(false);


$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);


$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}




$pdf->SetFont('times', '', 48);


$pdf->AddPage();


$html = '<span style="background-color:yellow;color:blue;">&nbsp;PAGE 1&nbsp;</span>
<p stroke="0.2" fill="true" strokecolor="yellow" color="blue" style="font-family:helvetica;font-weight:bold;font-size:26pt;">You can set a full page background.</p>';
$pdf->writeHTML($html, true, false, true, false, '');



$pdf->AddPage();


$html = '<span style="background-color:yellow;color:blue;">&nbsp;PAGE 2&nbsp;</span>';
$pdf->writeHTML($html, true, false, true, false, '');




$pdf->setPrintHeader(false);


$pdf->AddPage();





$bMargin = $pdf->getBreakMargin();

$auto_page_break = $pdf->getAutoPageBreak();

$pdf->SetAutoPageBreak(false, 0);

$img_file = K_PATH_IMAGES.'image_demo.jpg';
$pdf->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);

$pdf->SetAutoPageBreak($auto_page_break, $bMargin);

$pdf->setPageMark();



$html = '<span style="color:white;text-align:center;font-weight:bold;font-size:80pt;">PAGE 3</span>';
$pdf->writeHTML($html, true, false, true, false, '');




$pdf->Output('example_051.pdf', 'I');




