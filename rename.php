<?php
$dir = new RecursiveDirectoryIterator('C:\xamppp\htdocs\Hospital-Management-System');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/\.(php|html|md)$/');

foreach($files as $file) {
    $path = $file->getPathname();
    $content = file_get_contents($path);
    if($content === false) continue;
    $orig = $content;
    
    
    $content = str_ireplace('Ritsy Vitalss', 'Ritsy Vitals', $content);
    $content = str_ireplace('Ritsy Vitals', 'Ritsy Vitals', $content);
    $content = str_ireplace('Ritsy Vitals', 'Ritsy Vitals', $content);
    $content = str_ireplace('Ritsy Vitals Clinical', 'Ritsy Vitals', $content);
    $content = str_ireplace('Ritsy Vitals', 'Ritsy Vitals', $content);
    
    
    $content = preg_replace('/Ritsy Vitals(.*?<span[^>]*>)Health/i', 'Ritsy$1Vitals', $content);
    $content = preg_replace('/Ritsy Vitals(.*?<span[^>]*>)Clinical/i', 'Ritsy$1Vitals', $content);
    $content = preg_replace('/Ritsy Vitals(.*?<span[^>]*>)Admin/i', 'Ritsy$1Admin', $content);
    
    
    $content = str_ireplace('Ritsy Vitals@Ritsy Vitals.org', 'support@Ritsy.org', $content);
    $content = str_ireplace('support@Ritsy Vitals.org', 'support@Ritsy.org', $content);
    $content = str_ireplace('@Ritsy Vitals.org', '@Ritsy.org', $content);
    $content = str_replace('Ritsy Vitals', 'Ritsy', $content);
    $content = str_replace('Ritsy Vitals', 'Ritsy', $content);
    $content = str_replace('Ritsy Vitals', 'Ritsy', $content);
    
    if($orig !== $content) {
        file_put_contents($path, $content);
        echo "Updated $path\n";
    }
}
echo "Done replacing.";
?>
