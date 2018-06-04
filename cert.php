<?php

require_once('inc/conf.inc.php');

header("Content-Type: application/pkix-cert");

echo join('', file('/var/www/kcisd-dc2-ca.cer'));

?>
