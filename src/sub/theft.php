<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../db.php');
include('../update_user.php');

if(!$_SESSION['logged_in']){
    header('Location: ../error.php');
    exit();
}

updateUser($pdo);
$theftStatus = updateUserTheft($pdo);

extract($_SESSION['userData']);

if(!in_array('694979412268941413', $roles)) {
    header('Location: ../dashboard.php');
    exit();
}

$user_data_db = getUserFromDatabase($pdo,$discord_id);
$db_id = $user_data_db['id'];

$avatar_url = "https://cdn.discordapp.com/avatars/$discord_id/$avatar.png";

setcookie("lastPage", "theft");

$all_users = getAllUsersFromDatabase($pdo);
$user_theft = getUserTheft($pdo, $db_id);
$theftData = json_encode($user_theft, JSON_UNESCAPED_UNICODE);
$safeTheftData = htmlspecialchars($theftData, ENT_QUOTES, 'UTF-8');

?>
<!DOCTYPE html>
<html class="blackbackground">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
        <link href="../../dist/style.css" rel="stylesheet">
        <link href="../../dist/theft.css" rel="stylesheet">
        <script type="module" src="theft.js"></script>
        <link rel="icon" href="../../assets/favicon.png">
        <title>[ams_interface]</title>
    </head>
    <body>
        <div class="screen" id='theftScreen'>
            <img src="../../assets/scanlines.png" id="scan" class="noselect">
            <img src="../../assets/bezel.png" id="bezel" class="noselect">
            <div class="content" id="theftContent">

                <?php
                    if($theftStatus == 1) {
                    $payout = "[ $" . $user_theft['cash_reward'] . " ]";
                    echo "
                       
                        <div id='modalOverlay'>
                            <div id='resultModal' class='modal'>
                                <div class='modal-content'>
                                    <h1>theft successful!</h1>
                                    <h3>payout:</h3>
                                    <h2>$payout</h2>
                                    <input type='button' value='> claim' id='claimButton'>
                                </div>
                            </div>
                        </div>
                        ";

                    } else if ($theftStatus == 0) {
                    echo "
                        <div id='modalOverlay'>
                            <div id='resultModal' class='modal'>
                                <div class='modal-content'>
                                    <h1>theft failed!</h1>
                                    <h3>your attempts have been thwarted.</h3>
                                    <h2>try again later!</h2>
                                    <input type='button' value='> continue' id='claimButton'>
                                </div>
                            </div>
                        </div>
                        ";
                    }

                ?>

                <div class="header">
                    <input type="button" value="> back to dashboard" class="backButton" id="theftBackButton">
                    <div class="header-right">
                        <img class="avatar" src="<?php echo $avatar_url;?>" />
                        <span class="username"><?php echo $name;?></span>
                        <a href="../logout.php"><img class="logout" src="../../assets/door.png" id="theftLogout"></a>
                    </div>
                </div>

                <div class="menu" id="theftMenu">
                    
                    <div id="potentialVictims">
                        <?php
                            foreach($all_users as $user) {
                                $thisUserID = $user['id'];
                                $thisUserDID = $user['discord_id'];
                                $thisUserName = $user['discord_username'];
                                $thisUserAvatar = $user['discord_avatar'];
                                if ($thisUserID == $db_id) {
                                    echo "";
                                } else {
                                    echo "
                                    <div class='userRow' id='user$thisUserID' data-selected='false'>
                                        <img class='userRowImg' src='https://cdn.discordapp.com/avatars/$thisUserDID/$thisUserAvatar.png'>
                                        <h1 class='userRowName'>$thisUserName</h1>
                                    </div>
                                    ";
                                }
                               
                            }
                        ?>
                    </div>
                    <div id="accountSelection">
                        <div class="account" id="checkingSelection" data-selAccount="false">
                            <h1 class="accountTitle">/checking</h1>
                            <h2 class="accountInfo">Checking accounts, while usually more lucrative, are also signficantly more secure than offshore accounts.</h3>
                        </div>
                        <div class="account" id="offshoreSelection" data-selAccount="false">
                            <h1 class="accountTitle">/offshore</h1>
                            <h2 class="accountInfo">Offshore accounts, while much easier to break into, are generally less rewarding.</h3>
                        </div>
                    </div>
                    <div id="theftInfo" data-json="<?php echo $safeTheftData; ?>">
                        <h1 class="bigInfo" id="opMenuTitle">operation menu</h1>
                        <h3 class="smallInfo">/ base success chance</h3>
                        <h2 class="medInfo" id="baseOdds">[ null% ]</h2>
                        <h3 class="smallInfo">/ final success chance</h3>
                        <h2 class="medInfo" id="finalOdds">[ null% ]</h2>
                        <h3 class="smallInfo">/ duration</h3>
                        <h2 class="medInfo" id="operationTime">[ 00:00:00 ]</h2>
                        <input id="stealButton" type="button" value="> begin operation"></input>
                    </div>

                </div>

                <div class="footer">
                    <div id="copyright" align="center">&copy; <?php echo date('Y'); ?> Echo Asylum Wardens Service - All Rights Reserved.</div>
                </div>
                <div class="reauth" id="theftReauth" align="center">> re-authenticate (dev)</div>


            <audio id="hum" src="../../assets/hum.mp3" preload="auto" autoplay loop>Your browser isn't cool enough to join the audio party. You should get on that.</audio>
            <audio id="clicksound" src="../../assets/hover/click.mp3" preload="auto" autoplay></audio>
            <audio id="hoversound" src="../../assets/hover/hover.mp3" preload="auto"></audio>
            
            </div>
        </div>
    </body>
</html>