<?php



































require_once(dirname(__FILE__).'/tcpdf_barcodes_2d_include.php');


$barcodeobj = new TCPDF2DBarcode('http://www.tcpdf.org', 'QRCODE,H');


echo $barcodeobj->getBarcodeHTML(6, 6, 'black');




