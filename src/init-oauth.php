<?php

require_once('/var/config.php');

$discord_url = DISCORD_AUTH;
header("Location: $discord_url");
exit();

?>