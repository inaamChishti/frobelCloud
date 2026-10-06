<?php
$base = dirname(__DIR__);
@unlink($base.'/bootstrap/cache/config.php');
@unlink($base.'/bootstrap/cache/routes-v7.php');
echo "Cache cleared!";
