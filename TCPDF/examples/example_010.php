<?php




















require_once('tcpdf_include.php');



class MC_TCPDF extends TCPDF {

	
	public function PrintChapter($num, $title, $file, $mode=false) {
		
		$this->AddPage();
		
		$this->resetColumns();
		
		$this->ChapterTitle($num, $title);
		
		$this->setEqualColumns(3, 57);
		
		$this->ChapterBody($file, $mode);
	}

	
	public function ChapterTitle($num, $title) {
		$this->SetFont('helvetica', '', 14);
		$this->SetFillColor(200, 220, 255);
		$this->Cell(180, 6, 'Chapter '.$num.' : '.$title, 0, 1, '', 1);
		$this->Ln(4);
	}

	
	public function ChapterBody($file, $mode=false) {
		$this->selectColumn();
		
		$content = file_get_contents($file, false);
		
		$this->SetFont('times', '', 9);
		$this->SetTextColor(50, 50, 50);
		
		if ($mode) {
			
			$this->writeHTML($content, true, false, true, false, 'J');
		} else {
			
			$this->Write(0, $content, '', 0, 'J', true, 0, false, true, 0);
		}
		$this->Ln();
	}
} 





$pdf = new MC_TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 010');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');


$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 010', PDF_HEADER_STRING);


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




$pdf->PrintChapter(1, 'LOREM IPSUM [TEXT]', 'data/chapter_demo_1.txt', false);


$pdf->PrintChapter(2, 'LOREM IPSUM [HTML]', 'data/chapter_demo_2.txt', true);




$pdf->Output('example_010.pdf', 'I');




