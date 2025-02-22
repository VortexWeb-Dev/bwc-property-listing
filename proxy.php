<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: image/png");
echo file_get_contents($_GET['url']);
?>
