<?php

$discord_url = "https://discord.com/oauth2/authorize?client_id=574982700511789067&response_type=code&redirect_uri=http%3A%2F%2Flocalhost%2FAMSinterface%2Fsrc%2Fprocess_oauth.php&scope=identify+guilds+guilds.members.read";
header("Location: $discord_url");
exit();

?>