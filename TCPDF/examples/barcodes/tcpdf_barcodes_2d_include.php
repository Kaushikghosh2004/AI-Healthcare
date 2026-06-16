<?php



















$tcpdf_barcodes_2d_include_dirs = array(realpath('../../tcpdf_barcodes_2d.php'), '/usr/share/php/tcpdf/tcpdf_barcodes_2d.php', '/usr/share/tcpdf/tcpdf_barcodes_2d.php', '/usr/share/php-tcpdf/tcpdf_barcodes_2d.php', '/var/www/tcpdf/tcpdf_barcodes_2d.php', '/var/www/html/tcpdf/tcpdf_barcodes_2d.php', '/usr/local/apache2/htdocs/tcpdf/tcpdf_barcodes_2d.php');
foreach ($tcpdf_barcodes_2d_include_dirs as $tcpdf_barcodes_2d_include_path) {
	if (@file_exists($tcpdf_barcodes_2d_include_path)) {
		require_once($tcpdf_barcodes_2d_include_path);
		break;
	}
}




