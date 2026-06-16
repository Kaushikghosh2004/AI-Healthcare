<?php



















$tcpdf_barcodes_1d_include_dirs = array(realpath('../../tcpdf_barcodes_1d.php'), '/usr/share/php/tcpdf/tcpdf_barcodes_1d.php', '/usr/share/tcpdf/tcpdf_barcodes_1d.php', '/usr/share/php-tcpdf/tcpdf_barcodes_1d.php', '/var/www/tcpdf/tcpdf_barcodes_1d.php', '/var/www/html/tcpdf/tcpdf_barcodes_1d.php', '/usr/local/apache2/htdocs/tcpdf/tcpdf_barcodes_1d.php');
foreach ($tcpdf_barcodes_1d_include_dirs as $tcpdf_barcodes_1d_include_path) {
	if (@file_exists($tcpdf_barcodes_1d_include_path)) {
		require_once($tcpdf_barcodes_1d_include_path);
		break;
	}
}




