<?php
$secret = getenv("WEBHOOK_SECRET");
$path = "/var/www";
$signature = $_SERVER["HTTP_X_HUB_SIGNATURE_256"];

if ($signature) {
  $hash = "sha256=".hash_hmac('sha256', file_get_contents("php://input"), $secret);
  if (hash_equals($signature, $hash)) {
    echo shell_exec("cd {$path} && /usr/bin/git reset --hard origin/master && /usr/bin/git pull 2>&1");
    exit();
  }
}

http_response_code(404);
?>
