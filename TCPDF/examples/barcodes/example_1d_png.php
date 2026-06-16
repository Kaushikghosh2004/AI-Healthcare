<?php



































require_once(dirname(__FILE__).'/tcpdf_barcodes_1d_include.php');


$barcodeobj = new TCPDFBarcode('http://www.tcpdf.org', 'C128');


$barcodeobj->getBarcodePNG(2, 30, array(0,0,0));




