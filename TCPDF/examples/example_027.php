<?php




















require_once('tcpdf_include.php');


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 027');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');


$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 027', PDF_HEADER_STRING);


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




$pdf->setBarcode(date('Y-m-d H:i:s'));


$pdf->SetFont('helvetica', '', 11);


$pdf->AddPage();


$txt = "You can also export 1D barcodes in other formats (PNG, SVG, HTML). Check the examples inside the barcodes directory.\n";
$pdf->MultiCell(70, 50, $txt, 0, 'J', false, 1, 125, 30, true, 0, false, true, 0, 'T', false);
$pdf->SetY(30);



$pdf->SetFont('helvetica', '', 10);


$style = array(
	'position' => '',
	'align' => 'C',
	'stretch' => false,
	'fitwidth' => true,
	'cellfitalign' => '',
	'border' => true,
	'hpadding' => 'auto',
	'vpadding' => 'auto',
	'fgcolor' => array(0,0,0),
	'bgcolor' => false, 
	'text' => true,
	'font' => 'helvetica',
	'fontsize' => 8,
	'stretchtext' => 4
);




$pdf->Cell(0, 0, 'CODE 39 - ANSI MH10.8M-1983 - USD-3 - 3 of 9', 0, 1);
$pdf->write1DBarcode('CODE 39', 'C39', '', '', '', 18, 0.4, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'CODE 39 + CHECKSUM', 0, 1);
$pdf->write1DBarcode('CODE 39 +', 'C39+', '', '', '', 18, 0.4, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'CODE 39 EXTENDED', 0, 1);
$pdf->write1DBarcode('CODE 39 E', 'C39E', '', '', '', 18, 0.4, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'CODE 39 EXTENDED + CHECKSUM', 0, 1);
$pdf->write1DBarcode('CODE 39 E+', 'C39E+', '', '', '', 18, 0.4, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'CODE 93 - USS-93', 0, 1);
$pdf->write1DBarcode('TEST93', 'C93', '', '', '', 18, 0.4, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'Standard 2 of 5', 0, 1);
$pdf->write1DBarcode('1234567', 'S25', '', '', '', 18, 0.4, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'Standard 2 of 5 + CHECKSUM', 0, 1);
$pdf->write1DBarcode('1234567', 'S25+', '', '', '', 18, 0.4, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'Interleaved 2 of 5', 0, 1);
$pdf->write1DBarcode('1234567', 'I25', '', '', '', 18, 0.4, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'Interleaved 2 of 5 + CHECKSUM', 0, 1);
$pdf->write1DBarcode('1234567', 'I25+', '', '', '', 18, 0.4, $style, 'N');



$pdf->AddPage();


$pdf->Cell(0, 0, 'CODE 128 AUTO', 0, 1);
$pdf->write1DBarcode('CODE 128 AUTO', 'C128', '', '', '', 18, 0.4, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'CODE 128 A', 0, 1);
$pdf->write1DBarcode('CODE 128 A', 'C128A', '', '', '', 18, 0.4, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'CODE 128 B', 0, 1);
$pdf->write1DBarcode('CODE 128 B', 'C128B', '', '', '', 18, 0.4, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'CODE 128 C', 0, 1);
$pdf->write1DBarcode('0123456789', 'C128C', '', '', '', 18, 0.4, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'EAN 8', 0, 1);
$pdf->write1DBarcode('1234567', 'EAN8', '', '', '', 18, 0.4, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'EAN 13', 0, 1);
$pdf->write1DBarcode('1234567890128', 'EAN13', '', '', '', 18, 0.4, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'UPC-A', 0, 1);
$pdf->write1DBarcode('12345678901', 'UPCA', '', '', '', 18, 0.4, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'UPC-E', 0, 1);
$pdf->write1DBarcode('04210000526', 'UPCE', '', '', '', 18, 0.4, $style, 'N');


$pdf->AddPage();


$pdf->Cell(0, 0, '5-Digits UPC-Based Extension', 0, 1);
$pdf->write1DBarcode('51234', 'EAN5', '', '', '', 18, 0.4, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, '2-Digits UPC-Based Extension', 0, 1);
$pdf->write1DBarcode('34', 'EAN2', '', '', '', 18, 0.4, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'MSI', 0, 1);
$pdf->write1DBarcode('80523', 'MSI', '', '', '', 18, 0.4, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'MSI + CHECKSUM (module 11)', 0, 1);
$pdf->write1DBarcode('80523', 'MSI+', '', '', '', 18, 0.4, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'CODABAR', 0, 1);
$pdf->write1DBarcode('123456789', 'CODABAR', '', '', '', 18, 0.4, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'CODE 11', 0, 1);
$pdf->write1DBarcode('123-456-789', 'CODE11', '', '', '', 18, 0.4, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'PHARMACODE', 0, 1);
$pdf->write1DBarcode('789', 'PHARMA', '', '', '', 18, 0.4, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'PHARMACODE TWO-TRACKS', 0, 1);
$pdf->write1DBarcode('105', 'PHARMA2T', '', '', '', 18, 2, $style, 'N');


$pdf->AddPage();


$pdf->Cell(0, 0, 'IMB - Intelligent Mail Barcode - Onecode - USPS-B-3200', 0, 1);
$pdf->write1DBarcode('01234567094987654321-01234567891', 'IMB', '', '', '', 15, 0.6, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'POSTNET', 0, 1);
$pdf->write1DBarcode('98000', 'POSTNET', '', '', '', 15, 0.6, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'PLANET', 0, 1);
$pdf->write1DBarcode('98000', 'PLANET', '', '', '', 15, 0.6, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'RMS4CC (Royal Mail 4-state Customer Code) - CBC (Customer Bar Code)', 0, 1);
$pdf->write1DBarcode('SN34RD1A', 'RMS4CC', '', '', '', 15, 0.6, $style, 'N');

$pdf->Ln();


$pdf->Cell(0, 0, 'KIX (Klant index - Customer index)', 0, 1);
$pdf->write1DBarcode('SN34RDX1A', 'KIX', '', '', '', 15, 0.6, $style, 'N');





$pdf->AddPage();


$style['bgcolor'] = array(255,255,240);
$style['fgcolor'] = array(127,0,0);


$style['position'] = 'L';
$pdf->write1DBarcode('LEFT', 'C128A', '', '', '', 15, 0.4, $style, 'N');

$pdf->Ln(2);


$style['position'] = 'C';
$pdf->write1DBarcode('CENTER', 'C128A', '', '', '', 15, 0.4, $style, 'N');

$pdf->Ln(2);


$style['position'] = 'R';
$pdf->write1DBarcode('RIGHT', 'C128A', '', '', '', 15, 0.4, $style, 'N');

$pdf->Ln(2);


$style['fgcolor'] = array(0,127,0);
$style['position'] = '';
$style['stretch'] = false; 
$style['fitwidth'] = false; 


$style['align'] = 'L';
$pdf->write1DBarcode('LEFT', 'C128A', '', '', '', 15, 0.4, $style, 'N');

$pdf->Ln(2);


$style['align'] = 'C';
$pdf->write1DBarcode('CENTER', 'C128A', '', '', '', 15, 0.4, $style, 'N');

$pdf->Ln(2);


$style['align'] = 'R';
$pdf->write1DBarcode('RIGHT', 'C128A', '', '', '', 15, 0.4, $style, 'N');

$pdf->Ln(2);


$style['fgcolor'] = array(0,64,127);
$style['position'] = '';
$style['stretch'] = false; 
$style['fitwidth'] = true; 


$style['cellfitalign'] = 'L';
$pdf->write1DBarcode('LEFT', 'C128A', 105, '', 90, 15, 0.4, $style, 'N');

$pdf->Ln(2);


$style['cellfitalign'] = 'C';
$pdf->write1DBarcode('CENTER', 'C128A', 105, '', 90, 15, 0.4, $style, 'N');

$pdf->Ln(2);


$style['cellfitalign'] = 'R';
$pdf->write1DBarcode('RIGHT', 'C128A', 105, '', 90, 15, 0.4, $style, 'N');

$pdf->Ln(2);


$style['fgcolor'] = array(127,0,127);


$style['position'] = 'L';
$pdf->write1DBarcode('LEFT', 'C128A', '', '', '', 15, 0.4, $style, 'N');

$pdf->Ln(2);


$style['position'] = 'C';
$pdf->write1DBarcode('CENTER', 'C128A', '', '', '', 15, 0.4, $style, 'N');

$pdf->Ln(2);


$style['position'] = 'R';
$pdf->write1DBarcode('RIGHT', 'C128A', '', '', '', 15, 0.4, $style, 'N');





$style = array(
	'position' => '',
	'align' => '',
	'stretch' => true,
	'fitwidth' => false,
	'cellfitalign' => '',
	'border' => true,
	'hpadding' => 'auto',
	'vpadding' => 'auto',
	'fgcolor' => array(0,0,128),
	'bgcolor' => array(255,255,128),
	'text' => true,
	'label' => 'CUSTOM LABEL',
	'font' => 'helvetica',
	'fontsize' => 8,
	'stretchtext' => 4
);


$pdf->Cell(0, 0, 'CODE 39 EXTENDED + CHECKSUM', 0, 1);
$pdf->SetLineStyle(array('width' => 1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 0, 0)));
$pdf->write1DBarcode('CODE 39 E+', 'C39E+', '', '', 120, 25, 0.4, $style, 'N');




$pdf->Output('example_027.pdf', 'I');




