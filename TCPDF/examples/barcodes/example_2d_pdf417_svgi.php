<?php



































require_once(dirname(__FILE__).'/tcpdf_barcodes_2d_include.php');


$barcodeobj = new TCPDF2DBarcode('http://www.tcpdf.org', 'PDF417');


echo $barcodeobj->getBarcodeSVGcode(4, 4, 'black');




