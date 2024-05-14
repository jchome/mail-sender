<?php
$prefix = openssl_random_pseudo_bytes(15);
$time = time(); // second
$result = base64_encode($prefix . '-' . $time . '-'. $_SERVER['REMOTE_ADDR']);
print($result);
?>