<?php




















require_once('tcpdf_include.php');


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 063');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');


$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 063', PDF_HEADER_STRING);


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




$pdf->SetFont('helvetica', 'B', 16);


$pdf->AddPage();

$pdf->Write(0, 'Example of Text Stretching and Spacing (tracking)', '', 0, 'L', true, 0, false, false, 0);
$pdf->Ln(5);



$fonts = array('times', 'dejavuserif');
$alignments = array('L' => 'LEFT', 'C' => 'CENTER', 'R' => 'RIGHT', 'J' => 'JUSTIFY');



foreach ($fonts as $fkey => $font) {
	$pdf->SetFont($font, '', 14);
	foreach ($alignments as $align_mode => $align_name) {
		for ($stretching = 90; $stretching <= 110; $stretching += 10) {
			for ($spacing = -0.254; $spacing <= 0.254; $spacing += 0.254) {
				$pdf->setFontStretching($stretching);
				$pdf->setFontSpacing($spacing);
				$txt = $align_name.' | Stretching = '.$stretching.'% | Spacing = '.sprintf('%+.3F', $spacing).'mm';
				$pdf->Cell(0, 0, $txt, 1, 1, $align_mode);
			}
		}
	}
	$pdf->AddPage();
}



foreach ($fonts as $fkey => $font) {
	$pdf->SetFont($font, '', 11);
	foreach ($alignments as $align_mode => $align_name) {
		for ($stretching = 90; $stretching <= 110; $stretching += 10) {
			for ($spacing = -0.254; $spacing <= 0.254; $spacing += 0.254) {
				$html = '<span style="font-stretch:'.$stretching.'%;letter-spacing:'.$spacing.'mm;"><span style="color:red;">'.$align_name.'</span> | <span style="color:green;">Stretching = '.$stretching.'%</span> | <span style="color:blue;">Spacing = '.sprintf('%+.3F', $spacing).'mm</span><br />Lorem ipsum dolor sit amet, consectetur adipiscing elit. In sed imperdiet lectus. Phasellus quis velit velit, non condimentum quam. Sed neque urna, ultrices ac volutpat vel, laoreet vitae augue. Sed vel velit erat. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.</span>';
				$pdf->writeHTMLCell(0, 0, '', '', $html, 1, 1, false, true, $align_mode, false);
			}
		}
		if (!(($fkey == 1) AND ($align_mode == 'J'))) {
			$pdf->AddPage();
		}
	}
}



$pdf->setFontStretching(100);


$pdf->setFontSpacing(0);




$pdf->Output('example_063.pdf', 'I');




