<?php
$secret_key = bin2hex(random_bytes(32)); // 256-bit key
echo "Your JWT Secret Key: " . $secret_key;
?>
