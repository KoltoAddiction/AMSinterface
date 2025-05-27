<?php

include('../db.php');
include('../update_user.php');

updateUser($pdo);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!$_SESSION['logged_in']){
    header('Location: ../error.php');
    exit();
}
extract($_SESSION['userData']);

$avatar_url = "https://cdn.discordapp.com/avatars/$discord_id/$avatar.png";

setcookie("lastPage", "accounts");

$user_data_db = getUserFromDatabase($pdo,$discord_id);
$db_id = $user_data_db['id'];
$balChecking = $user_data_db['balChecking'];
$balSavings = $user_data_db['balSavings'];
$balOffshore = $user_data_db['balOffshore'];
$balHighYield = $user_data_db['balHighYield'];
$lastHYW = strtotime($user_data_db['lastHYW']);
$nextHYW = $lastHYW + 604800;
$dateNextHYW = new DateTime("@$nextHYW");

$allowedAccounts = 0;
if(in_array('814236595389595648', $roles)) { // Check if player has access to High-Yield account
    $allowedAccounts = 2;
} else if(in_array('694979412268941413', $roles)) { // If player does not have HY, check if they have access to Offshore account
    $allowedAccounts = 1;
}

?>
<!DOCTYPE html>
<html class="blackbackground">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
        <link href="../../dist/style.css" rel="stylesheet">
        <link href="../../dist/accounts.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script src="accounts.js"></script>
        <link rel="icon" href="../../assets/favicon.png">
        <title>[ams_interface]</title>
    </head>
    <body>

    <div id="lastHYW" style="display: none;">
        <?php
            echo htmlspecialchars($lastHYW);
        ?>
    </div>
    <div id="balSavings" style="display: none;">
        <?php
            echo htmlspecialchars($balSavings);
        ?>
    </div>
    <div id="allowedAccounts" style="display: none;">
        <?php
            echo htmlspecialchars($allowedAccounts);
        ?>
    </div>

        <div class="screen" id='accountsScreen'>
            <img src="../../assets/scanlines.png" id="scan" class="noselect">
            <img src="../../assets/bezel.png" id="bezel" class="noselect">
            <div class="content" id="accountsContent">
                <div class="header">
                    <input type="button" value="> back to dashboard" class="backButton" id="accountsBackButton">
                    <div class="header-right">
                        <img class="avatar" src="<?php echo $avatar_url;?>" />
                        <span class="username"><?php echo $name;?></span>
                        <a href="../logout.php"><img class="logout" src="../../assets/door.png" id="accountsLogout"></a>
                    </div>
                </div>

                <div class="menu">

                    <div id="transferPopup" class="modal">
                        <div class="modal-content">
                            <span id="closeTransferPopup" class="closeModal">&times;</span>
                            <h2 class="popupTitle">transfer funds</h2>
                                <input type="text" placeholder="transfer amount" id="transferAmount" name="transferAmount">
                                <div id="accountRadio">
                                    <label><input type="radio" class="accountOption" value="balChecking" name="accountChosen" checked id="checkingRadio"><span>checking</span></label><br>
                                    <label><input type="radio" class="accountOption" value="balSavings" name="accountChosen"  id="savingsRadio"><span>savings</span></label><br>
                                    <label id="chooseOffshore"><input type="radio" class="accountOption" value="balOffshore" name="accountChosen"  id="offshoreRadio"><span>offshore</span></label><br>
                                    <label id="chooseHighYield"><input type="radio" class="accountOption" value="balHighYield" name="accountChosen"  id="highyieldRadio"><span>high-yield</span></label><br>
                                </div>
                                <input id="submitTransfer" type="button" value="> process">
                        </div>
                    </div>

                    <div id="highYieldErrorPopup" class="modal">
                        <div class="modal-content">
                        <span id="closeHYEP" class="closeModal">&times;</span>
                        <h2 class="popupTitle">error!</h2>
                        <h3 class="errorMessage">withdrawals from your high-yield account have a one week cooldown.<br> your last withdrawal was at <?php echo $user_data_db['lastHYW']?>.<br> you can next withdraw at <?php echo $dateNextHYW->format('Y-m-d H:i:s');?>.</h3>
                        </div>
                    </div>

                    <div id="savingsErrorPopup" class="modal">
                        <div class="modal-content">
                        <span id="closeSE" class="closeModal">&times;</span>
                        <h2 class="popupTitle">error!</h2>
                        <h3 class="errorMessage">this transfer would cause you to exceed your savings account limit.<br> you cannot have more than 100,000,000 (100M) in your savings account at any time.</h3>
                        </div>
                    </div>

                    <div class="account">
                        <h1>/checking</h1>
                        <h2 class="balStatement">bal:</h2>
                        <h2 class="balance">[ $<?php echo $balChecking; ?> ]</h2>
                        <input id="checkingTransferBut" type="button" value="> transfer" class="transferButton">
                    </div>
                    <div class="account">
                        <h1>/savings</h1>
                        <h2 class="balStatement">bal:</h2>
                        <h2 class="balance">[ $<?php echo $balSavings; ?> ]</h2>
                        <input id="savingsTransferBut" type="button" value="> transfer" class="transferButton">
                    </div>
                    <div class="account">
                        <h1 id="offshoreAccountTitle">/offshore</h1>
                        <h2 class="balStatement" id="ObalStatement">bal:</h2>
                        <h2 class="balance" id="Obal">[ $<?php echo $balOffshore; ?> ]</h2>
                        <input id="offshoreTransferBut" type="button" value="> transfer" class="transferButton">
                    </div>
                    <div class="account" id="highYieldAccount">
                        <h1 id=highYieldAccountTitle>/high-yield</h1>
                        <h2 class="balStatement" id="HYbalStatement">bal:</h2>
                        <h2 class="balance" id="HYbal">[ $<?php echo $balHighYield; ?> ]</h2>
                        <input id="highyieldTransferBut" type="button" value="> transfer" class="transferButton">
                    </div>
                </div>

                <div class="footer">
                    <div id="copyright" align="center">&copy; <?php echo date('Y'); ?> Echo Asylum Wardens Service - All Rights Reserved.</div>
                </div>
                <div class="reauth" id="accountsReauth" align="center">> re-authenticate (dev)</div>


            <audio id="hum" src="../../assets/hum.mp3" preload="auto" autoplay loop>Your browser isn't cool enough to join the audio party. You should get on that.</audio>
            <audio id="clicksound" src="../../assets/hover/click.mp3" preload="auto" autoplay></audio>
            <audio id="hoversound" src="../../assets/hover/hover.mp3" preload="auto"></audio>
            </div>
        </div>
    </body>
</html>