<?php
header('Content-type: application/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo $atts['header'];
echo $atts['url'];
echo $atts['footer'];
?>
