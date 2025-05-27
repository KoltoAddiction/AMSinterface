<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../db.php');
include('../update_user.php');

updateUser($pdo);
$bountyStatus = updateUserBounty($pdo);

if(!$_SESSION['logged_in']){
    header('Location: ../error.php');
    exit();
}
extract($_SESSION['userData']);

if(!in_array('732204665319718972', $roles)) {
    header('Location: ../dashboard.php');
    exit();
}

$user_data_db = getUserFromDatabase($pdo,$discord_id);
$db_id = $user_data_db['id'];

$avatar_url = "https://cdn.discordapp.com/avatars/$discord_id/$avatar.png";

setcookie("lastPage", "bounties");

if (isset($_COOKIE['lastPage'])) {
    if ($_COOKIE['lastPage'] != "getInventory") {
        header('Location: getInventory.php');
    }
} else {
    header('Location: bounties.php');
}

$bounties = getBounties($pdo);
$user_bounty = getUserHunting($pdo, $dbID);

$equippedItems = getEquippedItems($pdo, $db_id);
$userStats = calculateStats($_SESSION['user_inventory'], $equippedItems);

$difficultyArray = ["Amateur", "Experienced", "Professional", "Mastery", "Unique", "Maximum"];
$typeArray = ["Elimination", "Flexible", "Retrieval"];

$totalDamage = ($userStats[0] * (1 + ($userStats[1] / 100)));
$totalArmor = ($userStats[2] * (1 + ($userStats[3] / 100)));
$totalResistance = ($userStats[4] * (1 + ($userStats[5] / 100)));

?>
<!DOCTYPE html>
<html class="blackbackground">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
        <link href="../../dist/style.css" rel="stylesheet">
        <link href="../../dist/bounties.css" rel="stylesheet">
        <script type="module" src="bounties.js"></script>
        <link rel="icon" href="../../assets/favicon.png">
        <title>[ams_interface]</title>
    </head>
    <body>

        <div class="screen" id='bountiesScreen'>
            <img src="../../assets/scanlines.png" id="scan" class="noselect">
            <img src="../../assets/bezel.png" id="bezel" class="noselect">
            <div class="content" id="bountiesContent">

                <?php
                    $curBountyID = $user_bounty['bounty_id'];
                    $curBounty = $bounties[$curBountyID];

                    if($curBounty['reward_type'] == 0) {
                        $payout = "[ $" . $curBounty['reward_cash'] . " ]";
                    } else {
                        $itemInfo = getItemInfo($pdo, $curBounty['reward_item']);
                        $payout = '[' . $curBounty[''] . '] ' . $itemInfo['name'];
                    }

                    if($bountyStatus == 3) {
                        echo "
                        <div id='modalOverlay'>
                            <div id='resultModal' class='modal'>
                                <div class='modal-content'>
                                    <h1>bounty successful!</h1>
                                    <h3>payout:</h3>
                                    <h2>$payout</h2>
                                    <h2 id='additionalExclam'>capture successful!</h2>
                                    <h3>additional payout:</h3>
                                    <h2>$payout</h2>
                                    <input type='button' value='> claim' id='claimButton'>
                                </div>
                            </div>
                        </div>
                        ";
                    } else if ($bountyStatus == 2) {
                    echo "
                        <div id='modalOverlay'>
                            <div id='resultModal' class='modal'>
                                <div class='modal-content'>
                                    <h1>bounty successful!</h1>
                                    <h3>payout:</h3>
                                    <h2>$payout</h2>
                                    <input type='button' value='> claim' id='claimButton'>
                                </div>
                            </div>
                        </div>
                        ";
                    } else if ($bountyStatus == 1) {
                    echo "
                        <div id='modalOverlay'>
                            <div id='resultModal' class='modal'>
                                <div class='modal-content'>
                                    <h1>bounty failed</h1>
                                    <h3>bad news...</h3>
                                    <h2>It seems you have a serious case of skill impairment. Try again for better luck!</h2>
                                    <input type='button' value='> continue' id='claimButton'>
                                </div>
                            </div>
                        </div>
                        ";
                    }

                ?>

                <div class="header">
                    <input type="button" value="> back to dashboard" class="backButton" id="bountiesBackButton">
                    <div class="header-right">
                        <img class="avatar" src="<?php echo $avatar_url;?>" />
                        <span class="username"><?php echo $name;?></span>
                        <a href="../logout.php"><img class="logout" src="../../assets/door.png" id="bountiesLogout"></a>
                    </div>
                </div>

                <div class="menu" id="bountiesMenu">
                    <div id="bountyBoard">
                        <h1 id="bountyBoardTitle">available contracts:</h1>
                        <div id="bounties">
                            <?php
                                foreach($bounties as $bounty){
                                    $bountyID = $bounty['id'];

                                    if($bountyID != 0) {
                                        $bountyData = json_encode($bounty, JSON_UNESCAPED_UNICODE);
                                        $safeBountyData = htmlspecialchars($bountyData, ENT_QUOTES, 'UTF-8');
                                        $bountyName = $bounty['name'];
                                        $bountyTag = $bounty['tag'];
                                        $bountyDifficulty = $difficultyArray[$bounty['difficulty']];
                                        $bountyType = $typeArray[$bounty['type']];
                                        $itemRow = getItemInfo($pdo, $bounty['reward_item']);
                                        $itemData = json_encode($itemRow, JSON_UNESCAPED_UNICODE);
                                        $safeItemData = htmlspecialchars($itemData, ENT_QUOTES, 'UTF-8');

                                        echo "
                                        <div class='bountyRow' id='bounty$bountyID' data-json='$safeBountyData' data-itemjson='$safeItemData'>
                                            <h1 class='bountyTag'>&$bountyTag;</h1>
                                            <div class='bountyCol'>
                                                <div class='bountySubRow1'>
                                                    <h2>$bountyName</h2>
                                                </div>
                                                <div class='bountySubRow2'>
                                                    <h2>$bountyDifficulty Bounty</h2>
                                                    <h2>$bountyType</h2>
                                                </div>
                                            </div>
                                        </div>
                                        ";
                                    }
                                    
                                }
                            ?>
                        </div>
                    </div>
                    
                    <div id="bountyInfo">
                        <?php 
                            if($user_bounty['bounty_id'] == 0) {
                                echo "
                                    <div id='noneSelNotice'>
                                        <h1>select a contract to view details.</h1>
                                    </div>
                                    <div id='selBountyInfo' data-tag='selected'>
                                        <span id='selBountyName'>[ unknown ]</span>
                                        <div>
                                            <span id='selBountyTag'>Callsign: unknown</span>
                                            <span id='selBountyType'>MO: unknown</span>
                                        </div>
                                        <div>
                                            <span id='selBountyDiff'>unknown Bounty</span>
                                            <span id='selBountyPayType'>unknown Payout</span>
                                        </div>
                                        <div id='payoutInfo'>
                                            <div>
                                                <h3>cash payout:</h3>
                                                <h2 id='selBountyCash'>[ \$null ]</h2>
                                            </div>
                                            <div>
                                                <h3>item payout:</h3>
                                                <h2 id='selBountyItem'>[ null ]</h2>
                                                <h2 id='selBountyItemQuan'>[ null ]</h2>
                                            </div>
                                        </div>
                                        <h2>description:</h2>
                                        <h3 id='selBountyDesc'>unknown</h3>
                                    </div>
                                ";
                            } else {
                                $curBountyID = $user_bounty['bounty_id'];
                                $curBounty = $bounties[$curBountyID];
                                $curBountyData = json_encode($curBounty, JSON_UNESCAPED_UNICODE);
                                $safeCurBountyData = htmlspecialchars($curBountyData, ENT_QUOTES, 'UTF-8');

                                $itemRow = getItemInfo($pdo, $curBounty['reward_item']);
                                $itemData = json_encode($itemRow, JSON_UNESCAPED_UNICODE);
                                $safeItemData = htmlspecialchars($itemData, ENT_QUOTES, 'UTF-8');

                                echo "
                                    <div id='selBountyInfo' data-tag='current' data-json='$safeCurBountyData' data-itemjson='$safeItemData'>
                                        <span id='currentBounty'>current bounty:</span>
                                        <span id='selBountyName'>[ unknown ]</span>
                                        <div>
                                            <span id='selBountyTag'>Callsign: unknown</span>
                                            <span id='selBountyType'>MO: unknown</span>
                                        </div>
                                        <div>
                                            <span id='selBountyDiff'>unknown Bounty</span>
                                            <span id='selBountyPayType'>unknown Payout</span>
                                        </div>
                                        <div id='payoutInfo'>
                                            <div>
                                                <h3>cash payout:</h3>
                                                <h2 id='selBountyCash'>[ \$null ]</h2>
                                            </div>
                                            <div>
                                                <h3>item payout:</h3>
                                                <h2 id='selBountyItem'>[ null ]</h2>
                                                <h2 id='selBountyItemQuan'>[ null ]</h2>
                                            </div>
                                        </div>
                                        <h2>description:</h2>
                                        <h3 id='selBountyDesc'>unknown</h3>
                                    </div>
                                ";
                            }
                        ?>
                    </div>
                    
                    <div id="confirmationMenu">
                                    <div id='startMenu'>
                                        <div class='gearStatsTable'>
                                            <span>your stats:</span>
                                            <div id='gstColumns'>
                                                <div>
                                                    <span class='gstHeader'>stat</span>
                                                    <span>damage</span>
                                                    <span>armor</span>
                                                    <span>resistance</span>
                                                </div>
                                                <div>
                                                    <span class='gstHeader'>total</span>
                                                    <span id='dmgTotal'><?php echo $totalDamage; ?></span>
                                                    <span id='armTotal'><?php echo $totalArmor; ?></span>
                                                    <span id='resTotal'><?php echo $totalResistance; ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div id='recommendedStats'>
                                            <h3>recommended stats:</h3>
                                            <h2 id='selBountyDmg'>dmg: [ null ]</h2>
                                            <h2 id='selBountyArm'>arm: [ null ]</h2>
                                            <h2 id='selBountyRes'>res: [ null ]</h2>
                                        </div>
                                        <div id='selBountyBody'>
                                            <div class='selBountyBodyCol' id='selBountyStats'>
                                                <h3>base odds:</h3>
                                                <h2 id='selBountyBO'>[ null% ]</h2>
                                                <h3>time required:</h3>
                                                <h2 id='selBountyBT'>[ 00:00:00 ]</h2>
                                                <h3>retrieval odds:</h3>
                                                <h2 id='selBountyBRO'>[ N/A ]</h2>
                                            </div>
                                            <div class='selBountyBodyCol' id='userOdds'>
                                                <h3>your odds:</h3>
                                                <h2 id='selBountyFO'>[ null% ]</h2>
                                                <h3>your time required:</h3>
                                                <h2 id='selBountyFT'>[ 00:00:00 ]</h2>
                                                <h3>your retrieval odds:</h3>
                                                <h2 id='selBountyFRO'>[ N/A ]</h2>
                                            </div>
                                        </div>
                                            <input type='button' id='huntButton' value='> begin hunt'>
                                    </div>
                                    <div id='cancelMenu' data-dmg='<?php echo $user_bounty['dmg']; ?>' data-arm='<?php echo $user_bounty['arm']; ?>' data-res='<?php echo $user_bounty['res']; ?>' data-start_time='<?php echo $user_bounty['start_time']; ?>'>
                                        <h3>remaining time:</h3>
                                        <h2 id='remainingTimer'>[ 00:00:00 ]</h2>
                                        <input type='button' id='cancelButton' value='> cancel hunt'>
                                    </div>
                    </div>
                </div>

                <div class="footer">
                    <div id="copyright" align="center">&copy; <?php echo date('Y'); ?> Echo Asylum Wardens Service - All Rights Reserved.</div>
                </div>
                <div class="reauth" id="bountiesReauth" align="center">> re-authenticate (dev)</div>


            <audio id="hum" src="../../assets/hum.mp3" preload="auto" autoplay loop>Your browser isn't cool enough to join the audio party. You should get on that.</audio>
            <audio id="clicksound" src="../../assets/hover/click.mp3" preload="auto" autoplay></audio>
            <audio id="hoversound" src="../../assets/hover/hover.mp3" preload="auto"></audio>
            
            </div>
        </div>
    </body>
</html>