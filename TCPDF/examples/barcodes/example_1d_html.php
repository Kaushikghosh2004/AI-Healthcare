<?php



































require_once(dirname(__FILE__).'/tcpdf_barcodes_1d_include.php');


$barcodeobj = new TCPDFBarcode('http://www.tcpdf.org', 'C128');


echo $barcodeobj->getBarcodeHTML(2, 30, 'black');




